<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Repository\AuditLogRepository;
use PDO;

final class MySqlAuditLogRepository implements AuditLogRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function create(array $data): int
    {
        $sql = 'INSERT INTO audit_logs
                (occurred_at, actor_type, actor_id, actor_name,
                 action, ip_address, user_agent,
                 context_type, context_id, summary, details)
                VALUES
                (:occurred_at, :actor_type, :actor_id, :actor_name,
                 :action, :ip_address, :user_agent,
                 :context_type, :context_id, :summary, :details)';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':occurred_at' => $data['occurred_at'] ?? date('Y-m-d H:i:s'),
            ':actor_type' => $data['actor_type'],
            ':actor_id' => $data['actor_id'] ?? null,
            ':actor_name' => $data['actor_name'] ?? null,
            ':action' => $data['action'],
            ':ip_address' => $data['ip_address'] ?? null,
            ':user_agent' => $data['user_agent'] ?? null,
            ':context_type' => $data['context_type'] ?? null,
            ':context_id' => $data['context_id'] ?? null,
            ':summary' => $data['summary'] ?? null,
            ':details' => isset($data['details'])
                ? json_encode($data['details'], JSON_UNESCAPED_UNICODE)
                : null,
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function paginate(int $page = 1, int $perPage = 50, ?array $filters = null): array
    {
        $filters ??= [];
        $where = [];
        $params = [];

        if (!empty($filters['actor_type'])) {
            $where[] = 'actor_type = :actor_type';
            $params[':actor_type'] = $filters['actor_type'];
        }

        if (!empty($filters['action'])) {
            $where[] = 'action = :action';
            $params[':action'] = $filters['action'];
        }

        if (!empty($filters['context_type'])) {
            $where[] = 'context_type = :context_type';
            $params[':context_type'] = $filters['context_type'];
        }

        if (!empty($filters['search'])) {
            $where[] = '(summary LIKE :search1 OR actor_name LIKE :search2)';
            $params[':search1'] = '%' . $filters['search'] . '%';
            $params[':search2'] = '%' . $filters['search'] . '%';
        }

        $whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

        $offset = ($page - 1) * $perPage;

        $sqlCount = "SELECT COUNT(*) FROM audit_logs {$whereSql}";
        $stmt = $this->pdo->prepare($sqlCount);
        $stmt->execute($params);
        $total = (int) $stmt->fetchColumn();

        $sql = "SELECT *
                FROM audit_logs
                {$whereSql}
                ORDER BY occurred_at DESC, id DESC
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

    public function latest(int $limit = 5): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT *
         FROM audit_logs
         ORDER BY occurred_at DESC, id DESC
         LIMIT :l'
        );
        $stmt->bindValue(':l', $limit, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
