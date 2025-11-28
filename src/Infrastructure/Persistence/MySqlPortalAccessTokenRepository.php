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

    public function markAsUsed(int $id, string $ip = '', string $userAgent = ''): void
    {
        $sql = "UPDATE portal_access_tokens
                SET status = 'USED',
                    used_at = NOW(),
                    used_ip = :ip,
                    used_user_agent = :ua
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id' => $id,
            ':ip' => $ip,
            ':ua' => mb_substr($userAgent, 0, 255),
        ]);
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

    public function countValid(): int
    {
        return (int)$this->pdo->query(
            "SELECT COUNT(*) 
         FROM portal_access_tokens 
         WHERE used_at IS NULL 
           AND expires_at >= NOW()"
        )->fetchColumn();
    }

    public function countExpired(): int
    {
        return (int)$this->pdo->query(
            "SELECT COUNT(*) 
         FROM portal_access_tokens 
         WHERE expires_at < NOW()"
        )->fetchColumn();
    }
}
