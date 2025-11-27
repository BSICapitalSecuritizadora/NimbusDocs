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
        $responseFiles = array_filter($files, static fn($f) => $f['origin'] === 'ADMIN');
        $userFiles     = array_filter($files, static fn($f) => $f['origin'] === 'USER');

        $pageTitle   = 'Detalhes da Submissão';
        $contentView = __DIR__ . '/../../View/admin/submissions/show.php';
        $viewData = [
            'submission'    => $submission,
            'files'         => $files,
            'userFiles'     => $userFiles,
            'responseFiles' => $responseFiles,
            'notes'         => $notes,
            'csrfToken'     => Csrf::token(),
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

        $this->config['audit']->log(
            $adminId ? (int)$adminId : null,
            'submission.status.updated',
            'submission',
            $id,
            [
                'new_status' => $status,
                'note'       => $noteText,
            ]
        );

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
            $baseUrl = $this->config['app']['url']
                ?? ($this->config['app_url'] ?? null);
            if (!$baseUrl) {
                $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
                $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
                $baseUrl = $scheme . '://' . $host;
            }
            $linkSubmissao = rtrim((string)$baseUrl, '/') . '/portal/submissions/' . $id;
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

    public function uploadResponseFiles(array $vars = []): void
    {
        $this->requireAdmin();

        $id    = (int)($vars['id'] ?? 0);
        $post  = $_POST;
        $token = $post['_token'] ?? '';

        if (!Csrf::validate($token)) {
            Session::flash('error', 'Sessão expirada. Tente novamente.');
            $this->redirect('/admin/submissions/' . $id);
        }

        $filesArray = $_FILES['response_files'] ?? null;
        if (!$filesArray || !isset($filesArray['name']) || !is_array($filesArray['name'])) {
            Session::flash('error', 'Nenhum arquivo enviado.');
            $this->redirect('/admin/submissions/' . $id);
        }

        $maxSize = (int)($this->config['upload']['max_filesize_bytes'] ?? 104857600);

        $allowedMimes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'text/plain',
            'text/csv',
            'image/jpeg',
            'image/png',
            'application/zip',
        ];

        $uploadDir = rtrim((string)($this->config['upload']['storage_path'] ?? ''), '/');
        if ($uploadDir === '') {
            // fallback para storage/uploads relativo ao projeto
            $uploadDir = rtrim(dirname(__DIR__, 4) . '/storage/uploads', '/');
        }
        $baseDir   = $uploadDir . '/' . date('Y') . '/' . date('m');

        if (!is_dir($baseDir)) {
            mkdir($baseDir, 0775, true);
        }

        $count = count($filesArray['name']);

        for ($i = 0; $i < $count; $i++) {
            $error = $filesArray['error'][$i];
            if ($error === UPLOAD_ERR_NO_FILE) {
                continue;
            }
            if ($error !== UPLOAD_ERR_OK) {
                continue;
            }

            $size = (int)$filesArray['size'][$i];
            if ($size <= 0 || $size > $maxSize) {
                continue;
            }

            $tmpName      = $filesArray['tmp_name'][$i];
            $originalName = $filesArray['name'][$i];
            $mime         = $filesArray['type'][$i] ?? 'application/octet-stream';

            if (!in_array($mime, $allowedMimes, true)) {
                continue;
            }

            if (!is_uploaded_file($tmpName)) {
                continue;
            }

            $ext        = pathinfo($originalName, PATHINFO_EXTENSION);
            $storedName = bin2hex(random_bytes(16)) . ($ext ? '.' . strtolower($ext) : '');
            $relative   = date('Y') . '/' . date('m') . '/' . $storedName;
            $fullPath   = $uploadDir . '/' . $relative;

            $dir = dirname($fullPath);
            if (!is_dir($dir)) {
                mkdir($dir, 0775, true);
            }

            if (!move_uploaded_file($tmpName, $fullPath)) {
                continue;
            }

            $checksum = hash_file('sha256', $fullPath);

            $this->fileRepo->create($id, [
                'origin'          => 'ADMIN',
                'original_name'   => $originalName,
                'stored_name'     => $storedName,
                'mime_type'       => $mime,
                'size_bytes'      => $size,
                'storage_path'    => $relative,
                'checksum'        => $checksum,
                'visible_to_user' => 1,  // já sobe visível para o usuário
            ]);
        }

        $this->config['audit']->log(
            $adminId ?? null, // se você já tem o admin em sessão; se não, recupera como nos outros métodos
            'submission.response_files.uploaded',
            'submission',
            $id,
            [
                'files_count' => $count,
            ]
        );

        Session::flash('success', 'Documentos enviados para o usuário.');
        $this->redirect('/admin/submissions/' . $id);
    }
}
