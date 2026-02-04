<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Admin;

use App\Infrastructure\Persistence\MySqlPortalSubmissionFileRepository;
use App\Support\AuditLogger;
use App\Support\Session;
use App\Support\StreamingFileDownloader;
use App\Support\DownloadConcurrencyGuard;
use App\Support\FileMetadataCache;

final class FileAdminController
{
    private MySqlPortalSubmissionFileRepository $fileRepo;
    private AuditLogger $audit;
    private StreamingFileDownloader $downloader;
    private DownloadConcurrencyGuard $concurrencyGuard;
    private FileMetadataCache $metadataCache;

    public function __construct(private array $config)
    {
        $this->fileRepo = new MySqlPortalSubmissionFileRepository($config['pdo']);
        $this->audit    = new AuditLogger($config['pdo']);
        $this->downloader = new StreamingFileDownloader();
        $this->concurrencyGuard = new DownloadConcurrencyGuard();
        $this->metadataCache = new FileMetadataCache();
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

        $id = (int)($vars['id'] ?? 0);
        $clientIp = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

        // Controle de concorrência
        if (!$this->concurrencyGuard->acquire($clientIp)) {
            http_response_code(429);
            echo 'Limite de downloads simultâneos atingido. Aguarde um download terminar.';
            return;
        }

        try {
            // Busca metadata com cache
            $file = $this->metadataCache->remember(
                'submission_file',
                $id,
                fn() => $this->fileRepo->findById($id)
            );

            if (!$file) {
                http_response_code(404);
                echo 'Arquivo não encontrado.';
                return;
            }

            $storageBase = dirname(__DIR__, 4) . '/storage/';
            // Normalize path separators (Windows compatibility)
            $storagePath = str_replace('\\', '/', $file['storage_path']);
            $fullPath  = $storageBase . ltrim($storagePath, '/');

            if (!is_file($fullPath)) {
                http_response_code(404);
                echo 'Arquivo físico não encontrado.';
                return;
            }

            $admin = Session::get('admin');
            $this->audit->log('ADMIN', $admin['id'] ?? null, 'FILE_DOWNLOAD', 'PORTAL_SUBMISSION_FILE', $id);

            $mime = $file['mime_type'] ?: 'application/octet-stream';

            // Usa streaming em chunks
            $success = $this->downloader->stream(
                $fullPath,
                $mime,
                basename($file['original_name']),
                'attachment',
                (int)$file['size_bytes']
            );

            if (!$success) {
                http_response_code(500);
                echo 'Erro ao processar download.';
            }
        } finally {
            // Sempre libera o slot, mesmo em caso de erro
            $this->concurrencyGuard->release($clientIp);
        }
        exit;
    }

    /**
     * Preview file inline (PDFs and images)
     */
    public function preview(array $vars = []): void
    {
        $this->requireAdmin();

        $id = (int)($vars['id'] ?? 0);
        $clientIp = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

        // Controle de concorrência
        if (!$this->concurrencyGuard->acquire($clientIp)) {
            http_response_code(429);
            echo 'Limite de downloads simultâneos atingido. Aguarde um download terminar.';
            return;
        }

        try {
            // Busca metadata com cache
            $file = $this->metadataCache->remember(
                'submission_file',
                $id,
                fn() => $this->fileRepo->findById($id)
            );

            if (!$file) {
                http_response_code(404);
                echo 'Arquivo não encontrado.';
                return;
            }

            $storageBase = dirname(__DIR__, 4) . '/storage/';
            // Normalize path separators (Windows compatibility)
            $storagePath = str_replace('\\', '/', $file['storage_path']);
            $fullPath  = $storageBase . ltrim($storagePath, '/');

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
                $this->concurrencyGuard->release($clientIp);
                $this->download($vars);
                return;
            }

            $admin = Session::get('admin');
            $this->audit->log('ADMIN', $admin['id'] ?? null, 'FILE_PREVIEW', 'PORTAL_SUBMISSION_FILE', $id);

            // Usa streaming em chunks
            $success = $this->downloader->stream(
                $fullPath,
                $mime,
                basename($file['original_name']),
                'inline',
                (int)$file['size_bytes']
            );

            if (!$success) {
                http_response_code(500);
                echo 'Erro ao processar preview.';
            }
        } finally {
            // Sempre libera o slot, mesmo em caso de erro
            $this->concurrencyGuard->release($clientIp);
        }
        exit;
    }
}
