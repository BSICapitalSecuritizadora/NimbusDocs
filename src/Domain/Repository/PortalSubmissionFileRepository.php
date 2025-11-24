<?php

declare(strict_types=1);

namespace App\Domain\Repository;

interface PortalSubmissionFileRepository
{
    /** @return array<int, array> */
    public function findBySubmission(int $submissionId): array;
}
