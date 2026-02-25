<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Portal;

use App\Infrastructure\Persistence\MySqlPortalSubmissionFileRepository;
use App\Support\Auth;
use App\Support\DownloadConcurrencyGuard;
use App\Support\FileMetadataCache;
use App\Support\StreamingFileDownloader;

final class PortalFileController
{
    private MySqlPortalSubmissionFileRepository $fileRepo;

    private StreamingFileDownloader $downloader;

    private DownloadConcurrencyGuard $concurrencyGuard;

    private FileMetadataCache $metadataCache;

    public function __construct(private array $config)
    {
        $this->fileRepo = new MySqlPortalSubmissionFileRepository($config['pdo']);
        $this->downloader = new StreamingFileDownloader();
        $this->concurrencyGuard = new DownloadConcurrencyGuard();
        $this->metadataCache = new FileMetadataCache();
    }

    public function download(array $vars = []): void
    {
        $user = Auth::requirePortalUser();
        Auth::requireRecentLogin(10); // Exige login nos últimos 10 min

        $id = (int) ($vars['id'] ?? 0);
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
                fn () => $this->fileRepo->findById($id)
            );

            if (!$file || (int) $file['visible_to_user'] !== 1 || $file['origin'] !== 'ADMIN') {
                http_response_code(404);
                echo 'Arquivo não encontrado.';

                return;
            }

            // Validação de Propriedade (Correção IDOR)
            $submissionRepo = new \App\Infrastructure\Persistence\MySqlPortalSubmissionRepository($this->config['pdo']);
            $submission = $submissionRepo->findForUser((int) $file['submission_id'], (int) $user['id']);

            if (!$submission) {
                http_response_code(403);
                echo 'Acesso negado. Este arquivo não pertence a uma de suas submissões.';

                return;
            }

            $logger = $this->config['portal_access_logger'] ?? null;
            if ($logger) {
                $logger->log((int) $user['id'], 'DOWNLOAD_SUBMISSION_FILE', 'submission_file', (int) $id);
            }

            $storageBase = dirname(__DIR__, 4) . '/storage/';
            // Normalize path separators (Windows compatibility)
            $storagePath = str_replace('\\', '/', $file['storage_path']);
            $fullPath = $storageBase . ltrim($storagePath, '/');

            if (!is_file($fullPath)) {
                http_response_code(404);
                echo 'Arquivo físico não encontrado.';

                return;
            }

            $mime = $file['mime_type'] ?: 'application/octet-stream';

            // Usa streaming em chunks
            $success = $this->downloader->stream(
                $fullPath,
                $mime,
                basename($file['original_name']),
                'attachment',
                (int) $file['size_bytes']
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
        $user = Auth::requirePortalUser();
        Auth::requireRecentLogin(10); // Exige login nos últimos 10 min

        $id = (int) ($vars['id'] ?? 0);
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
                fn () => $this->fileRepo->findById($id)
            );

            if (!$file || (int) $file['visible_to_user'] !== 1 || $file['origin'] !== 'ADMIN') {
                http_response_code(404);
                echo 'Arquivo não encontrado.';

                return;
            }

            $logger = $this->config['portal_access_logger'] ?? null;
            if ($logger) {
                $logger->log((int) $user['id'], 'PREVIEW_SUBMISSION_FILE', 'submission_file', (int) $id);
            }

            $storageBase = dirname(__DIR__, 4) . '/storage/';
            // Normalize path separators (Windows compatibility)
            $storagePath = str_replace('\\', '/', $file['storage_path']);
            $fullPath = $storageBase . ltrim($storagePath, '/');

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

            // Usa streaming em chunks
            $success = $this->downloader->stream(
                $fullPath,
                $mime,
                basename($file['original_name']),
                'inline',
                (int) $file['size_bytes']
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
