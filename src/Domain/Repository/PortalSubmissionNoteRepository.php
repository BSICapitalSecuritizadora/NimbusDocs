<?php

declare(strict_types=1);

namespace App\Domain\Repository;

interface PortalSubmissionNoteRepository
{
    public function create(array $data): int;

    /** Notas visíveis para o usuário final */
    public function listVisibleForSubmission(int $submissionId): array;

    /** Todas as notas (admin) */
    public function listAllForSubmission(int $submissionId): array;
}
