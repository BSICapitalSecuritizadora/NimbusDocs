<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Portal;

use App\Infrastructure\Persistence\MySqlPortalSubmissionRepository;
use App\Infrastructure\Persistence\MySqlPortalSubmissionFileRepository;
use App\Support\Csrf;
use App\Support\Session;
use App\Support\RandomToken;
use Respect\Validation\Validator as v;

final class PortalSubmissionController
{
    private MySqlPortalSubmissionRepository $repo;
    private MySqlPortalSubmissionFileRepository $fileRepo;

    public function __construct(private array $config)
    {
        $this->repo     = new MySqlPortalSubmissionRepository($config['pdo']);
        $this->fileRepo = new MySqlPortalSubmissionFileRepository($config['pdo']);
    }

    private function requireUser(): array
    {
        $user = Session::get('portal_user');
        if (!$user) {
            http_response_code(403);
            echo '403 - Acesso não autorizado ao portal.';
            exit;
        }
        return $user;
    }

    public function index(array $vars = []): void
    {
        $user = $this->requireUser();

        $page    = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = 10;

        $pagination = $this->repo->paginateByUser((int)$user['id'], $page, $perPage);

        $pageTitle   = 'Minhas submissões';
        $contentView = __DIR__ . '/../../View/portal/submissions/index.php';
        $viewData    = [
            'pagination' => $pagination,
            'flash'      => [
                'success' => Session::getFlash('success'),
                'error'   => Session::getFlash('error'),
            ],
        ];

        require __DIR__ . '/../../View/portal/layouts/base.php';
    }

    public function showCreateForm(array $vars = []): void
    {
        $this->requireUser();

        $pageTitle   = 'Nova submissão';
        $contentView = __DIR__ . '/../../View/portal/submissions/create.php';
        $viewData    = [
            'csrfToken' => Csrf::token(),
            'errors'    => Session::getFlash('errors') ?? [],
            'old'       => Session::getFlash('old') ?? [],
        ];

        require __DIR__ . '/../../View/portal/layouts/base.php';
    }

    public function store(array $vars = []): void
    {
        $user  = $this->requireUser();
        $post  = $_POST;
        $token = $post['_token'] ?? '';

        if (!Csrf::validate($token)) {
            Session::flash('error', 'Sessão expirada. Tente novamente.');
            $this->redirect('/portal/submissions/create');
        }

        $data = [
            'title'   => trim($post['title'] ?? ''),
            'message' => trim($post['message'] ?? ''),
        ];

        $errors = [];

        if (!v::stringType()->length(3, 190)->validate($data['title'])) {
            $errors['title'] = 'Título deve ter pelo menos 3 caracteres.';
        }

        if ($errors) {
            Session::flash('errors', $errors);
            Session::flash('old', $data);
            $this->redirect('/portal/submissions/create');
        }

        // Gera um código de referência amigável
        $refCode = sprintf(
            'SUB-%s-%s',
            date('Ymd'),
            substr(RandomToken::shortCode(8), 0, 8)
        );

        $ip = $_SERVER['REMOTE_ADDR']      ?? '';
        $ua = $_SERVER['HTTP_USER_AGENT']  ?? '';

        $submissionId = $this->repo->createForUser((int)$user['id'], [
            'reference_code'      => $refCode,
            'title'               => $data['title'],
            'message'             => $data['message'],
            'status'              => 'PENDING',
            'created_ip'          => $ip,
            'created_user_agent'  => $ua,
        ]);

        Session::flash('success', 'Submissão enviada com sucesso.');
        $this->redirect('/portal/submissions/' . $submissionId);
    }

    public function show(array $vars = []): void
    {
        $user = $this->requireUser();
        $id   = (int)($vars['id'] ?? 0);

        $submission = $this->repo->findByIdForUser($id, (int)$user['id']);

        if (!$submission) {
            http_response_code(404);
            echo 'Submissão não encontrada.';
            return;
        }

        $files = $this->fileRepo->findBySubmission($id); // por enquanto só exibir (upload depois)

        $pageTitle   = 'Detalhes da submissão';
        $contentView = __DIR__ . '/../../View/portal/submissions/show.php';
        $viewData    = [
            'submission' => $submission,
            'files'      => $files,
        ];

        require __DIR__ . '/../../View/portal/layouts/base.php';
    }

    private function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }
}
