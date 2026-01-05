<?php

declare(strict_types=1);

namespace App\Domain\Repository;

interface AdminUserRepository
{
    public function findById(int $id): ?array;
    public function findActiveByEmail(string $email): ?array;
    public function paginate(int $page, int $perPage): array;
    public function countAll(): int;
    public function create(array $data): int;
    public function update(int $id, array $data): void;
    public function updateLastLogin(int $id, string $provider): void;
}
