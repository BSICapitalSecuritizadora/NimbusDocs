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
use App\Support\Auth;
use App\Support\FileUpload;
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

    

    public function index(array $vars = []): void
    {
        $user = Auth::requirePortalUser();

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
        Auth::requirePortalUser();

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
        $user  = Auth::requirePortalUser();
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

        // --- salva anexos usando helper FileUpload + grava no banco ---
        if ($hasFiles) {
            $userId = (int)$user['id'];
            $storageBase = dirname(__DIR__, 5) . '/storage/portal_uploads/' . $userId . '/';

            $count = count($filesArray['name']);
            for ($i = 0; $i < $count; $i++) {
                if (($filesArray['error'][$i] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
                    continue;
                }

                $fileInfo = [
                    'name'     => $filesArray['name'][$i],
                    'type'     => $filesArray['type'][$i] ?? '',
                    'tmp_name' => $filesArray['tmp_name'][$i],
                    'error'    => $filesArray['error'][$i],
                    'size'     => (int)$filesArray['size'][$i],
                ];

                try {
                    $stored = FileUpload::store($fileInfo, $storageBase);

                    $storedName   = basename($stored['path']);
                    $checksum     = is_file($stored['path']) ? hash_file('sha256', $stored['path']) : null;
                    $relativePath = 'portal_uploads/' . $userId . '/' . $storedName;

                    $this->fileRepo->create($submissionId, [
                        'origin'         => 'USER',
                        'original_name'  => $stored['original_name'],
                        'stored_name'    => $storedName,
                        'mime_type'      => $stored['mime_type'],
                        'size_bytes'     => (int)$stored['size'],
                        'storage_path'   => $relativePath,
                        'checksum'       => $checksum,
                        'visible_to_user'=> 0,
                    ]);
                } catch (\Throwable $e) {
                    $this->audit->log('PORTAL_USER', (int)$user['id'], 'USER_FILE_UPLOAD_FAILED', 'PORTAL_SUBMISSION', $submissionId, [
                        'error' => $e->getMessage(),
                        'file'  => $fileInfo['name'] ?? null,
                    ]);
                    // segue para o próximo arquivo
                }
            }
        }

        $this->audit->log('PORTAL_USER', (int)$user['id'], 'SUBMISSION_CREATED', 'PORTAL_SUBMISSION', $submissionId, [
            'reference_code' => $refCode,
        ]);

        $this->config['audit']->portalUserAction([
            'actor_id'    => (int)$user['id'],
            'actor_name'  => $user['full_name'] ?? $user['name'] ?? $user['email'],
            'action'      => 'PORTAL_SUBMISSION_CREATED',
            'summary'     => 'Nova submissão criada pelo usuário do portal.',
            'context_type' => 'submission',
            'context_id'  => $submissionId,
            'details'     => [
                'title'   => $data['title'],
                'has_files' => $hasFiles,
            ],
        ]);

        // --- e-mail de confirmação (se serviço estiver configurado) ---
        if (isset($this->config['mail'])) {
            $emailUsuario = $user['email'] ?? null;

            if ($emailUsuario) {
                $nomeUsuario  = $user['full_name'] ?? $user['name'] ?? 'Cliente';
                $titulo       = $data['title'];
                // Determina a base URL a partir da config moderna (app.url),
                // mantendo compatibilidade com uma possível chave legada (app_url).
                $baseUrl = $this->config['app']['url']
                    ?? ($this->config['app_url'] ?? null);
                if (!$baseUrl) {
                    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
                    $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
                    $baseUrl = $scheme . '://' . $host;
                }
                $linkSubmissao = rtrim((string)$baseUrl, '/') . '/portal/submissions/' . $submissionId;

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
        $user = Auth::requirePortalUser();
        $userId = (int)$user['id'];
        $id = (int)($vars['id'] ?? 0);

        $submission = $this->repo->findForUser($id, $userId);
        if (!$submission) {
            http_response_code(404);
            echo 'Envio não encontrado.';
            return;
        }

        $files = $this->fileRepo->findBySubmission($id);
        $notes = $this->noteRepo->listVisibleForSubmission($id);
        $responseFiles = $this->fileRepo->findVisibleToUser($id);

        $pageTitle   = 'Detalhes da submissão';
        $contentView = __DIR__ . '/../../View/portal/submissions/show.php';
        $viewData = [
            'submission'    => $submission,
            'files'         => $files,          // se ainda usa
            'responseFiles' => $responseFiles,
            'notes'         => $notes,
        ];

        require __DIR__ . '/../../View/portal/layouts/base.php';
    }

    private function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }
}
