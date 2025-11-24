<?php

declare(strict_types=1);

namespace App\Domain\Repository;

interface AdminUserRepository
{
    public function findActiveByEmail(string $email): ?array;
    public function updateLastLogin(int $id, string $provider): void;

    // NOVOS:
    public function paginate(int $page, int $perPage): array;
    public function findById(int $id): ?array;
    public function create(array $data): int;
    public function update(int $id, array $data): void;
    public function deactivate(int $id): void;
}
