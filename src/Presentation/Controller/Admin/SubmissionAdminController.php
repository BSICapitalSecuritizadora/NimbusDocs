<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Admin;

use App\Infrastructure\Persistence\MySqlPortalSubmissionRepository;
use App\Infrastructure\Persistence\MySqlPortalSubmissionFileRepository;
use App\Infrastructure\Persistence\MySqlPortalUserRepository;
use App\Support\Csrf;
use App\Support\AuditLogger;
use App\Support\Session;
use App\Support\Auth;
use App\Support\FileUpload;
use App\Infrastructure\Persistence\MySqlPortalSubmissionNoteRepository;
use Respect\Validation\Validator as v;

final class SubmissionAdminController
{
    private MySqlPortalSubmissionRepository $repo;
    private MySqlPortalSubmissionFileRepository $fileRepo;
    private MySqlPortalSubmissionNoteRepository $noteRepo;
    private MySqlPortalUserRepository $portalUserRepo;
    private AuditLogger $audit;

    public function __construct(
        private array $config,
        private ?\App\Application\Service\FileService $fileService = null,
        private ?\App\Application\Service\ExportService $exportService = null
    ) {
        $this->repo           = new MySqlPortalSubmissionRepository($config['pdo']);
        $this->fileRepo       = new MySqlPortalSubmissionFileRepository($config['pdo']);
        $this->noteRepo       = new MySqlPortalSubmissionNoteRepository($config['pdo']);
        $this->portalUserRepo = new MySqlPortalUserRepository($config['pdo']);
        $this->audit          = new AuditLogger($config['pdo']);
        
        // Fallback instantiation if manual DI fails (backwards compatibility or simple testing)
        if (!$this->fileService) {
             $this->fileService = new \App\Application\Service\FileService($this->fileRepo, $config);
        }
        if (!$this->exportService) {
             $this->exportService = new \App\Application\Service\ExportService();
        }
    }

    private function requireAdmin(): void
    {
        Auth::requireAdmin();
    }

    public function index(array $vars = []): void
    {
        $this->requireAdmin();

        $page    = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = 15;

        $filters = [
            'status'    => $_GET['status']    ?? null,
            'user_name' => $_GET['user_name'] ?? null,
        ];

        $pagination = $this->repo->paginateAll($filters, $page, $perPage);

        $pageTitle   = 'Envios';
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
        
        // Busca logs de auditoria desta submissão
        $auditLogs = $this->audit->getByContext('submission', $id, 15);

        $pageTitle   = 'Detalhes da Submissão';
        $contentView = __DIR__ . '/../../View/admin/submissions/show.php';
        $viewData = [
            'submission'    => $submission,
            'files'         => $files,
            'userFiles'     => $userFiles,
            'responseFiles' => $responseFiles,
            'notes'         => $notes,
            'auditLogs'     => $auditLogs,
            'csrfToken'     => Csrf::token(),
        ];

        require __DIR__ . '/../../View/admin/layouts/base.php';
    }

