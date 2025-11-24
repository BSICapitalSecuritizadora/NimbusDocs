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

    public function findValidWithUserByCode(string $code): ?array
    {
        $sql = "SELECT 
                    t.id           AS token_id,
                    t.code         AS token_code,
                    t.expires_at   AS token_expires_at,
                    t.status       AS token_status,
                    t.used_at      AS token_used_at,
                    t.created_at   AS token_created_at,
                    u.id           AS user_id,
                    u.full_name    AS user_full_name,
                    u.email        AS user_email,
                    u.document_number AS user_document_number,
                    u.phone_number    AS user_phone_number,
                    u.status       AS user_status
                FROM portal_access_tokens t
                INNER JOIN portal_users u ON u.id = t.portal_user_id
                WHERE t.code = :code
                  AND t.status = 'PENDING'
                  AND t.expires_at > NOW()
                  AND u.status IN ('ACTIVE', 'INVITED')
                ORDER BY t.created_at DESC
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':code' => $code]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function markAsUsed(int $tokenId, string $ip, string $userAgent): void
    {
        $sql = "UPDATE portal_access_tokens
                SET status = 'USED',
                    used_at = NOW(),
                    used_ip = :ip,
                    used_user_agent = :ua
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id' => $tokenId,
            ':ip' => $ip,
            ':ua' => mb_substr($userAgent, 0, 255),
        ]);
    }
}
