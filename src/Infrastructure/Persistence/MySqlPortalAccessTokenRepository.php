<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Repository\PortalAccessTokenRepository;
use App\Support\Encrypter;
use DateTimeInterface;
use PDO;

final class MySqlPortalAccessTokenRepository implements PortalAccessTokenRepository
{
    public function __construct(private PDO $pdo) {}

    public function create(array $data): int
    {
        // Garante unicidade lógica: antes de criar um novo token, revoga os válidos anteriores
        if (isset($data['portal_user_id'])) {
            $this->revokePreviousValidTokensForUser((int)$data['portal_user_id']);
        }

        $sql = "INSERT INTO portal_access_tokens
                (portal_user_id, code, expires_at, status)
                VALUES (:portal_user_id, :code, :expires_at, :status)";

        $stmt = $this->pdo->prepare($sql);
        // Aceita tanto 'code' (nome da coluna) quanto 'token' (nome antigo na camada de cima)
        $rawCode = $data['code'] ?? $data['token'] ?? null;
        $hashedCode = $rawCode ? Encrypter::hash($rawCode) : null;

        $stmt->execute([
            ':portal_user_id' => $data['portal_user_id'],
            ':code'           => $hashedCode,
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
        $stmt->execute([':code' => Encrypter::hash($token)]);

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
        $stmt->execute([':code' => Encrypter::hash($code)]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Busca um token por código, independente de status/expiração.
     * Útil para diagnosticar se um código informado está expirado
     * e acionar notificações relacionadas.
     */
    public function findByCode(string $code): ?array
    {
        $sql = "SELECT * FROM portal_access_tokens WHERE code = :code ORDER BY created_at DESC LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':code' => Encrypter::hash($code)]);
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
        // Mantido para compatibilidade; revoga tokens pendentes (independente de expiração)
        $this->revokePreviousValidTokensForUser($portalUserId);
    }

    /**
     * Revoga quaisquer tokens ainda válidos (pendentes, não usados e não expirados)
     * de um usuário antes de emitir um novo.
     */
    public function revokePreviousValidTokensForUser(int $portalUserId): void
    {
        $sql = "UPDATE portal_access_tokens
                SET status = 'REVOKED'
                WHERE portal_user_id = :uid
                  AND status = 'PENDING'
                  AND used_at IS NULL
                  AND expires_at > NOW()";

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

    public function findById(int $id): ?array
    {
        $sql = "SELECT t.*, u.full_name AS user_name, u.email AS user_email
            FROM portal_access_tokens t
            LEFT JOIN portal_users u ON u.id = t.portal_user_id
            WHERE t.id = :id
            LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * @return array{items: array<int,array>, total: int}
     */
    public function paginate(int $page, int $perPage, ?array $filters = null): array
    {
        $filters ??= [];
        $where  = [];
        $params = [];

        if (!empty($filters['status'])) {
            if ($filters['status'] === 'valid') {
                $where[] = 't.used_at IS NULL AND t.expires_at >= NOW()';
            } elseif ($filters['status'] === 'expired') {
                $where[] = 't.used_at IS NULL AND t.expires_at < NOW()';
            } elseif ($filters['status'] === 'used') {
                $where[] = 't.used_at IS NOT NULL';
            }
        }

        if (!empty($filters['search'])) {
            $where[] = '(u.full_name LIKE :search OR u.email LIKE :search)';
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        $whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

        $sqlCount = "SELECT COUNT(*)
                 FROM portal_access_tokens t
                 LEFT JOIN portal_users u ON u.id = t.portal_user_id
                 {$whereSql}";
        $stmt = $this->pdo->prepare($sqlCount);
        $stmt->execute($params);
        $total = (int)$stmt->fetchColumn();

        $offset = ($page - 1) * $perPage;

        $sql = "SELECT t.*, u.full_name AS user_name, u.email AS user_email
            FROM portal_access_tokens t
            LEFT JOIN portal_users u ON u.id = t.portal_user_id
            {$whereSql}
            ORDER BY t.created_at DESC, t.id DESC
            LIMIT :limit OFFSET :offset";

        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $items = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        return [
            'items' => $items,
            'total' => $total,
        ];
    }

    public function revoke(int $id): void
    {
        $sql = "UPDATE portal_access_tokens
            SET used_at = IF(used_at IS NULL, NOW(), used_at)
            WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
    }

    /**
     * Remove tokens expirados ou revogados há mais de X dias para limpeza (LGPD).
     */
    public function deleteExpired(int $days): int
    {
        $sql = "DELETE FROM portal_access_tokens 
                WHERE (status = 'REVOKED' OR expires_at < NOW())
                  AND created_at < DATE_SUB(NOW(), INTERVAL :days DAY)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':days', $days, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->rowCount();
    }
}
