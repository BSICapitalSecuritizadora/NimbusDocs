<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Repository\PortalSubmissionRepository;
use PDO;

final class MySqlPortalSubmissionRepository implements PortalSubmissionRepository
{
    public function __construct(private PDO $pdo) {}

    // --------- PORTAL (usuário final) ---------

    public function paginateByUser(int $portalUserId, int $page, int $perPage): array
    {
        $offset = max(0, ($page - 1) * $perPage);

        $stmtTotal = $this->pdo->prepare(
            "SELECT COUNT(*) FROM portal_submissions WHERE portal_user_id = :uid"
        );
        $stmtTotal->execute([':uid' => $portalUserId]);
        $total = (int)$stmtTotal->fetchColumn();

        $sql = "SELECT *
                FROM portal_submissions
                WHERE portal_user_id = :uid
                ORDER BY submitted_at DESC
                LIMIT :perPage OFFSET :offset";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':uid', $portalUserId, PDO::PARAM_INT);
        $stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $items = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        return [
            'items'   => $items,
            'total'   => $total,
            'page'    => $page,
            'perPage' => $perPage,
            'pages'   => (int)ceil($total / $perPage),
        ];
    }

    public function findByIdForUser(int $id, int $portalUserId): ?array
    {
        $sql = "SELECT *
                FROM portal_submissions
                WHERE id = :id AND portal_user_id = :uid
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id, ':uid' => $portalUserId]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function createForUser(int $portalUserId, array $data): int
    {
        $sql = "INSERT INTO portal_submissions
                (portal_user_id, reference_code, title, message, status, created_ip, created_user_agent)
                VALUES (:portal_user_id, :reference_code, :title, :message, :status, :created_ip, :created_user_agent)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':portal_user_id'    => $portalUserId,
            ':reference_code'    => $data['reference_code'],
            ':title'             => $data['title'],
            ':message'           => $data['message'] ?? null,
            ':status'            => $data['status'] ?? 'PENDING',
            ':created_ip'        => $data['created_ip'] ?? null,
            ':created_user_agent' => $data['created_user_agent'] ?? null,
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    // --------- ADMIN ---------

    public function findById(int $id): ?array
    {
        $sql = "SELECT * FROM portal_submissions WHERE id = :id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function paginateAll(array $filters, int $page, int $perPage): array
    {
        $where  = [];
        $params = [];

        if (!empty($filters['status'])) {
            $where[]           = 's.status = :status';
            $params[':status'] = $filters['status'];
        }

        if (!empty($filters['portal_user_id'])) {
            $where[]                = 's.portal_user_id = :uid';
            $params[':uid']         = (int)$filters['portal_user_id'];
        }

        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $sqlTotal = "SELECT COUNT(*)
                     FROM portal_submissions s
                     JOIN portal_users u ON u.id = s.portal_user_id
                     $whereSql";

        $stmtTotal = $this->pdo->prepare($sqlTotal);
        $stmtTotal->execute($params);
        $total = (int)$stmtTotal->fetchColumn();

        $offset = max(0, ($page - 1) * $perPage);

        $sql = "SELECT 
                    s.*,
                    u.full_name AS user_full_name,
                    u.email     AS user_email
                FROM portal_submissions s
                JOIN portal_users u ON u.id = s.portal_user_id
                $whereSql
                ORDER BY s.submitted_at DESC
                LIMIT :perPage OFFSET :offset";

        $stmt = $this->pdo->prepare($sql);

        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }

        $stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $items = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        return [
            'items'   => $items,
            'total'   => $total,
            'page'    => $page,
            'perPage' => $perPage,
            'pages'   => (int)ceil($total / $perPage),
        ];
    }

    public function findWithUserById(int $id): ?array
    {
        $sql = "SELECT 
                    s.*,
                    u.full_name AS user_full_name,
                    u.email     AS user_email,
                    u.document_number AS user_document_number,
                    u.phone_number    AS user_phone_number
                FROM portal_submissions s
                JOIN portal_users u ON u.id = s.portal_user_id
                WHERE s.id = :id
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function updateStatus(int $id, string $status, ?int $adminUserId): void
    {
        $sql = "UPDATE portal_submissions
            SET status = :status,
                status_updated_at = NOW(),
                status_updated_by = :admin_user_id
            WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':status'        => $status,
            ':admin_user_id' => $adminUserId,
            ':id'            => $id,
        ]);
    }

    public function countAll(): int
    {
        return (int)$this->pdo->query("SELECT COUNT(*) FROM portal_submissions")->fetchColumn();
    }

