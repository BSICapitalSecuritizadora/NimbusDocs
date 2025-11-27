<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Repository\PortalAccessTokenRepository;
use DateTimeInterface;
use PDO;

final class MySqlPortalAccessTokenRepository implements PortalAccessTokenRepository
{
    public function __construct(private PDO $pdo) {}

    public function create(array $data): int
    {
        $sql = "INSERT INTO portal_access_tokens
                (portal_user_id, code, expires_at, status)
                VALUES (:portal_user_id, :code, :expires_at, :status)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':portal_user_id' => $data['portal_user_id'],
            ':code'           => $data['token'], // interface usa 'token', mas tabela usa 'code'
            ':expires_at'     => $data['expires_at'],
            ':status'         => 'PENDING',
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function findValidToken(string $token): ?array
    {
        $sql = "SELECT *
                FROM portal_access_tokens
                WHERE code = :code
                  AND status = 'PENDING'
                  AND expires_at > NOW()
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':code' => $token]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function markAsUsed(int $id): void
    {
        $sql = "UPDATE portal_access_tokens
                SET status = 'USED',
                    used_at = NOW()
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
    }

    public function listRecentForUser(int $portalUserId, int $limit = 10): array
    {
        $sql = "SELECT *
            FROM portal_access_tokens
            WHERE portal_user_id = :uid
            ORDER BY created_at DESC
            LIMIT :limit";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':uid', $portalUserId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function invalidateOldTokensForUser(int $portalUserId): void
    {
        $sql = "UPDATE portal_access_tokens
                SET status = 'REVOKED'
                WHERE portal_user_id = :uid
                  AND status = 'PENDING'";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':uid' => $portalUserId]);
    }
}
