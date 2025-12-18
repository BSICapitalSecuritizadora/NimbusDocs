<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use PDO;

final class MySqlNotificationOutboxRepository
{
    public function __construct(private PDO $pdo) {}

    public function enqueue(array $row): int
    {
        $sql = "
            INSERT INTO notification_outbox
            (type, recipient_email, recipient_name, subject, template, payload_json, max_attempts, next_attempt_at)
            VALUES
            (:type, :email, :name, :subject, :template, :payload, :max_attempts, :next_attempt_at)
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':type'           => $row['type'],
            ':email'          => $row['recipient_email'],
            ':name'           => $row['recipient_name'] ?? null,
            ':subject'        => $row['subject'],
            ':template'       => $row['template'],
            ':payload'        => $row['payload_json'],
            ':max_attempts'   => $row['max_attempts'] ?? 5,
            ':next_attempt_at' => $row['next_attempt_at'] ?? null,
        ]);

        return (int)$this->pdo->lastInsertId();
    }

        /**
         * Lista itens da fila com filtros simples.
         * @param array{status?:string,recipient?:string,type?:string} $filters
         * @return array<int,array<string,mixed>>
         */
        public function list(array $filters = [], int $limit = 100, int $offset = 0): array
        {
            $where = [];
            $params = [];

            if (!empty($filters['status'])) {
                $where[] = 'status = :status';
                $params[':status'] = $filters['status'];
            }

            if (!empty($filters['recipient'])) {
                $where[] = 'recipient_email LIKE :recipient';
                $params[':recipient'] = '%' . $filters['recipient'] . '%';
            }

            if (!empty($filters['type'])) {
                $where[] = 'type LIKE :type';
                $params[':type'] = '%' . $filters['type'] . '%';
            }

            $whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

            $sql = "
                SELECT *
                FROM notification_outbox
                {$whereSql}
                ORDER BY created_at DESC
                LIMIT :limit OFFSET :offset
            ";

            $stmt = $this->pdo->prepare($sql);
            foreach ($params as $k => $v) {
                $stmt->bindValue($k, $v);
            }
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        }

    /**
     * Busca lote pronto para envio e trava via status SENDING.
     * @return array<int,array<string,mixed>>
     */
    public function claimBatch(int $limit = 20): array
    {
        // Pega PENDING com next_attempt_at null ou <= now
        $sqlSelect = "
            SELECT *
            FROM notification_outbox
            WHERE status = 'PENDING'
              AND (next_attempt_at IS NULL OR next_attempt_at <= NOW())
            ORDER BY created_at ASC
            LIMIT {$limit}
        ";
        $rows = $this->pdo->query($sqlSelect)->fetchAll(PDO::FETCH_ASSOC) ?: [];

        if (!$rows) {
            return [];
        }

        // Marca como SENDING (trava simples)
        $ids = array_map(fn($r) => (int)$r['id'], $rows);
        $in  = implode(',', $ids);

        $this->pdo->exec("
            UPDATE notification_outbox
            SET status = 'SENDING'
            WHERE id IN ({$in}) AND status = 'PENDING'
        ");

        // Recarrega somente os que viraram SENDING
        $stmt = $this->pdo->query("
            SELECT *
            FROM notification_outbox
            WHERE id IN ({$in}) AND status = 'SENDING'
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function markSent(int $id): void
    {
        $stmt = $this->pdo->prepare("
            UPDATE notification_outbox
            SET status = 'SENT', sent_at = NOW()
            WHERE id = :id
        ");
        $stmt->execute([':id' => $id]);
    }

    public function markFailed(int $id, string $error, int $attempts, ?string $nextAttemptAt): void
    {
        $stmt = $this->pdo->prepare("
            UPDATE notification_outbox
            SET status = :status,
                attempts = :attempts,
                last_error = :error,
                next_attempt_at = :next_attempt_at
            WHERE id = :id
        ");

        $status = ($attempts >= 5) ? 'FAILED' : 'PENDING';

        $stmt->execute([
            ':id'              => $id,
            ':status'          => $status,
            ':attempts'        => $attempts,
            ':error'           => $error,
            ':next_attempt_at' => $nextAttemptAt,
        ]);
    }

    public function reprocess(int $id): void
    {
        $stmt = $this->pdo->prepare("
            UPDATE notification_outbox
            SET status = 'PENDING', attempts = 0, next_attempt_at = NULL, last_error = NULL
            WHERE id = :id AND status = 'FAILED'
        ");
        $stmt->execute([':id' => $id]);
    }

    public function cancel(int $id): void
    {
        $stmt = $this->pdo->prepare("
            UPDATE notification_outbox
            SET status = 'CANCELLED', next_attempt_at = NULL
            WHERE id = :id AND status = 'PENDING'
        ");
        $stmt->execute([':id' => $id]);
    }
}
