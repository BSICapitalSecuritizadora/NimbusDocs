<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Admin;

use App\Infrastructure\Persistence\MySqlPortalSubmissionRepository;
use App\Infrastructure\Persistence\MySqlPortalSubmissionFileRepository;
use App\Support\Csrf;
use App\Support\AuditLogger;
use App\Support\Session;
use App\Infrastructure\Persistence\MySqlPortalSubmissionNoteRepository;
use Respect\Validation\Validator as v;

final class SubmissionAdminController
{
    private MySqlPortalSubmissionRepository $repo;
    private MySqlPortalSubmissionFileRepository $fileRepo;
    private MySqlPortalSubmissionNoteRepository $noteRepo;
    private AuditLogger $audit;

    public function __construct(private array $config)
    {
        $this->repo     = new MySqlPortalSubmissionRepository($config['pdo']);
        $this->fileRepo = new MySqlPortalSubmissionFileRepository($config['pdo']);
        $this->noteRepo = new MySqlPortalSubmissionNoteRepository($config['pdo']);
        $this->audit    = new AuditLogger($config['pdo']);
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
        $notes = $this->noteRepo->listAllForSubmission($id);

        $pageTitle   = 'Detalhes da Submissão';
        $contentView = __DIR__ . '/../../View/admin/submissions/show.php';
        $viewData    = [
            'submission' => $submission,
            'files'      => $files,
            'notes'      => $notes,
            'csrfToken'  => Csrf::token(),
        ];

        require __DIR__ . '/../../View/admin/layouts/base.php';
    }

    private function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }

    public function updateStatus(array $vars = []): void
    {
        $this->requireAdmin();

        $id     = (int)($vars['id'] ?? 0);
        $post   = $_POST;
        $token  = $post['_token'] ?? '';

        $submission = $this->repo->findWithUserById($id);
        if (!$submission) {
            Session::flash('error', 'Submissão não encontrada.');
            $this->redirect('/admin/submissions');
        }

        if (!Csrf::validate($token)) {
            Session::flash('error', 'Sessão expirada. Tente novamente.');
            $this->redirect('/admin/submissions/' . $id);
        }

        $status   = $post['status'] ?? '';
        $noteText = trim($post['note'] ?? '');
        $visible  = $post['visibility'] ?? 'USER_VISIBLE';

        $allowedStatus = ['PENDING', 'UNDER_REVIEW', 'COMPLETED', 'REJECTED'];
        if (!in_array($status, $allowedStatus, true)) {
            Session::flash('error', 'Status inválido.');
            $this->redirect('/admin/submissions/' . $id);
        }

        if (!in_array($visible, ['ADMIN_ONLY', 'USER_VISIBLE'], true)) {
            $visible = 'USER_VISIBLE';
        }

        // Atualiza status
        $admin = Session::get('admin');
        $adminId = $admin['id'] ?? null;

        $this->repo->updateStatus($id, $status, $adminId ? (int)$adminId : null);

        // Cria nota (opcional, mas quase sempre haverá algo)
        if ($noteText !== '') {
            $this->noteRepo->create([
                'submission_id' => $id,
                'admin_user_id' => $adminId ? (int)$adminId : null,
                'visibility'    => $visible,
                'message'       => $noteText,
            ]);
        }

        $this->audit->log('ADMIN', $adminId ? (int)$adminId : null, 'SUBMISSION_STATUS_UPDATED', 'PORTAL_SUBMISSION', $id, [
            'status' => $status,
            'note_visibility' => $visible,
        ]);

        // Envia e-mail se serviço estiver disponível e tivermos e-mail do usuário
        if (isset($this->config['mail']) && !empty($submission['user_email'])) {
            $linkSubmissao = rtrim($this->config['app_url'], '/') . '/portal/submissions/' . $id;
            $nomeUsuario   = $submission['user_full_name'] ?? 'Cliente';

            $body = sprintf(
                '<p>Olá %s,</p>
                <p>O status da sua submissão <strong>%s</strong> foi atualizado para:
                <strong>%s</strong>.</p>%s
                <p><a href="%s">Ver detalhes</a></p>',
                htmlspecialchars($nomeUsuario, ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($submission['title'], ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($status, ENT_QUOTES, 'UTF-8'),
                $noteText !== ''
                    ? '<p>Comentário do analista:</p><blockquote>' . nl2br(htmlspecialchars($noteText, ENT_QUOTES, 'UTF-8')) . '</blockquote>'
                    : '',
                htmlspecialchars($linkSubmissao, ENT_QUOTES, 'UTF-8')
            );

            $this->config['mail']->sendMail(
                $submission['user_email'],
                'Status atualizado – ' . $submission['title'],
                $body
            );
        }

        Session::flash('success', 'Status atualizado com sucesso.');
        $this->redirect('/admin/submissions/' . $id);
    }
}