    /**
     * Reenvia notificação ao usuário sobre a submissão
     */
    public function resendNotification(array $vars = []): void
    {
        $this->requireAdmin();
        $admin = Auth::requireAdmin();

        $id = (int)($vars['id'] ?? 0);
        $token = $_POST['_token'] ?? '';

        if (!Csrf::validate($token)) {
            Session::flash('error', 'Sessão expirada. Tente novamente.');
            $this->redirect('/admin/submissions/' . $id);
        }

        $submission = $this->repo->findWithUserById($id);
        if (!$submission) {
            Session::flash('error', 'Submissão não encontrada.');
            $this->redirect('/admin/submissions');
        }

        $portalUser = $this->portalUserRepo->findById((int)$submission['portal_user_id']);
        if (!$portalUser) {
            Session::flash('error', 'Usuário do portal não encontrado.');
            $this->redirect('/admin/submissions/' . $id);
        }

        // Envia notificação
        $notification = $this->config['notification'] ?? null;
        if ($notification) {
            try {
                $notification->notifySubmissionReceived($submission, $portalUser);
                
                // Log de auditoria
                $this->audit->log(
                    'ADMIN',
                    $admin['id'] ? (int)$admin['id'] : null,
                    'SUBMISSION_NOTIFICATION_RESENT',
                    'submission',
                    $id,
                    ['recipient_email' => $portalUser['email']]
                );

                Session::flash('success', 'Notificação reenviada com sucesso para ' . $portalUser['email'] . '.');
            } catch (\Throwable $e) {
                Session::flash('error', 'Erro ao reenviar notificação: ' . $e->getMessage());
            }
        } else {
            Session::flash('error', 'Serviço de notificação não configurado.');
        }

        $this->redirect('/admin/submissions/' . $id);
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

        // Guarda status anterior
        $submissionData = $this->repo->findById($id);
        $oldStatus = $submissionData['status'] ?? null;

        // Atualiza status
        $admin = Auth::requireAdmin();
        $adminId = $admin['id'] ?? null;

        $this->repo->updateStatus($id, $status, $adminId ? (int)$adminId : null);

        // Cria nota (opcional)
        if ($noteText !== '') {
            $this->noteRepo->create([
                'submission_id' => $id,
                'admin_user_id' => $adminId ? (int)$adminId : null,
                'visibility'    => $visible,
                'message'       => $noteText,
            ]);
        }

        // Busca submissão atualizada
        $updatedSubmission = $this->repo->findById($id);

        // Busca usuário do portal para notificação
        $portalUser = $this->portalUserRepo->findById((int)$updatedSubmission['portal_user_id']);

        // Envia notificação se status mudou
        $notifications = $this->config['notifications_service'] ?? null;
        if ($notifications && $portalUser && $oldStatus !== $updatedSubmission['status']) {
            $notifications->portalSubmissionStatusChanged(
                $portalUser,
                $updatedSubmission,
                (string)$oldStatus,
                (string)$updatedSubmission['status']
            );
        }

        // Log de auditoria
        $this->audit->log(
            'ADMIN',
            $adminId ? (int)$adminId : null,
            'SUBMISSION_STATUS_CHANGED',
            'submission',
            $id,
            [
                'old_status' => $oldStatus,
                'new_status' => $status,
                'note'       => $noteText,
            ]
        );

        Session::flash('success', 'Status atualizado com sucesso.');
        $this->redirect('/admin/submissions/' . $id);
    }

    public function uploadResponseFiles(array $vars = []): void
    {
        $this->requireAdmin();
        $admin = Auth::requireAdmin();

        $id    = (int)($vars['id'] ?? 0);
        $post  = $_POST;
        $token = $post['_token'] ?? '';

        if (!Csrf::validate($token)) {
            Session::flash('error', 'Sessão expirada. Tente novamente.');
            $this->redirect('/admin/submissions/' . $id);
        }

        try {
            // Delegate to Service
            $result = $this->fileService->processAdminResponseUploads(
                $id,
                $_FILES['response_files'] ?? [],
                (int)$admin['id']
            );

            if (!empty($result['errors'])) {
                // Log partial errors but show success for uploaded ones
                $errorMsg = implode('<br>', $result['errors']);
                Session::flash('error', 'Alguns arquivos não puderam ser enviados:<br>' . $errorMsg);
            }

            if ($result['uploaded'] > 0) {
                 $this->config['audit']->adminAction([
                    'actor_id'    => $admin['id'] ?? null,
                    'actor_name'  => $admin['name'] ?? null,
                    'action'      => 'SUBMISSION_RESPONSE_FILES_UPLOADED',
                    'summary'     => 'Arquivos de resposta enviados ao usuário.',
                    'context_type' => 'submission',
                    'context_id'  => $id,
                    'details'     => [
                        'files_count' => $result['uploaded'],
                    ],
                ]);

                // Notification Logic
                $submission = $this->repo->findById($id);
                $portalUser = $this->portalUserRepo->findById((int)$submission['portal_user_id']);
                $notifications = $this->config['notifications_service'] ?? null;
                if ($notifications && $portalUser) {
                    $notifications->portalSubmissionResponseUploaded($portalUser, $submission);
                }

                Session::flash('success', $result['uploaded'] . ' arquivo(s) enviado(s) com sucesso.');
            } else if (empty($result['errors'])) {
                Session::flash('error', 'Nenhum arquivo válido enviado.');
            }

        } catch (\Exception $e) {
            Session::flash('error', $e->getMessage());
        }

        $this->redirect('/admin/submissions/' . $id);
    }

