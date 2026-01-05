<?php

declare(strict_types=1);

namespace App\Infrastructure\Logging;

use PDO;

final class AdminAuditLogger
{
    public function __construct(private PDO $pdo) {}

    public function log(
        ?int $adminUserId,
        string $action,
        ?string $entityType = null,
        ?int $entityId = null,
        array $details = []
    ): void {
        $ip  = $_SERVER['REMOTE_ADDR']     ?? null;
        $ua  = $_SERVER['HTTP_USER_AGENT'] ?? null;

        $sql = "INSERT INTO admin_audit_log
                (admin_user_id, action, entity_type, entity_id,
                 ip_address, user_agent, details)
                VALUES
                (:admin_user_id, :action, :entity_type, :entity_id,
                 :ip_address, :user_agent, :details)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':admin_user_id' => $adminUserId,
            ':action'        => $action,
            ':entity_type'   => $entityType,
            ':entity_id'     => $entityId,
            ':ip_address'    => $ip,
            ':user_agent'    => $ua,
            ':details'       => $details ? json_encode($details, JSON_UNESCAPED_UNICODE) : null,
        ]);
    }
}
