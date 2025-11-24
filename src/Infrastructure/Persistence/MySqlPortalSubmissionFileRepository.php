<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Repository\PortalSubmissionFileRepository;
use PDO;

final class MySqlPortalSubmissionFileRepository implements PortalSubmissionFileRepository
{
    public function __construct(private PDO $pdo) {}

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
}
