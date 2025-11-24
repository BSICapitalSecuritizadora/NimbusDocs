<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Repository\AdminUserRepository;
use PDO;

final class MySqlAdminUserRepository implements AdminUserRepository
{
    public function __construct(private PDO $pdo) {}

    public function findActiveByEmail(string $email): ?array
    {
        $sql = "SELECT * FROM admin_users
                WHERE email = :email AND status = 'ACTIVE'
                LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function updateLastLogin(int $id, string $provider): void
    {
        $sql = "UPDATE admin_users
                SET last_login_at = NOW(), last_login_provider = :p
                WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':p' => $provider, ':id' => $id]);
    }
}
