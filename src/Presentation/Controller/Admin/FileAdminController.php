<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Admin;

use App\Infrastructure\Persistence\MySqlPortalSubmissionFileRepository;
use App\Support\AuditLogger;
use App\Support\Session;

final class FileAdminController
{
    private MySqlPortalSubmissionFileRepository $fileRepo;
    private AuditLogger $audit;

    public function __construct(private array $config)
    {
        $this->fileRepo = new MySqlPortalSubmissionFileRepository($config['pdo']);
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

    public function download(array $vars = []): void
    {
        $this->requireAdmin();

        $id   = (int)($vars['id'] ?? 0);
        $file = $this->fileRepo->findById($id);

        if (!$file) {
            http_response_code(404);
            echo 'Arquivo não encontrado.';
            return;
        }

        $uploadDir = rtrim($this->config['upload_dir'] ?? $this->config['upload']['dir'] ?? dirname(__DIR__, 5) . '/storage/uploads', '/');
        $fullPath  = $uploadDir . '/' . ltrim($file['storage_path'], '/');

        if (!is_file($fullPath)) {
            http_response_code(404);
            echo 'Arquivo físico não encontrado.';
            return;
        }

        $mime = $file['mime_type'] ?: 'application/octet-stream';

        header('Content-Description: File Transfer');
        header('Content-Type: ' . $mime);
        header('Content-Disposition: attachment; filename="' . basename($file['original_name']) . '"');
        header('Content-Length: ' . (string)$file['size_bytes']);
        header('Cache-Control: private');
        header('Pragma: public');

        $admin = Session::get('admin');
        $this->audit->log('ADMIN', $admin['id'] ?? null, 'FILE_DOWNLOAD', 'PORTAL_SUBMISSION_FILE', $id);

        readfile($fullPath);
        exit;
    }

    /**
     * Preview file inline (PDFs and images)
     */
    public function preview(array $vars = []): void
    {
        $this->requireAdmin();

        $id   = (int)($vars['id'] ?? 0);
        $file = $this->fileRepo->findById($id);

        if (!$file) {
            http_response_code(404);
            echo 'Arquivo não encontrado.';
            return;
        }

        $uploadDir = rtrim($this->config['upload_dir'] ?? $this->config['upload']['dir'] ?? dirname(__DIR__, 5) . '/storage/uploads', '/');
        $fullPath  = $uploadDir . '/' . ltrim($file['storage_path'], '/');

        if (!is_file($fullPath)) {
            http_response_code(404);
            echo 'Arquivo físico não encontrado.';
            return;
        }

        $mime = $file['mime_type'] ?: 'application/octet-stream';
        
        // Only allow preview for safe file types
        $previewableMimes = [
            'application/pdf',
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
            'image/svg+xml',
            'text/plain',
        ];

        if (!in_array($mime, $previewableMimes, true)) {
            // Fallback to download for non-previewable files
            $this->download($vars);
            return;
        }

        $admin = Session::get('admin');
        $this->audit->log('ADMIN', $admin['id'] ?? null, 'FILE_PREVIEW', 'PORTAL_SUBMISSION_FILE', $id);

        header('Content-Type: ' . $mime);
        header('Content-Length: ' . (string)$file['size_bytes']);
        header('Content-Disposition: inline; filename="' . basename($file['original_name']) . '"');
        header('Cache-Control: private, max-age=3600');
        
        readfile($fullPath);
        exit;
    }
}
