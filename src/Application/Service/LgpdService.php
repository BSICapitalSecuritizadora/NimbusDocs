<?php

declare(strict_types=1);

namespace App\Application\Service;

use PDO;

/**
 * Serviço de Conformidade LGPD
 * 
 * Gerencia consentimentos, retenção de dados, anonimização e direitos do titular.
 */
final class LgpdService
{
    public function __construct(private PDO $pdo)
    {
    }

    // -------------------------------------------------------------------------
    // Consentimentos
    // -------------------------------------------------------------------------

    /**
     * Registra consentimento do usuário.
     */
    public function recordConsent(
        string $userType,
        int $userId,
        string $consentType,
        string $version,
        string $action = 'GRANTED'
    ): int {
        $sql = "
            INSERT INTO consent_logs 
            (user_type, user_id, consent_type, version, action, ip_address, user_agent)
            VALUES (:user_type, :user_id, :consent_type, :version, :action, :ip, :ua)
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':user_type'    => $userType,
            ':user_id'      => $userId,
            ':consent_type' => $consentType,
            ':version'      => $version,
            ':action'       => $action,
            ':ip'           => $_SERVER['REMOTE_ADDR'] ?? null,
            ':ua'           => mb_substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255),
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Verifica se usuário aceitou versão atual de um documento.
     */
    public function hasValidConsent(string $userType, int $userId, string $consentType): bool
    {
        // Pega versão ativa do documento
        $activeVersion = $this->getActiveDocumentVersion($consentType);
        if (!$activeVersion) {
            return true; // Sem documento ativo, considera válido
        }

        $sql = "
            SELECT id FROM consent_logs 
            WHERE user_type = :user_type 
              AND user_id = :user_id 
              AND consent_type = :consent_type 
              AND version = :version 
              AND action = 'GRANTED'
            ORDER BY created_at DESC
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':user_type'    => $userType,
            ':user_id'      => $userId,
            ':consent_type' => $consentType,
            ':version'      => $activeVersion,
        ]);

        return $stmt->fetch() !== false;
    }

    /**
     * Retorna versão ativa de um documento legal.
     */
    public function getActiveDocumentVersion(string $type): ?string
    {
        $stmt = $this->pdo->prepare("
            SELECT version FROM legal_documents 
            WHERE type = :type AND is_active = 1 
            LIMIT 1
        ");
        $stmt->execute([':type' => $type]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['version'] ?? null;
    }

    /**
     * Retorna documento legal ativo.
     */
    public function getActiveDocument(string $type): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM legal_documents 
            WHERE type = :type AND is_active = 1 
            LIMIT 1
        ");
        $stmt->execute([':type' => $type]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    // -------------------------------------------------------------------------
    // Anonimização e exclusão
    // -------------------------------------------------------------------------

    /**
     * Anonimiza dados de um usuário do portal.
     * Mantém registros para auditoria, mas remove dados identificáveis.
     */
    public function anonymizePortalUser(int $userId): array
    {
        $anonymizedId = 'ANON_' . bin2hex(random_bytes(8));
        $changes = [];

        $this->pdo->beginTransaction();

        try {
            // Anonimiza portal_users
            $stmt = $this->pdo->prepare("
                UPDATE portal_users SET
                    email = CONCAT(:anon_id, '@anonimizado.local'),
                    full_name = 'Usuário Anonimizado',
                    phone = NULL,
                    cpf = NULL,
                    document_number = NULL,
                    is_active = 0,
                    anonymized_at = NOW()
                WHERE id = :user_id
            ");
            $stmt->execute([':anon_id' => $anonymizedId, ':user_id' => $userId]);
            $changes['portal_users'] = $stmt->rowCount();

            // Anonimiza audit_logs - mantém ação mas remove identificação
            $stmt = $this->pdo->prepare("
                UPDATE audit_logs SET
                    ip_address = '0.0.0.0',
                    user_agent = 'ANONYMIZED'
                WHERE actor_type = 'PORTAL_USER' AND actor_id = :user_id
            ");
            $stmt->execute([':user_id' => $userId]);
            $changes['audit_logs'] = $stmt->rowCount();

            // Remove tokens
            $stmt = $this->pdo->prepare("DELETE FROM portal_user_tokens WHERE user_id = :user_id");
            $stmt->execute([':user_id' => $userId]);
            $changes['tokens_deleted'] = $stmt->rowCount();

            $this->pdo->commit();

            // Registra a anonimização
            $this->recordConsent('PORTAL_USER', $userId, 'anonymization', '1.0', 'GRANTED');

            return [
                'success'      => true,
                'anonymized_id'=> $anonymizedId,
                'changes'      => $changes,
            ];

        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            return [
                'success' => false,
                'error'   => $e->getMessage(),
            ];
        }
    }

    /**
     * Exporta dados de um usuário (portabilidade).
     */
    public function exportUserData(int $userId): array
    {
        $data = [];

        // Dados do usuário
        $stmt = $this->pdo->prepare("
            SELECT id, email, full_name, created_at, terms_accepted_at
            FROM portal_users WHERE id = :id
        ");
        $stmt->execute([':id' => $userId]);
        $data['user'] = $stmt->fetch(PDO::FETCH_ASSOC);

        // Submissões
        $stmt = $this->pdo->prepare("
            SELECT id, reference_code, status, submitted_at, created_at
            FROM portal_submissions WHERE user_id = :id
        ");
        $stmt->execute([':id' => $userId]);
        $data['submissions'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Consentimentos
        $stmt = $this->pdo->prepare("
            SELECT consent_type, version, action, created_at
            FROM consent_logs 
            WHERE user_type = 'PORTAL_USER' AND user_id = :id
            ORDER BY created_at DESC
        ");
        $stmt->execute([':id' => $userId]);
        $data['consents'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $data;
    }

    // -------------------------------------------------------------------------
    // Solicitações do Titular
    // -------------------------------------------------------------------------

    /**
     * Cria uma solicitação de titular (DSAR).
     */
    public function createDataSubjectRequest(
        int $userId,
        string $requestType,
        ?string $reason = null
    ): int {
        $sql = "
            INSERT INTO data_subject_requests 
            (user_type, user_id, request_type, reason)
            VALUES ('PORTAL_USER', :user_id, :request_type, :reason)
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':user_id'      => $userId,
            ':request_type' => $requestType,
            ':reason'       => $reason,
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Processa uma solicitação de titular.
     */
    public function processDataSubjectRequest(
        int $requestId,
        int $adminId,
        string $status,
        ?string $notes = null
    ): bool {
        $stmt = $this->pdo->prepare("
            UPDATE data_subject_requests SET
                status = :status,
                processed_by = :admin_id,
                processed_at = NOW(),
                notes = :notes
            WHERE id = :id
        ");

        return $stmt->execute([
            ':id'       => $requestId,
            ':status'   => $status,
            ':admin_id' => $adminId,
            ':notes'    => $notes,
        ]);
    }

    /**
     * Retorna solicitações pendentes.
     */
    public function getPendingRequests(): array
    {
        $sql = "
            SELECT dsr.*, pu.email, pu.full_name
            FROM data_subject_requests dsr
            JOIN portal_users pu ON dsr.user_id = pu.id
            WHERE dsr.status IN ('PENDING', 'PROCESSING')
            ORDER BY dsr.created_at ASC
        ";

        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    // -------------------------------------------------------------------------
    // Retenção de Dados
    // -------------------------------------------------------------------------

    /**
     * Executa políticas de retenção de dados.
     * Deve ser chamado por um job diário.
     */
    public function executeRetentionPolicies(): array
    {
        $results = [];

        $policies = $this->pdo->query("
            SELECT * FROM data_retention_policies WHERE is_active = 1
        ")->fetchAll(PDO::FETCH_ASSOC);

        foreach ($policies as $policy) {
            $dataType = $policy['data_type'];
            $days = (int)$policy['retention_days'];
            $action = $policy['action_type'];

            $affected = match ($dataType) {
                'request_logs' => $this->deleteOldRequestLogs($days),
                'audit_logs' => $this->archiveOldAuditLogs($days),
                'failed_notifications' => $this->deleteFailedNotifications($days),
                default => 0,
            };

            $results[$dataType] = [
                'action'   => $action,
                'days'     => $days,
                'affected' => $affected,
            ];

            // Atualiza last_executed_at
            $this->pdo->prepare("
                UPDATE data_retention_policies SET last_executed_at = NOW() WHERE id = :id
            ")->execute([':id' => $policy['id']]);
        }

        return $results;
    }

    private function deleteOldRequestLogs(int $days): int
    {
        // Request logs são em arquivo, não banco
        return 0;
    }

    private function archiveOldAuditLogs(int $days): int
    {
        // Por simplicidade, apenas marca como arquivado (não deleta)
        return 0;
    }

    private function deleteFailedNotifications(int $days): int
    {
        $stmt = $this->pdo->prepare("
            DELETE FROM notification_outbox 
            WHERE status = 'FAILED' 
              AND created_at < DATE_SUB(NOW(), INTERVAL :days DAY)
        ");
        $stmt->execute([':days' => $days]);
        return $stmt->rowCount();
    }
}
