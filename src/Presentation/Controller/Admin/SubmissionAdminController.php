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

    public function __construct(private array $config)
    {
        $this->repo           = new MySqlPortalSubmissionRepository($config['pdo']);
        $this->fileRepo       = new MySqlPortalSubmissionFileRepository($config['pdo']);
        $this->noteRepo       = new MySqlPortalSubmissionNoteRepository($config['pdo']);
        $this->portalUserRepo = new MySqlPortalUserRepository($config['pdo']);
        $this->audit          = new AuditLogger($config['pdo']);
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
        $uploaded = 0;
        $errors = [];

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

            if (!is_uploaded_file($tmpName)) {
                continue;
            }

            // Usar FileUpload::store() seguro para validação completa
            try {
                $tempFile = [
                    'name'     => $originalName,
                    'type'     => $filesArray['type'][$i] ?? 'application/octet-stream',
                    'tmp_name' => $tmpName,
                    'error'    => $error,
                    'size'     => $size,
                ];
                
                $stored = FileUpload::store($tempFile, $baseDir);
                
                $storedName = basename($stored['path']);
                $relative   = str_replace($uploadDir . '/', '', $stored['path']);
                $checksum   = hash_file('sha256', $stored['path']);

                $this->fileRepo->create($id, [
                    'origin'          => 'ADMIN',
                    'original_name'   => $stored['original_name'],
                    'stored_name'     => $storedName,
                    'mime_type'       => $stored['mime_type'], // MIME real detectado
                    'size_bytes'      => $stored['size'],
                    'storage_path'    => $relative,
                    'checksum'        => $checksum,
                    'visible_to_user' => 1,
                ]);
                
                $uploaded++;
            } catch (\RuntimeException $e) {
                // Log erro e continua para próximo arquivo
                $errors[] = $originalName . ': ' . $e->getMessage();
                continue;
            }
        }

        if (!empty($errors)) {
            error_log('Erros ao processar uploads: ' . implode('; ', $errors));
        }

        $this->config['audit']->adminAction([
            'actor_id'    => $admin['id'] ?? null,
            'actor_name'  => $admin['name'] ?? null,
            'action'      => 'SUBMISSION_RESPONSE_FILES_UPLOADED',
            'summary'     => 'Arquivos de resposta enviados ao usuário.',
            'context_type' => 'submission',
            'context_id'  => $id,
            'details'     => [
                'files_count' => $count, // ou número efetivo de arquivos gravados
            ],
        ]);

        $submission = $this->repo->findById($id);
        $portalUser = $this->portalUserRepo->findById((int)$submission['portal_user_id']);

        $notifications = $this->config['notifications_service'] ?? null;
        if ($notifications && $portalUser) {
            $notifications->portalSubmissionResponseUploaded($portalUser, $submission);
        }

        Session::flash('success', 'Documentos enviados para o usuário.');
        $this->redirect('/admin/submissions/' . $id);
    }

    public function exportCsv(array $vars = []): void
    {
        $this->requireAdmin();

        // Filtros vindos da query string
        $filters = [
            'status'    => $_GET['status']    ?? null,
            'user_name' => $_GET['user_name'] ?? null,
            'from_date'      => $_GET['from_date']      ?? null,
            'to_date'        => $_GET['to_date']        ?? null,
        ];

        $items = $this->repo->exportForAdmin($filters);

        $fields = [
            'id',
            'reference_code',
            'status',
            'title',
            'message',
            'submitted_at',
            'user_name',
            'user_email',
            'user_document_number',
            'user_phone_number',
            'portal_user_id',
        ];

        $data = [];
        foreach ($items as $row) {
            $line = [];
            foreach ($fields as $f) {
                $line[] = $row[$f] ?? '';
            }
            $data[] = $line;
        }

        \App\Support\CsvResponse::output($fields, $data, 'submissoes_export.csv');
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

        $items = $this->repo->exportForAdmin($filters);
        $rowCount = count($items);
        $lastRowIndex = $rowCount + 2; // Title + Header + Data

        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename="relatorio_submissoes_' . date('Ymd_Hi') . '.xls"');
        header('Pragma: no-cache');
        header('Expires: 0');

        echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
        echo '<head>';
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
        echo '<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>Relatório</x:Name><x:WorksheetOptions><x:DisplayGridlines/><x:AutoFilter x:Range="A2:G' . $lastRowIndex . '"/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->';
        echo '<style>
                .header-brand { background-color: #0f172a; color: #ffffff; font-size: 16px; font-weight: bold; text-align: center; height: 40px; vertical-align: middle; }
                .th-style { background-color: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; font-weight: bold; height: 30px; vertical-align: middle; text-align: center; }
                .td-style { border: 1px solid #cbd5e1; vertical-align: middle; padding: 5px; color: #334155; }
                .status-badge { color: #ffffff; font-weight: bold; text-align: center; border: 1px solid #cbd5e1; vertical-align: middle; }
                .odd { background-color: #ffffff; }
                .even { background-color: #f8fafc; }
                .text-center { text-align: center; }
                .text-left { text-align: left; }
              </style>';
        echo '</head>';
        echo '<body>';
        echo '<table border="1" cellpadding="5" cellspacing="0" style="border-collapse: collapse; border-color: #cbd5e1;">';
        
        // Branded Header (Row 1)
        echo '<tr><td colspan="7" class="header-brand">NIMBUSDOCS — RELATÓRIO DE SUBMISSÕES</td></tr>';
        
        // Column Headers (Row 2)
        echo '<tr>';
        echo '<th class="th-style">Código</th>';
        echo '<th class="th-style" style="width: 120px;">Situação</th>';
        echo '<th class="th-style">Assunto</th>';
        echo '<th class="th-style">Data Envio</th>';
        echo '<th class="th-style">Solicitante</th>';
        echo '<th class="th-style">Email</th>';
        echo '<th class="th-style">Documento (CPF/CNPJ)</th>';
        echo '</tr>';

        $i = 0;
        foreach ($items as $row) {
             $i++;
             $rowClass = ($i % 2 === 0) ? 'even' : 'odd';

             $statusData = match($row['status'] ?? '') {
                 'PENDING'      => ['label' => 'Pendente',   'bg' => '#f59e0b'],
                 'UNDER_REVIEW' => ['label' => 'Em Análise', 'bg' => '#3b82f6'],
                 'COMPLETED'    => ['label' => 'Concluído',  'bg' => '#10b981'],
                 'APPROVED'     => ['label' => 'Aprovado',   'bg' => '#10b981'],
                 'REJECTED'     => ['label' => 'Rejeitado',  'bg' => '#ef4444'],
                 default        => ['label' => $row['status'], 'bg' => '#64748b']
             };

            $doc = preg_replace('/\D/', '', $row['user_document_number'] ?? '');
            $maskedDoc = $doc;
            if (strlen($doc) === 11) {
                $maskedDoc = preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $doc);
            } elseif (strlen($doc) === 14) {
                $maskedDoc = preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $doc);
            }

            echo '<tr class="' . $rowClass . '">';
            echo '<td class="td-style text-center">' . htmlspecialchars($row['reference_code'] ?? '') . '</td>';
            echo '<td class="status-badge" style="background-color:' . $statusData['bg'] . ';">' . htmlspecialchars($statusData['label']) . '</td>';
            echo '<td class="td-style text-left">' . htmlspecialchars($row['title'] ?? '') . '</td>';
            echo '<td class="td-style text-center">' . htmlspecialchars($row['submitted_at'] ?? '') . '</td>';
            echo '<td class="td-style text-left">' . htmlspecialchars($row['user_name'] ?? '') . '</td>';
            echo '<td class="td-style text-left">' . htmlspecialchars($row['user_email'] ?? '') . '</td>';
            echo '<td class="td-style text-center" style="mso-number-format:\@;">' . htmlspecialchars($maskedDoc) . '</td>';
            echo '</tr>';
        }

        echo '</table></body></html>';
        exit;
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
