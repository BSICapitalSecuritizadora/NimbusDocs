<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Portal;

use App\Infrastructure\Persistence\MySqlPortalSubmissionFileRepository;
use App\Support\Auth;

final class PortalFileController
{
    private MySqlPortalSubmissionFileRepository $fileRepo;

    public function __construct(private array $config)
    {
        $this->fileRepo = new MySqlPortalSubmissionFileRepository($config['pdo']);
    }

    public function download(array $vars = []): void
    {
        $user = Auth::requirePortalUser();

        $id   = (int)($vars['id'] ?? 0);
        $file = $this->fileRepo->findById($id);

        if (!$file || (int)$file['visible_to_user'] !== 1 || $file['origin'] !== 'ADMIN') {
            http_response_code(404);
            echo 'Arquivo não encontrado.';
            return;
        }

        // Aqui seria ideal validar se a submissão pertence a esse usuário.
        // Se o seu repo de submissões tiver findByIdForUser, use aqui.
        // Exemplo (pseudo):
        // $submission = $this->submissionRepo->findByIdForUser((int)$file['submission_id'], (int)$user['id']);
        // if (!$submission) { 404 ... }

        $logger = $this->config['portal_access_logger'] ?? null;
        if ($logger) {
            $logger->log((int)$user['id'], 'DOWNLOAD_SUBMISSION_FILE', 'submission_file', $fileId);
        }

        $storageBase = dirname(__DIR__, 5) . '/storage/';
        $fullPath    = $storageBase . ltrim($file['storage_path'], '/');

        if (!is_file($fullPath)) {
            http_response_code(404);
            echo 'Arquivo físico não encontrado.';
            return;
        }

        $mime = $file['mime_type'] ?: 'application/octet-stream';

        header('Content-Description: File Transfer');
        header('Content-Type', $mime);
        header('Content-Disposition: attachment; filename="' . basename($file['original_name']) . '"');
        header('Content-Length: ' . (string)$file['size_bytes']);

        readfile($fullPath);
        exit;
    }
}