    public function exportCsv(array $vars = []): void
    {
        $this->requireAdmin();

        $filters = [
            'status'    => $_GET['status']    ?? null,
            'user_name' => $_GET['user_name'] ?? null,
            'from_date' => $_GET['from_date'] ?? null,
            'to_date'   => $_GET['to_date']   ?? null,
        ];

        // Generator yielding arrays
        $cursor = $this->repo->getExportCursor($filters);

        $headers = [
            'ID',
            'Código',
            'Situação',
            'Tipo',
            'Assunto',
            'Mensagem',
            'Data Envio',
            'Solicitante',
            'Email',
            'Documento',
            'Telefone',
            'ID Usuário',
        ];

        // Wrap cursor to format rows for CSV
        $formattedCursor = (function() use ($cursor) {
            foreach ($cursor as $row) {
                yield [
                    $row['id'],
                    $row['reference_code'],
                    $row['status'],
                    $row['submission_type'],
                    $row['title'],
                    $row['message'],
                    $row['submitted_at'],
                    $row['user_name'],
                    $row['user_email'],
                    $row['user_document_number'],
                    $row['user_phone_number'],
                    $row['portal_user_id']
                ];
            }
        })();

        $this->exportService->streamCsv($formattedCursor, $headers, 'submissoes_export.csv');
    }

    public function exportExcel(array $vars = []): void
    {
        $this->requireAdmin();

        $filters = [
            'status'    => $_GET['status']    ?? null,
            'user_name' => $_GET['user_name'] ?? null,
            'from_date' => $_GET['from_date'] ?? null,
            'to_date'   => $_GET['to_date']   ?? null,
        ];

        $cursor = $this->repo->getExportCursor($filters);

        $columns = [
            'reference_code' => 'Código',
            'status' => [
                'label' => 'Situação',
                'formatter' => function($val) {
                    return match($val) {
                         'PENDING' => 'Pendente',
                         'UNDER_REVIEW' => 'Em Análise',
                         'COMPLETED' => 'Concluído',
                         'APPROVED' => 'Aprovado',
                         'REJECTED' => 'Rejeitado',
                         default => $val
                    };
                }
            ],
            'title' => 'Assunto',
            'submitted_at' => 'Data Envio',
            'user_name' => 'Solicitante',
            'user_email' => 'Email',
            'user_document_number' => [
                'label' => 'Documento (CPF/CNPJ)',
                'style' => 'mso-number-format:\@;' // Force text format
            ]
        ];

        $filename = 'relatorio_submissoes_' . date('Ymd_Hi') . '.xls';
        $this->exportService->streamHtmlExcel($cursor, $columns, 'NIMBUSDOCS — RELATÓRIO DE SUBMISSÕES', $filename);
    }

    public function exportPrint(array $vars = []): void
    {
        $this->requireAdmin();

        $filters = [
            'status'    => $_GET['status']    ?? null,
            'user_name' => $_GET['user_name'] ?? null,
        ];

        // Fetch all filtered records (limit reasonable amount for print)
        $submissions = $this->repo->paginateAll($filters, 1, 500)['items'];

        $pageTitle = 'Relatório de Submissões';
        
        // Render special print layout
        require __DIR__ . '/../../View/admin/submissions/print.php';
        exit;
    }
}
