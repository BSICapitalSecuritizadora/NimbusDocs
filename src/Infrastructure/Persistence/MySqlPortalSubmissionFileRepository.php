<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Repository\PortalSubmissionFileRepository;
use PDO;

final class MySqlPortalSubmissionFileRepository implements PortalSubmissionFileRepository
{
    public function __construct(private PDO $pdo) {}

    public function create(int $submissionId, array $data): int
    {
        $sql = "INSERT INTO portal_submission_files
                (submission_id, original_name, stored_name, mime_type, size_bytes, storage_path, checksum)
                VALUES (:submission_id, :original_name, :stored_name, :mime_type, :size_bytes, :storage_path, :checksum)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':submission_id' => $submissionId,
            ':original_name' => $data['original_name'],
            ':stored_name'   => $data['stored_name'],
            ':mime_type'     => $data['mime_type'],
            ':size_bytes'    => $data['size_bytes'],
            ':storage_path'  => $data['storage_path'],
            ':checksum'      => $data['checksum'] ?? null,
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function findBySubmission(int $submissionId): array
    {
        $sql = "SELECT *
                FROM portal_submission_files
                WHERE submission_id = :sid
                ORDER BY uploaded_at ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':sid' => $submissionId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function findById(int $id): ?array
    {
        $sql = "SELECT *
                FROM portal_submission_files
                WHERE id = :id
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }
}
