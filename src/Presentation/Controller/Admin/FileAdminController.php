<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Admin;

use App\Infrastructure\Persistence\MySqlPortalSubmissionFileRepository;
use App\Support\Session;

final class FileAdminController
{
    private MySqlPortalSubmissionFileRepository $fileRepo;

    public function __construct(private array $config)
    {
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

        readfile($fullPath);
        exit;
    }
}
