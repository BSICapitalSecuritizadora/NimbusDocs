<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Portal;

use App\Infrastructure\Persistence\MySqlGeneralDocumentRepository;
use App\Infrastructure\Logging\PortalAccessLogger;
use App\Support\Auth;
use App\Support\StreamingFileDownloader;
use App\Support\DownloadConcurrencyGuard;
use App\Support\FileMetadataCache;

final class PortalGeneralDocumentsViewerController
{
    private MySqlGeneralDocumentRepository $docs;
    private ?PortalAccessLogger $logger;
    private StreamingFileDownloader $downloader;
    private DownloadConcurrencyGuard $concurrencyGuard;
    private FileMetadataCache $metadataCache;

    public function __construct(private array $config)
    {
        $pdo = $config['pdo'];
        $this->docs = new MySqlGeneralDocumentRepository($pdo);
        $this->logger = $config['portal_access_logger'] ?? null;
        $this->downloader = new StreamingFileDownloader();
        $this->concurrencyGuard = new DownloadConcurrencyGuard();
        $this->metadataCache = new FileMetadataCache();
    }

    public function view(array $vars): void
    {
        $user = Auth::requirePortalUser();

        $id = (int)($vars['id'] ?? 0);
        
        // Busca metadata com cache
        $doc = $this->metadataCache->remember(
            'general_document',
            $id,
            fn() => $this->docs->find($id)
        );

        if (!$doc || $doc['is_active'] != 1) {
            http_response_code(404);
            echo "Documento não encontrado.";
            return;
        }

        $pageTitle   = "Visualizar documento";
        $contentView = __DIR__ . '/../../View/portal/general_documents/viewer.php';

        $viewData = [
            'doc' => $doc,
        ];

        require __DIR__ . '/../../View/portal/layouts/base.php';
    }

    public function stream(array $vars): void
    {
        $user = Auth::requirePortalUser();
        $userId = $user['id'];
        $clientIp = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

        $id = (int)($vars['id'] ?? 0);

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
                fn() => $this->docs->find($id)
            );

            if (!$doc || $doc['is_active'] != 1) {
                http_response_code(404);
                echo "Documento não encontrado.";
                return;
            }

            // log
            if ($this->logger) {
                $this->logger->log($userId, 'VIEW_GENERAL_DOCUMENT', 'general_document', $id);
            }

            if (!is_file($doc['file_path'])) {
                http_response_code(404);
                echo "Arquivo não encontrado.";
                return;
            }

            // Usa streaming em chunks
            $success = $this->downloader->stream(
                $doc['file_path'],
                $doc['file_mime'],
                $doc['file_original_name'],
                'inline',
                (int)$doc['file_size']
            );

            if (!$success) {
                http_response_code(500);
                echo 'Erro ao processar visualização.';
            }
        } finally {
            // Sempre libera o slot, mesmo em caso de erro
            $this->concurrencyGuard->release($clientIp);
        }
        exit;
    }
}
