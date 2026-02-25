<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Portal;

use App\Infrastructure\Persistence\MySqlPortalDocumentRepository;
use App\Support\Auth;
use App\Support\DownloadConcurrencyGuard;
use App\Support\FileMetadataCache;
use App\Support\StreamingFileDownloader;

final class PortalDocumentController
{
    private MySqlPortalDocumentRepository $repo;

    private StreamingFileDownloader $downloader;

    private DownloadConcurrencyGuard $concurrencyGuard;

    private FileMetadataCache $metadataCache;

    public function __construct(private array $config)
    {
        $this->repo = new MySqlPortalDocumentRepository($config['pdo']);
        $this->downloader = new StreamingFileDownloader();
        $this->concurrencyGuard = new DownloadConcurrencyGuard();
        $this->metadataCache = new FileMetadataCache();
    }

    public function index(): void
    {
        $user = Auth::requirePortalUser();

        $docs = $this->repo->findByUser((int) $user['id']);

        $pageTitle = 'Meus Documentos';
        $contentView = __DIR__ . '/../../View/portal/documents/index.php';

        $viewData = [
            'user' => $user,
            'docs' => $docs,
        ];

        require __DIR__ . '/../../View/portal/layouts/base.php';
    }

    public function download(array $vars): void
    {
        $user = Auth::requirePortalUser();
        $docId = (int) $vars['id'];
        $clientIp = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

        // Controle de concorrência
        if (!$this->concurrencyGuard->acquire($clientIp)) {
            http_response_code(429);
            echo 'Limite de downloads simultâneos atingido. Aguarde um download terminar.';

            return;
        }

        try {
            // Busca metadata com cache
            $doc = $this->metadataCache->remember(
                'portal_document',
                $docId,
                fn () => $this->repo->find($docId)
            );

            if (!$doc || (int) $doc['portal_user_id'] !== (int) $user['id']) {
                http_response_code(403);
                echo 'Acesso negado.';

                return;
            }

            // Log de download
            $logger = $this->config['portal_access_logger'] ?? null;
            if ($logger) {
                $logger->log((int) $user['id'], 'DOWNLOAD_DOCUMENT', 'document', $docId);
            }

            if (!is_file($doc['file_path'])) {
                http_response_code(404);
                echo 'Arquivo não encontrado.';

                return;
            }

            // Usa streaming em chunks
            $success = $this->downloader->stream(
                $doc['file_path'],
                $doc['file_mime'],
                $doc['file_original_name'],
                'attachment',
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
