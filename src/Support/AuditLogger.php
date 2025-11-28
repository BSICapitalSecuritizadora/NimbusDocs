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
}
