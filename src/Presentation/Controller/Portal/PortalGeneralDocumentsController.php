<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Portal;

use App\Infrastructure\Persistence\MySqlDocumentCategoryRepository;
use App\Infrastructure\Persistence\MySqlGeneralDocumentRepository;
use App\Infrastructure\Logging\PortalAccessLogger;
use App\Support\Auth;

final class PortalGeneralDocumentsController
{
    private MySqlDocumentCategoryRepository $categories;
    private MySqlGeneralDocumentRepository $docs;
    private ?PortalAccessLogger $logger;

    public function __construct(private array $config)
    {
        $pdo = $config['pdo'];
        $this->categories = new MySqlDocumentCategoryRepository($pdo);
        $this->docs       = new MySqlGeneralDocumentRepository($pdo);
        $this->logger     = $config['portal_access_logger'] ?? null;
    }

    public function index(): void
    {
        $user = Auth::requirePortalUser();

        $categoryId = isset($_GET['category_id']) ? (int)$_GET['category_id'] : null;
        $term       = trim($_GET['q'] ?? '');

        $categories = $this->categories->all();
        $documents  = $this->docs->listForPortal($categoryId, $term);

        $pageTitle   = 'Documentos gerais';
        $contentView = __DIR__ . '/../../View/portal/general_documents/index.php';

        $viewData = [
            'user'       => $user,
            'categories' => $categories,
            'documents'  => $documents,
            'currentCategory' => $categoryId,
            'term'       => $term,
        ];

        require __DIR__ . '/../../View/portal/layouts/base.php';
    }

    public function download(array $vars): void
    {
        $user   = Auth::requirePortalUser();
        $userId = (int)$user['id'];

        $id  = (int)($vars['id'] ?? 0);
        $doc = $this->docs->find($id);

        if (!$doc || (int)$doc['is_active'] !== 1) {
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

        header('Content-Type: ' . $mime);
        header('Content-Disposition: ' . $disposition . '; filename="' . $doc['file_original_name'] . '"');
        header('Content-Length: ' . $doc['file_size']);
        readfile($doc['file_path']);
        exit;
    }
}
