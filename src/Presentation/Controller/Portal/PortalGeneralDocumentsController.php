<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Portal;

use App\Infrastructure\Logging\PortalAccessLogger;
use App\Infrastructure\Persistence\MySqlDocumentCategoryRepository;
use App\Infrastructure\Persistence\MySqlGeneralDocumentRepository;
use App\Support\Auth;
use App\Support\DownloadConcurrencyGuard;
use App\Support\FileMetadataCache;
use App\Support\StreamingFileDownloader;

final class PortalGeneralDocumentsController
{
    private MySqlDocumentCategoryRepository $categories;

    private MySqlGeneralDocumentRepository $docs;

    private ?PortalAccessLogger $logger;

    private StreamingFileDownloader $downloader;

    private DownloadConcurrencyGuard $concurrencyGuard;

    private FileMetadataCache $metadataCache;

    public function __construct(private array $config)
    {
        $pdo = $config['pdo'];
        $this->categories = new MySqlDocumentCategoryRepository($pdo);
        $this->docs = new MySqlGeneralDocumentRepository($pdo);
        $this->logger = $config['portal_access_logger'] ?? null;
        $this->downloader = new StreamingFileDownloader();
        $this->concurrencyGuard = new DownloadConcurrencyGuard();
        $this->metadataCache = new FileMetadataCache();
    }

    public function index(): void
    {
        $queryString = $_SERVER['QUERY_STRING'] ?? '';
        $redirectUrl = '/portal/documents';
        if ($queryString) {
            $redirectUrl .= '?' . $queryString;
        }
        header('Location: ' . $redirectUrl);
        exit;
    }

    public function download(array $vars): void
    {
        $user = Auth::requirePortalUser();
        $userId = (int) $user['id'];
        $clientIp = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

        $id = (int) ($vars['id'] ?? 0);

        // Controle de concorrência
        if (!$this->concurrencyGuard->acquire($clientIp)) {
            http_response_code(429);
            echo 'Limite de downloads simultâneos atingido. Aguarde um download terminar.';

            return;
        }

        try {
            // Busca metadata com cache
            $doc = $this->metadataCache->remember(
                'general_document',
                $id,
                fn () => $this->docs->find($id)
            );

            if (!$doc || (int) $doc['is_active'] !== 1) {
                http_response_code(404);
                echo 'Documento não encontrado.';

                return;
            }

            // logar download
            if ($this->logger) {
                $this->logger->log($userId, 'DOWNLOAD_GENERAL_DOCUMENT', 'general_document', $id);
            }

            if (!is_file($doc['file_path'])) {
                http_response_code(404);
                echo 'Arquivo não encontrado no servidor.';

                return;
            }

            $disposition = isset($_GET['preview']) ? 'inline' : 'attachment';
            $mime = $doc['file_mime'];

            // Force PDF mime type if previewing and extension is pdf
            // (Fixes issue where application/octet-stream forces download)
            if (isset($_GET['preview'])) {
                $ext = strtolower(pathinfo($doc['file_original_name'], PATHINFO_EXTENSION));
                if ($ext === 'pdf') {
                    $mime = 'application/pdf';
                } elseif (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    // Ensure images have correct mime type for preview
                    $mime = match ($ext) {
                        'jpg', 'jpeg' => 'image/jpeg',
                        'png' => 'image/png',
                        'gif' => 'image/gif',
                        'webp' => 'image/webp',
                        default => $mime
                    };
                }
            }

            // Usa streaming em chunks
            $success = $this->downloader->stream(
                $doc['file_path'],
                $mime,
                $doc['file_original_name'],
                $disposition,
                (int) $doc['file_size']
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
}
