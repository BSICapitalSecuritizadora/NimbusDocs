<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Repository\PortalSubmissionNoteRepository;
use PDO;

final class MySqlPortalSubmissionNoteRepository implements PortalSubmissionNoteRepository
{
    public function __construct(private PDO $pdo) {}

    public function create(array $data): int
    {
        $sql = "INSERT INTO portal_submission_notes
                (submission_id, admin_user_id, visibility, message)
                VALUES (:submission_id, :admin_user_id, :visibility, :message)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':submission_id' => $data['submission_id'],
            ':admin_user_id' => $data['admin_user_id'] ?? null,
            ':visibility'    => $data['visibility'],
            ':message'       => $data['message'],
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function listVisibleForSubmission(int $submissionId): array
    {
        $sql = "SELECT *
                FROM portal_submission_notes
                WHERE submission_id = :sid
                  AND visibility = 'USER_VISIBLE'
                ORDER BY created_at ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':sid' => $submissionId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function listAllForSubmission(int $submissionId): array
    {
        $sql = "SELECT *
                FROM portal_submission_notes
                WHERE submission_id = :sid
                ORDER BY created_at ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':sid' => $submissionId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}
