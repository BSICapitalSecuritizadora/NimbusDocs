<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Admin;

use App\Infrastructure\Persistence\MySqlPortalSubmissionRepository;
use App\Infrastructure\Persistence\MySqlPortalSubmissionFileRepository;
use App\Support\Csrf;
use App\Support\Session;

final class SubmissionAdminController
{
    private MySqlPortalSubmissionRepository $repo;
    private MySqlPortalSubmissionFileRepository $fileRepo;

    public function __construct(private array $config)
    {
        $this->repo     = new MySqlPortalSubmissionRepository($config['pdo']);
        $this->fileRepo = new MySqlPortalSubmissionFileRepository($config['pdo']);
    }

    private function requireAdmin(): void
    {
        $admin = Session::get('admin');
        if (!$admin) {
            http_response_code(403);
            echo '403 - Não autorizado.';
            exit;
        }
    }

    public function index(array $vars = []): void
    {
        $this->requireAdmin();

        $page    = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = 15;

        $filters = [
            'status'         => $_GET['status']         ?? null,
            'portal_user_id' => $_GET['portal_user_id'] ?? null,
        ];

        $pagination = $this->repo->paginateAll($filters, $page, $perPage);

        $pageTitle   = 'Submissões do Portal';
        $contentView = __DIR__ . '/../../View/admin/submissions/index.php';
        $viewData    = [
            'pagination' => $pagination,
            'filters'    => $filters,
            'csrfToken'  => Csrf::token(),
            'flash'      => [
                'success' => Session::getFlash('success'),
                'error'   => Session::getFlash('error'),
            ],
        ];

        require __DIR__ . '/../../View/admin/layouts/base.php';
    }

    public function show(array $vars = []): void
    {
        $this->requireAdmin();

        $id         = (int)($vars['id'] ?? 0);
        $submission = $this->repo->findWithUserById($id);

        if (!$submission) {
            Session::flash('error', 'Submissão não encontrada.');
            $this->redirect('/admin/submissions');
        }

        $files = $this->fileRepo->findBySubmission($id);

        $pageTitle   = 'Detalhes da Submissão';
        $contentView = __DIR__ . '/../../View/admin/submissions/show.php';
        $viewData    = [
            'submission' => $submission,
            'files'      => $files,
        ];

        require __DIR__ . '/../../View/admin/layouts/base.php';
    }

    private function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }
}
