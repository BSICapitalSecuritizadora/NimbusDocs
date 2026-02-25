<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use PDO;

final class MySqlSubmissionReportRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    /**
     * Monta WHERE e parâmetros a partir dos filtros.
     * @param array<string,mixed> $filters
     * @return array{where:string, params:array<string,mixed>}
     */
    private function buildWhere(array $filters): array
    {
        $where = [];
        $params = [];

        if (!empty($filters['status'])) {
            $where[] = 's.status = :status';
            $params[':status'] = $filters['status'];
        }

        if (!empty($filters['email'])) {
            $where[] = 'u.email LIKE :email';
            $params[':email'] = '%' . $filters['email'] . '%';
        }

        if (!empty($filters['from_date'])) {
            $where[] = 'DATE(s.submitted_at) >= :from';
            $params[':from'] = $filters['from_date'];
        }

        if (!empty($filters['to_date'])) {
            $where[] = 'DATE(s.submitted_at) <= :to';
            $params[':to'] = $filters['to_date'];
        }

        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        return ['where' => $whereSql, 'params' => $params];
    }

    /**
     * KPIs agregados no período.
     * @param array<string,mixed> $filters
     * @return array<string,int>
     */
    public function kpis(array $filters): array
    {
        ['where' => $whereSql, 'params' => $params] = $this->buildWhere($filters);

        // total geral
        $sqlTotal = "
            SELECT COUNT(*) 
            FROM portal_submissions s
            JOIN portal_users u ON u.id = s.portal_user_id
            {$whereSql}
        ";
        $stmt = $this->pdo->prepare($sqlTotal);
        $stmt->execute($params);
        $total = (int) $stmt->fetchColumn();

        // por status
        $sqlStatus = "
            SELECT s.status, COUNT(*) AS total
            FROM portal_submissions s
            JOIN portal_users u ON u.id = s.portal_user_id
            {$whereSql}
            GROUP BY s.status
        ";
        $stmt = $this->pdo->prepare($sqlStatus);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $byStatus = [];
        foreach ($rows as $r) {
            $byStatus[$r['status']] = (int) $r['total'];
        }

        return [
            'total' => $total,
            'pending' => $byStatus['PENDING'] ?? 0,
            'approved' => $byStatus['COMPLETED'] ?? 0,
            'rejected' => $byStatus['REJECTED'] ?? 0,
        ];
    }

    /**
     * Submissões por dia dentro do filtro.
     * @param array<string,mixed> $filters
     * @return array<int,array{day:string,total:int}>
     */
    public function byDay(array $filters): array
    {
        ['where' => $whereSql, 'params' => $params] = $this->buildWhere($filters);

        $sql = "
            SELECT DATE(s.submitted_at) AS day, COUNT(*) AS total
            FROM portal_submissions s
            JOIN portal_users u ON u.id = s.portal_user_id
            {$whereSql}
            GROUP BY DATE(s.submitted_at)
            ORDER BY day ASC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        $out = [];
        foreach ($rows as $r) {
            $out[] = [
                'day' => $r['day'],
                'total' => (int) $r['total'],
            ];
        }

        return $out;
    }

    /**
     * Ranking de usuários por quantidade de submissões no período.
     * @param array<string,mixed> $filters
     * @return array<int,array<string,mixed>>
     */
    public function rankingUsers(array $filters): array
    {
        ['where' => $whereSql, 'params' => $params] = $this->buildWhere($filters);

        $sql = "
            SELECT 
                u.id,
                u.full_name,
                u.email,
                COUNT(*) AS total
            FROM portal_submissions s
            JOIN portal_users u ON u.id = s.portal_user_id
            {$whereSql}
            GROUP BY u.id, u.full_name, u.email
            ORDER BY total DESC, u.full_name ASC
            LIMIT 20
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Listagem de submissões filtradas (para tabela e export).
     * @param array<string,mixed> $filters
     * @return array<int,array<string,mixed>>
     */
    public function listSubmissions(array $filters): array
    {
        ['where' => $whereSql, 'params' => $params] = $this->buildWhere($filters);

        $sql = "
            SELECT 
                s.id,
                s.title,
                s.status,
                s.submitted_at AS created_at,
                s.status_updated_at AS updated_at,
                u.full_name AS user_name,
                u.email     AS user_email
            FROM portal_submissions s
            JOIN portal_users u ON u.id = s.portal_user_id
            {$whereSql}
            ORDER BY s.submitted_at DESC, s.id DESC
            LIMIT 500
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}
