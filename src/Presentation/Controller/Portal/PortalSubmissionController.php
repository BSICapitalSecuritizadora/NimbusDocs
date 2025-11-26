<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Portal;

use App\Infrastructure\Persistence\MySqlPortalSubmissionRepository;
use App\Infrastructure\Persistence\MySqlPortalSubmissionFileRepository;
use App\Infrastructure\Persistence\MySqlPortalSubmissionNoteRepository;
use App\Support\Csrf;
use App\Support\AuditLogger;
use App\Support\Session;
use App\Support\RandomToken;
use Respect\Validation\Validator as v;

final class PortalSubmissionController
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

        // --- validação básica dos anexos ---
        $filesArray = $_FILES['attachments'] ?? null;
        $hasFiles   = $filesArray && isset($filesArray['name']) && is_array($filesArray['name']);

        $maxSize = (int)($this->config['upload_max_file_size'] ?? $this->config['upload']['max_file_size'] ?? 104857600);

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
        ];

        if ($hasFiles) {
            $count = count($filesArray['name']);
            for ($i = 0; $i < $count; $i++) {
                $error = $filesArray['error'][$i];
                if ($error === UPLOAD_ERR_NO_FILE) {
                    continue;
                }
                if ($error !== UPLOAD_ERR_OK) {
                    $errors['attachments'] = 'Erro ao enviar um ou mais arquivos.';
                    break;
                }

                $size = (int)$filesArray['size'][$i];
                if ($size > $maxSize) {
                    $errors['attachments'] = 'Um dos arquivos excede o tamanho máximo permitido.';
                    break;
                }

                $type = $filesArray['type'][$i] ?? '';
                if ($type && !in_array($type, $allowedMimes, true)) {
                    $errors['attachments'] = 'Um dos arquivos possui tipo não permitido.';
                    break;
                }
            }
        }

        if ($errors) {
            Session::flash('errors', $errors);
            Session::flash('old', $data);
            $this->redirect('/portal/submissions/create');
        }

        // --- cria submissão ---
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

        // --- salva anexos fisicamente + na tabela ---
        if ($hasFiles) {
            $this->saveUploadedFiles($submissionId, $filesArray, $maxSize);
        }

        $this->audit->log('PORTAL_USER', (int)$user['id'], 'SUBMISSION_CREATED', 'PORTAL_SUBMISSION', $submissionId, [
            'reference_code' => $refCode,
        ]);

        // --- e-mail de confirmação (se serviço estiver configurado) ---
        if (isset($this->config['mail'])) {
            $emailUsuario = $user['email'] ?? null;

            if ($emailUsuario) {
                $nomeUsuario  = $user['full_name'] ?? $user['name'] ?? 'Cliente';
                $titulo       = $data['title'];
                $linkSubmissao = rtrim($this->config['app_url'], '/') . '/portal/submissions/' . $submissionId;

                $body = sprintf(
                    '<p>Olá %s,</p>
                    <p>Sua submissão <strong>%s</strong> foi recebida com sucesso.</p>
                    <p>Você pode acompanhar o status em:
                    <a href="%s">clique aqui</a></p>',
                    htmlspecialchars($nomeUsuario, ENT_QUOTES, 'UTF-8'),
                    htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8'),
                    htmlspecialchars($linkSubmissao, ENT_QUOTES, 'UTF-8')
                );

                $this->config['mail']->sendMail(
                    $emailUsuario,
                    'Recebemos sua submissão',
                    $body
                );
            }
        }

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

        $files = $this->fileRepo->findBySubmission($id);
        $notes = $this->noteRepo->listVisibleForSubmission($id);

        $pageTitle   = 'Detalhes da submissão';
        $contentView = __DIR__ . '/../../View/portal/submissions/show.php';
        $viewData    = [
            'submission' => $submission,
            'files'      => $files,
            'notes'      => $notes,
        ];

        require __DIR__ . '/../../View/portal/layouts/base.php';
    }

    private function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }

    private function saveUploadedFiles(int $submissionId, array $filesArray, int $maxSize): void
    {
        $uploadDir = rtrim($this->config['upload_dir'] ?? $this->config['upload']['dir'] ?? dirname(__DIR__, 5) . '/storage/uploads', '/');

        $baseDir = $uploadDir . '/' . date('Y') . '/' . date('m');
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
                continue; // já validamos antes; aqui só pulamos
            }

            $tmpName      = $filesArray['tmp_name'][$i];
            $originalName = $filesArray['name'][$i];
            $size         = (int)$filesArray['size'][$i];
            $mime         = $filesArray['type'][$i] ?? 'application/octet-stream';

            if (!is_uploaded_file($tmpName) || $size <= 0 || $size > $maxSize) {
                continue;
            }

            $ext = pathinfo($originalName, PATHINFO_EXTENSION);
            $storedName = bin2hex(random_bytes(16)) . ($ext ? ('.' . strtolower($ext)) : '');

            $relativePath = date('Y') . '/' . date('m') . '/' . $storedName;
            $fullPath     = $uploadDir . '/' . $relativePath;

            $dir = dirname($fullPath);
            if (!is_dir($dir)) {
                mkdir($dir, 0775, true);
            }

            if (!move_uploaded_file($tmpName, $fullPath)) {
                continue;
            }

            $checksum = hash_file('sha256', $fullPath);

            $this->fileRepo->create($submissionId, [
                'original_name' => $originalName,
                'stored_name'   => $storedName,
                'mime_type'     => $mime,
                'size_bytes'    => $size,
                'storage_path'  => $relativePath,
                'checksum'      => $checksum,
            ]);
        }
    }
}
