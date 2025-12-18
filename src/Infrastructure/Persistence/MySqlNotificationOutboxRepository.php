<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use PDO;
use Psr\Log\LoggerInterface;

final class MySqlNotificationOutboxRepository
{
    public function __construct(private PDO $pdo, private ?LoggerInterface $logger = null) {}

    /** @return array<int,array<string,mixed>> */
    public function search(array $filters = [], int $limit = 200): array
    {
        $where  = [];
        $params = [];

        if (!empty($filters['status'])) {
            $where[] = 'status = :status';
            $params[':status'] = $filters['status'];
        }

        if (!empty($filters['type'])) {
            $where[] = 'type = :type';
            $params[':type'] = $filters['type'];
        }

        if (!empty($filters['email'])) {
            $where[] = 'recipient_email LIKE :email';
            $params[':email'] = '%' . $filters['email'] . '%';
        }

        if (!empty($filters['from_date'])) {
            $where[] = 'DATE(created_at) >= :from';
            $params[':from'] = $filters['from_date'];
        }

        if (!empty($filters['to_date'])) {
            $where[] = 'DATE(created_at) <= :to';
            $params[':to'] = $filters['to_date'];
        }

        $whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

        $sql = "
            SELECT *
            FROM notification_outbox
            {$whereSql}
            ORDER BY created_at DESC, id DESC
            LIMIT {$limit}
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /** @return array<int,string> */
    public function distinctTypes(): array
    {
        $stmt = $this->pdo->query("
            SELECT DISTINCT type
            FROM notification_outbox
            ORDER BY type ASC
        ");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        return array_values(array_map(fn($r) => (string)$r['type'], $rows));
    }

    /** @return array<int,string> */
    public function distinctStatuses(): array
    {
        // enums conhecidos, mas melhor ler do banco:
        $stmt = $this->pdo->query("
            SELECT DISTINCT status
            FROM notification_outbox
            ORDER BY status ASC
        ");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        return array_values(array_map(fn($r) => (string)$r['status'], $rows));
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM notification_outbox WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function cancel(int $id): bool
    {
        // Só cancela se ainda estiver pendente
        $stmt = $this->pdo->prepare("
            UPDATE notification_outbox
            SET status = 'CANCELLED'
            WHERE id = :id AND status IN ('PENDING')
        ");
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount() > 0;
    }

    public function reprocess(int $id): bool
    {
        // Reprocessa falhas: volta para pending e limpa erro/agenda
        $stmt = $this->pdo->prepare("
            UPDATE notification_outbox
            SET status = 'PENDING',
                next_attempt_at = NULL,
                last_error = NULL
            WHERE id = :id AND status IN ('FAILED')
        ");
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount() > 0;
    }

    public function resetAttemptsAndReprocess(int $id): bool
    {
        // Mais forte: zera attempts também (útil se você aumentou max_attempts)
        $stmt = $this->pdo->prepare("
            UPDATE notification_outbox
            SET status = 'PENDING',
                attempts = 0,
                next_attempt_at = NULL,
                last_error = NULL
            WHERE id = :id AND status IN ('FAILED','CANCELLED')
        ");
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount() > 0;
    }

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
     * Verifica se já existe notificação de token expirado para o mesmo token e destinatário
     * em estados que indiquem processamento/entrega (PENDING, SENDING, SENT).
     * @param int $tokenId
     * @param string $recipientEmail
     * @param int|null $windowHours Janela de tempo em horas (null = sem limite)
     */
    public function existsTokenExpiredFor(int $tokenId, string $recipientEmail, ?int $windowHours = null): bool
    {
        $params = [
            ':email' => $recipientEmail,
            ':tokenId' => (string)$tokenId,
        ];

        $timeCondition = '';
        if ($windowHours !== null && $windowHours > 0) {
            $timeCondition = " AND created_at > DATE_SUB(NOW(), INTERVAL :windowHours HOUR)";
            $params[':windowHours'] = $windowHours;
        }

        $sql = "SELECT 1
                FROM notification_outbox
                WHERE type = 'TOKEN_EXPIRED'
                  AND recipient_email = :email
                  AND JSON_UNQUOTE(JSON_EXTRACT(payload_json, '$.token.id')) = :tokenId
                  AND status IN ('PENDING','SENDING','SENT')
                  {$timeCondition}
                LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return (bool)$stmt->fetchColumn();
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
        // Rescue interno: libera jobs travados em 'SENDING' há X minutos (default 30)
        // Pode ser ajustado via OUTBOX_RESCUE_MINUTES ou NOTIFICATION_WORKER_RESCUE_MINUTES
        $minutes = 30;
        if (isset($_ENV['OUTBOX_RESCUE_MINUTES'])) {
            $minutes = (int)$_ENV['OUTBOX_RESCUE_MINUTES'];
        } elseif (isset($_ENV['NOTIFICATION_WORKER_RESCUE_MINUTES'])) {
            $minutes = (int)$_ENV['NOTIFICATION_WORKER_RESCUE_MINUTES'];
        }
        if ($minutes < 1) {
            $minutes = 30;
        }
        // INTERVAL não aceita bind param, então interpolamos valor saneado
        $logRescue = true;
        if (isset($_ENV['OUTBOX_RESCUE_LOG'])) {
            $logRescue = filter_var($_ENV['OUTBOX_RESCUE_LOG'], FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);
            $logRescue = ($logRescue === null) ? true : $logRescue; // default true when invalid
        }

        try {
            $rescued = $this->pdo->exec(
                "UPDATE notification_outbox\n" .
                "SET status='PENDING'\n" .
                "WHERE status='SENDING'\n" .
                "  AND created_at < (NOW() - INTERVAL {$minutes} MINUTE)"
            );
            if ($rescued && $this->logger && $logRescue) {
                $this->logger->info('notification_outbox rescue executed', [
                    'rescued' => $rescued,
                    'minutes' => $minutes,
                ]);
            }
        } catch (\Throwable $e) {
            // silencioso: não impede o claim
            if ($this->logger && $logRescue) {
                $this->logger->warning('notification_outbox rescue failed', [
                    'error' => $e->getMessage(),
                    'minutes' => $minutes,
                ]);
            }
        }

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

}
