<?php

declare(strict_types=1);

namespace App\Domain\Repository;

interface PortalSubmissionRepository
{
    // PORTAL (usuário final)
    public function paginateByUser(int $portalUserId, int $page, int $perPage, ?string $search = null, ?string $status = null): array;

    public function findByIdForUser(int $id, int $portalUserId): ?array;

    public function createForUser(int $portalUserId, array $data): int;

    // ADMIN
    public function paginateAll(array $filters, int $page, int $perPage): array;

    public function findWithUserById(int $id): ?array;
}
