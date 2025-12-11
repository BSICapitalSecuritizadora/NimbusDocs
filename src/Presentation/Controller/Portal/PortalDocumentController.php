<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Portal;

use App\Infrastructure\Persistence\MySqlPortalDocumentRepository;
use App\Support\Auth;

final class PortalDocumentController
{
    private MySqlPortalDocumentRepository $repo;
    public function __construct(private array $config)
    {
        $this->repo = new MySqlPortalDocumentRepository($config['pdo']);
    }

    public function index(): void
    {
        $user = Auth::requirePortalUser();

        $docs = $this->repo->findByUser((int)$user['id']);

        $pageTitle   = 'Meus Documentos';
        $contentView = __DIR__ . '/../../View/portal/documents/index.php';

        $viewData = [
            'user'  => $user,
            'docs'  => $docs,
        ];

        require __DIR__ . '/../../View/portal/layouts/base.php';
    }

    public function download(array $vars): void
    {
        $user = Auth::requirePortalUser();
        $docId = (int)$vars['id'];

        $doc = $this->repo->find($docId);

        if (!$doc || (int)$doc['portal_user_id'] !== (int)$user['id']) {
            http_response_code(403);
            echo 'Acesso negado.';
            exit;
        }

        // Log de download
        $logger = $this->config['portal_access_logger'] ?? null;
        if ($logger) {
            $logger->log((int)$user['id'], 'DOWNLOAD_DOCUMENT', 'document', $docId);
        }

        header('Content-Type: ' . $doc['file_mime']);
        header('Content-Disposition: attachment; filename="' . $doc['file_original_name'] . '"');
        header('Content-Length: ' . $doc['file_size']);

        readfile($doc['file_path']);
        exit;
    }
}
