<?php

declare(strict_types=1);

namespace App\Domain\Repository;

interface PortalSubmissionFileRepository
{
    /** Cria um registro de arquivo e retorna o ID */
    public function create(int $submissionId, array $data): int;

    /** @return array<int, array> */
    public function findBySubmission(int $submissionId): array;

    public function findById(int $id): ?array;
}
