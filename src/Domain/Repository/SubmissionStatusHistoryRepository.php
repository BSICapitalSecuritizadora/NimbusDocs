<?php

declare(strict_types=1);

namespace App\Domain\Repository;

interface SubmissionStatusHistoryRepository
{
    /**
     * @return array<int, array>
     */
    public function findBySubmissionId(int $submissionId): array;

    /**
     * @param array<string, mixed> $data
     * @return int
     */
    public function create(array $data): int;
}
