<?php

declare(strict_types=1);

namespace App\Support;

use PDO;

final class AuditLogger
{
    public function __construct(private PDO $pdo) {}

    public function log(
        string $actorType,
        ?int $actorId,
        string $action,
        ?string $targetType = null,
        ?int $targetId = null,
        array $context = []
    ): void {
        $sql = "INSERT INTO audit_logs (actor_type, actor_id, action, target_type, target_id, ip_address, user_agent, details)
                VALUES (:actor_type, :actor_id, :action, :target_type, :target_id, :ip, :ua, :details)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':actor_type'  => $actorType,
            ':actor_id'    => $actorId,
            ':action'      => $action,
            ':target_type' => $targetType,
            ':target_id'   => $targetId,
            ':ip'          => $_SERVER['REMOTE_ADDR'] ?? null,
            ':ua'          => mb_substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255),
            ':details'     => $context ? json_encode($context, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR) : null,
        ]);
    }

    /**
     * Busca logs de auditoria por contexto (target_type + target_id)
     * 
     * @return array Lista de logs ordenados por data decrescente
     */
    public function getByContext(string $targetType, int $targetId, int $limit = 20): array
    {
        $sql = "SELECT al.*, 
                       COALESCE(au.name, pu.full_name, 'Sistema') AS actor_name
                FROM audit_logs al
                LEFT JOIN admin_users au ON al.actor_type = 'ADMIN' AND al.actor_id = au.id
                LEFT JOIN portal_users pu ON al.actor_type = 'PORTAL_USER' AND al.actor_id = pu.id
                WHERE al.target_type = :target_type AND al.target_id = :target_id
                ORDER BY al.occurred_at DESC
                LIMIT :limit";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':target_type', $targetType, \PDO::PARAM_STR);
        $stmt->bindValue(':target_id', $targetId, \PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
