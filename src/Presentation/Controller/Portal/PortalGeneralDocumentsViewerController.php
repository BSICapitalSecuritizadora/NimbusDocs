<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Portal;

use App\Infrastructure\Persistence\MySqlGeneralDocumentRepository;
use App\Infrastructure\Logging\PortalAccessLogger;
use App\Support\Auth;

final class PortalGeneralDocumentsViewerController
{
    private MySqlGeneralDocumentRepository $docs;
    private ?PortalAccessLogger $logger;

    public function __construct(private array $config)
    {
        $pdo = $config['pdo'];
        $this->docs = new MySqlGeneralDocumentRepository($pdo);
        $this->logger = $config['portal_access_logger'] ?? null;
    }

    public function view(array $vars): void
    {
        $user = Auth::requirePortalUser();

        $id = (int)($vars['id'] ?? 0);
        $doc = $this->docs->find($id);

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

        $id = (int)($vars['id'] ?? 0);
        $doc = $this->docs->find($id);

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

        header("Content-Type: {$doc['file_mime']}");
        header("Content-Length: {$doc['file_size']}");
        header("Content-Disposition: inline; filename=\"{$doc['file_original_name']}\"");

        readfile($doc['file_path']);
        exit;
    }
}
