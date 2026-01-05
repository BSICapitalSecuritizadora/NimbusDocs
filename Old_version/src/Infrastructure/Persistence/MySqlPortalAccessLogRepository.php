<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use PDO;

final class MySqlPortalAccessLogRepository
{
    public function __construct(private PDO $pdo) {}

    /**
     * @param array<string,mixed> $filters
     * @return array<int,array<string,mixed>>
     */
    public function search(array $filters = []): array
    {
        $where  = [];
        $params = [];

        if (!empty($filters['user_id'])) {
            $where[] = 'l.portal_user_id = :uid';
            $params[':uid'] = (int)$filters['user_id'];
        }

        if (!empty($filters['email'])) {
            $where[] = 'u.email LIKE :email';
            $params[':email'] = '%' . $filters['email'] . '%';
        }

        if (!empty($filters['action'])) {
            $where[] = 'l.action = :action';
            $params[':action'] = $filters['action'];
        }

        if (!empty($filters['resource_type'])) {
            $where[] = 'l.resource_type = :rtype';
            $params[':rtype'] = $filters['resource_type'];
        }

        if (!empty($filters['from_date'])) {
            $where[] = 'l.created_at >= :from';
            $params[':from'] = $filters['from_date'] . ' 00:00:00';
        }

        if (!empty($filters['to_date'])) {
            $where[] = 'l.created_at <= :to';
            $params[':to'] = $filters['to_date'] . ' 23:59:59';
        }

        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $sql = "
            SELECT 
                l.*,
                u.full_name AS user_name,
                u.email     AS user_email
            FROM portal_access_log l
            JOIN portal_users u ON u.id = l.portal_user_id
            {$whereSql}
            ORDER BY l.created_at DESC
            LIMIT 200
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}
