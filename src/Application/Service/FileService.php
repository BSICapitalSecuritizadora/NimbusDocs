<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Domain\Repository\PortalSubmissionFileRepository;
use App\Support\FileUpload;
use RuntimeException;

class FileService
{
    public function __construct(
        private PortalSubmissionFileRepository $fileRepo,
        private array $config // Injecting config for upload paths and constraints
    ) {}

    /**
     * Process multiple file uploads for a submission from Admin context
     * 
     * @param int $submissionId
     * @param array $filesArray $_FILES['key']
     * @param int|null $adminUserId
     * @return array Result summary ['count' => int, 'errors' => array]
     */
    public function processAdminResponseUploads(int $submissionId, array $filesArray, ?int $adminUserId): array
    {
        if (empty($filesArray) || !isset($filesArray['name']) || !is_array($filesArray['name'])) {
            throw new RuntimeException('Nenhum arquivo enviado.');
        }

        $maxSize = (int)($this->config['upload']['max_filesize_bytes'] ?? 104857600);
        $uploadDir = rtrim((string)($this->config['upload']['storage_path'] ?? ''), '/');
        
        if ($uploadDir === '') {
            // Fallback safe path relative to project root if config is missing
            $uploadDir = dirname(__DIR__, 3) . '/storage/uploads';
        }

        // Organize by Year/Month
        $baseDir = $uploadDir . '/' . date('Y') . '/' . date('m');
        if (!is_dir($baseDir)) {
            if (!mkdir($baseDir, 0775, true) && !is_dir($baseDir)) {
                throw new RuntimeException('Falha ao criar diretório de upload.');
            }
        }

        $count = count($filesArray['name']);
        $uploaded = 0;
        $errors = [];

        for ($i = 0; $i < $count; $i++) {
            $error = $filesArray['error'][$i];
            if ($error === UPLOAD_ERR_NO_FILE) {
                continue;
            }
            if ($error !== UPLOAD_ERR_OK) {
                $errors[] = "Erro no arquivo {$filesArray['name'][$i]} (Code: $error)";
                continue;
            }

            $size = (int)$filesArray['size'][$i];
            if ($size <= 0 || $size > $maxSize) {
                $errors[] = "Arquivo {$filesArray['name'][$i]} excede o tamanho permitido.";
                continue;
            }

            $tmpName = $filesArray['tmp_name'][$i];
            $originalName = $filesArray['name'][$i];

            if (!is_uploaded_file($tmpName)) {
                $errors[] = "Arquivo {$filesArray['name'][$i]} inválido.";
                continue;
            }

            try {
                $tempFile = [
                    'name'     => $originalName,
                    'type'     => $filesArray['type'][$i] ?? 'application/octet-stream',
                    'tmp_name' => $tmpName,
                    'error'    => $error,
                    'size'     => $size,
                ];
                
                // Uses Support\FileUpload for secure storage (random name, extension check)
                $stored = FileUpload::store($tempFile, $baseDir);
                
                $storedName = basename($stored['path']);
                // Save relative path for database
                $relative   = str_replace($uploadDir . '/', '', $stored['path']);
                $checksum   = hash_file('sha256', $stored['path']);

                $this->fileRepo->create($submissionId, [
                    'origin'          => 'ADMIN',
                    'original_name'   => $stored['original_name'],
                    'stored_name'     => $storedName,
                    'mime_type'       => $stored['mime_type'],
                    'size_bytes'      => $stored['size'],
                    'storage_path'    => $relative,
                    'checksum'        => $checksum,
                    'visible_to_user' => 1, // Admin uploads are visible to user by default
                    'uploaded_by'     => $adminUserId // Optional, depending on schema support
                ]);
                
                $uploaded++;
            } catch (\Throwable $e) {
                $errors[] = "{$originalName}: " . $e->getMessage();
            }
        }

        return [
            'uploaded' => $uploaded,
            'errors'   => $errors
        ];
    }
}
