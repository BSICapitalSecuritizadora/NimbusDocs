<?php

declare(strict_types=1);

namespace App\Domain\Repository;

interface PortalUserRepository
{
    public function paginate(int $page, int $perPage): array;
    public function findById(int $id): ?array;
    public function findActiveByLogin(string $identifier): ?array;
    public function create(array $data): int;
    public function update(int $id, array $data): void;
    public function updatePassword(int $id, string $passwordHash): void;
    public function updateLastLogin(int $id, string $method): void;
    public function deactivate(int $id): void;
}
