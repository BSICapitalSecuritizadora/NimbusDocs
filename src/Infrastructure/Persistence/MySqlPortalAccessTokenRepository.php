<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Repository\PortalAccessTokenRepository;
use DateTimeInterface;
use PDO;

final class MySqlPortalAccessTokenRepository implements PortalAccessTokenRepository
{
    public function __construct(private PDO $pdo) {}

    public function createToken(int $portalUserId, string $code, DateTimeInterface $expiresAt): int
    {
        $sql = "INSERT INTO portal_access_tokens
                (portal_user_id, code, expires_at, status)
                VALUES (:portal_user_id, :code, :expires_at, 'PENDING')";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':portal_user_id' => $portalUserId,
            ':code'           => $code,
            ':expires_at'     => $expiresAt->format('Y-m-d H:i:s'),
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function listRecentByUser(int $portalUserId, int $limit = 10): array
    {
        $sql = "SELECT *
                FROM portal_access_tokens
                WHERE portal_user_id = :portal_user_id
                ORDER BY created_at DESC
                LIMIT :limit";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':portal_user_id', $portalUserId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function revokePendingTokens(int $portalUserId): void
    {
        $sql = "UPDATE portal_access_tokens
                SET status = 'REVOKED'
                WHERE portal_user_id = :portal_user_id
                  AND status = 'PENDING'";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':portal_user_id' => $portalUserId]);
    }
}
