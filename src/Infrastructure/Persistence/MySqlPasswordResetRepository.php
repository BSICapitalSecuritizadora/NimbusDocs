<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Repository\PasswordResetRepository;
use App\Support\Encrypter;
use PDO;

/**
 * MySQL implementation of PasswordResetRepository
 */
class MySqlPasswordResetRepository implements PasswordResetRepository
{
    public function __construct(
        private PDO $pdo
    ) {}

    public function create(int $adminUserId, string $token, \DateTimeInterface $expiresAt): int
    {
        $sql = "INSERT INTO password_reset_tokens (admin_user_id, token, expires_at) VALUES (:user_id, :token, :expires_at)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'user_id' => $adminUserId,
            'token' => Encrypter::hash($token),
            'expires_at' => $expiresAt->format('Y-m-d H:i:s'),
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function findValidByToken(string $token): ?array
    {
        $sql = "
            SELECT prt.*, au.email, au.name
            FROM password_reset_tokens prt
            JOIN admin_users au ON au.id = prt.admin_user_id
            WHERE prt.token = :token
              AND prt.expires_at > NOW()
              AND prt.used_at IS NULL
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['token' => Encrypter::hash($token)]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function markAsUsed(string $token): bool
    {
        $sql = "UPDATE password_reset_tokens SET used_at = NOW() WHERE token = :token";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['token' => Encrypter::hash($token)]);

        return $stmt->rowCount() > 0;
    }

    public function deleteByUserId(int $adminUserId): int
    {
        $sql = "DELETE FROM password_reset_tokens WHERE admin_user_id = :user_id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['user_id' => $adminUserId]);

        return $stmt->rowCount();
    }

    public function deleteExpired(): int
    {
        $sql = "DELETE FROM password_reset_tokens WHERE expires_at < NOW()";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->rowCount();
    }
}