    public function countByStatus(string $status): int
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM portal_submissions WHERE status = :s");
        $stmt->execute([':s' => $status]);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Retorna contagens por status para gráficos.
     * @param array<string> $statuses
     * @return array<string,int>
     */
    public function countsByStatuses(array $statuses): array
    {
        if (!$statuses) {
            return [];
        }
        $placeholders = implode(',', array_fill(0, count($statuses), '?'));
        $sql = "SELECT status, COUNT(*) AS total
                FROM portal_submissions
                WHERE status IN ($placeholders)
                GROUP BY status";
        $stmt = $this->pdo->prepare($sql);
        foreach ($statuses as $i => $st) {
            $stmt->bindValue($i + 1, $st);
        }
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        $out = array_fill_keys($statuses, 0);
        foreach ($rows as $r) {
            $out[$r['status']] = (int)$r['total'];
        }
        return $out;
    }

    /**
     * Contagem por dia (últimos N dias)
     * @return array<int,array{date:string,total:int}>
     */
    public function countsPerDay(int $days = 30): array
    {
        $sql = "SELECT DATE(submitted_at) AS d, COUNT(*) AS total
                FROM portal_submissions
                WHERE submitted_at >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
                GROUP BY d
                ORDER BY d ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':days', $days, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        return array_map(fn($r) => ['date' => $r['d'], 'total' => (int)$r['total']], $rows);
    }

    public function countOlderPending(int $days = 7): int
    {
        $sql = "SELECT COUNT(*)
                FROM portal_submissions
                WHERE status IN ('PENDING','IN_REVIEW')
                  AND submitted_at < DATE_SUB(NOW(), INTERVAL :days DAY)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':days', $days, PDO::PARAM_INT);
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    public function latest(int $limit = 5): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT s.*, u.full_name AS user_name, u.email AS user_email
         FROM portal_submissions s
         JOIN portal_users u ON u.id = s.portal_user_id
         ORDER BY s.submitted_at DESC
         LIMIT :l"
        );
        $stmt->bindValue(':l', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function countForUser(int $userId): int
    {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM portal_submissions WHERE portal_user_id = :uid"
        );
        $stmt->execute([':uid' => $userId]);

        return (int)$stmt->fetchColumn();
    }

    public function countForUserByStatus(int $userId, string $status): int
    {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) 
         FROM portal_submissions 
         WHERE portal_user_id = :uid AND status = :status"
        );
        $stmt->execute([
            ':uid'    => $userId,
            ':status' => $status,
        ]);

        return (int)$stmt->fetchColumn();
    }

    /**
     * Lista as submissões do usuário para o dashboard (com limite)
     * @return array<int,array>
     */
    public function latestForUser(int $userId, int $limit = 10): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT *
         FROM portal_submissions
         WHERE portal_user_id = :uid
         ORDER BY submitted_at DESC
         LIMIT :lim"
        );
        $stmt->bindValue(':uid', $userId, \PDO::PARAM_INT);
        $stmt->bindValue(':lim', $limit, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    public function findForUser(int $id, int $userId): ?array
    {
        $sql = "SELECT *
            FROM portal_submissions
            WHERE id = :id AND portal_user_id = :uid
            LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id'  => $id,
            ':uid' => $userId,
        ]);

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Exporta submissões para o admin, já filtradas (sem paginação)
     * @param array $filters
     * @return array<int,array>
     */
    public function exportForAdmin(array $filters = []): array
    {
        $where = [];
        $params = [];

        if (!empty($filters['status'])) {
            $where[] = 's.status = :status';
            $params[':status'] = $filters['status'];
        }
        if (!empty($filters['portal_user_id'])) {
            $where[] = 's.portal_user_id = :uid';
            $params[':uid'] = (int)$filters['portal_user_id'];
        }
        if (!empty($filters['from_date'])) {
            $where[] = 's.submitted_at >= :from_date';
            $params[':from_date'] = $filters['from_date'];
        }
        if (!empty($filters['to_date'])) {
            $where[] = 's.submitted_at <= :to_date';
            $params[':to_date'] = $filters['to_date'];
        }

        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $sql = "SELECT
            s.id,
            s.reference_code,
            s.status,
            s.title,
            s.message,
            s.submitted_at,
            u.full_name AS user_name,
            u.email     AS user_email,
            u.document_number AS user_document_number,
            u.phone_number    AS user_phone_number,
            s.portal_user_id
        FROM portal_submissions s
        JOIN portal_users u ON u.id = s.portal_user_id
        $whereSql
        ORDER BY s.submitted_at DESC";

        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }
}
