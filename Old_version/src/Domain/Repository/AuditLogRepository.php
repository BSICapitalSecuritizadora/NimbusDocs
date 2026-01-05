<?php

declare(strict_types=1);

namespace App\Domain\Repository;

interface AuditLogRepository
{
    public function create(array $data): int;

    /**
     * Lista paginada de logs (mais recentes primeiro)
     * @return array{items: array<int,array>, total: int}
     */
    public function paginate(int $page = 1, int $perPage = 50, ?array $filters = null): array;
}
