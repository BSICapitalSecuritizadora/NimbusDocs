<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use PDO;

/**
 * Repository para comentários de submissões.
 */
final class MySqlSubmissionCommentRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    /**
     * Adiciona um comentário em uma submissão.
     */
    public function add(array $data): int
    {
        $sql = "
            INSERT INTO submission_comments 
            (submission_id, author_type, author_id, comment, is_internal, requires_action)
            VALUES (:submission_id, :author_type, :author_id, :comment, :is_internal, :requires_action)
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':submission_id'  => $data['submission_id'],
            ':author_type'    => $data['author_type'],    // 'ADMIN' ou 'PORTAL_USER'
            ':author_id'      => $data['author_id'],
            ':comment'        => $data['comment'],
            ':is_internal'    => (int)($data['is_internal'] ?? false),
            ':requires_action'=> (int)($data['requires_action'] ?? false),
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Retorna comentários de uma submissão.
     *
     * @param bool $includeInternal Se true, inclui comentários internos (apenas admin)
     */
    public function getBySubmission(int $submissionId, bool $includeInternal = false): array
    {
        $sql = "
            SELECT sc.*,
                   CASE 
                       WHEN sc.author_type = 'ADMIN' THEN au.name 
                       WHEN sc.author_type = 'PORTAL_USER' THEN pu.full_name 
                       ELSE 'Sistema'
                   END as author_name
            FROM submission_comments sc
            LEFT JOIN admin_users au ON sc.author_type = 'ADMIN' AND sc.author_id = au.id
            LEFT JOIN portal_users pu ON sc.author_type = 'PORTAL_USER' AND sc.author_id = pu.id
            WHERE sc.submission_id = :submission_id
        ";

        if (!$includeInternal) {
            $sql .= " AND sc.is_internal = 0";
        }

        $sql .= " ORDER BY sc.created_at ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':submission_id' => $submissionId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Registra histórico de mudança de status.
     */
    public function addStatusHistory(array $data): int
    {
        $sql = "
            INSERT INTO submission_status_history 
            (submission_id, old_status, new_status, changed_by_type, changed_by_id, reason)
            VALUES (:submission_id, :old_status, :new_status, :changed_by_type, :changed_by_id, :reason)
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':submission_id'   => $data['submission_id'],
            ':old_status'      => $data['old_status'] ?? null,
            ':new_status'      => $data['new_status'],
            ':changed_by_type' => $data['changed_by_type'] ?? 'SYSTEM',
            ':changed_by_id'   => $data['changed_by_id'] ?? null,
            ':reason'          => $data['reason'] ?? null,
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Retorna histórico de status de uma submissão.
     */
    public function getStatusHistory(int $submissionId): array
    {
        $sql = "
            SELECT sh.*,
                   CASE 
                       WHEN sh.changed_by_type = 'ADMIN' THEN au.name 
                       WHEN sh.changed_by_type = 'PORTAL_USER' THEN pu.full_name 
                       ELSE 'Sistema'
                   END as changed_by_name
            FROM submission_status_history sh
            LEFT JOIN admin_users au ON sh.changed_by_type = 'ADMIN' AND sh.changed_by_id = au.id
            LEFT JOIN portal_users pu ON sh.changed_by_type = 'PORTAL_USER' AND sh.changed_by_id = pu.id
            WHERE sh.submission_id = :submission_id
            ORDER BY sh.created_at ASC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':submission_id' => $submissionId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Atualiza status da submissão com histórico.
     */
    public function updateSubmissionStatus(
        int $submissionId,
        string $newStatus,
        string $changedByType,
        ?int $changedById,
        ?string $reason = null,
        ?int $correctionDays = null
    ): bool {
        // Pega status atual
        $stmt = $this->pdo->prepare("SELECT status FROM portal_submissions WHERE id = :id");
        $stmt->execute([':id' => $submissionId]);
        $current = $stmt->fetch(PDO::FETCH_ASSOC);
        $oldStatus = $current['status'] ?? null;

        // Atualiza submissão
        $sql = "UPDATE portal_submissions SET status = :status";
        $params = [':status' => $newStatus, ':id' => $submissionId];

        if ($newStatus === 'NEEDS_CORRECTION') {
            $sql .= ", correction_count = correction_count + 1, last_correction_request_at = NOW()";
            
            if ($correctionDays) {
                $sql .= ", correction_deadline = DATE_ADD(NOW(), INTERVAL :days DAY)";
                $params[':days'] = $correctionDays;
            }
        }

        $sql .= " WHERE id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        // Registra histórico
        $this->addStatusHistory([
            'submission_id'   => $submissionId,
            'old_status'      => $oldStatus,
            'new_status'      => $newStatus,
            'changed_by_type' => $changedByType,
            'changed_by_id'   => $changedById,
            'reason'          => $reason,
        ]);

        return $stmt->rowCount() > 0;
    }
}
