<?php
declare(strict_types=1);

namespace App\Infrastructure\Logging;

use PDO;

final class PortalAccessLogger
{
    public function __construct(private PDO $pdo) {}

    public function log(
        int $portalUserId,
        string $action,
        ?string $resourceType = null,
        ?int $resourceId = null
    ): void {
        $ip        = $_SERVER['REMOTE_ADDR']      ?? null;
        $userAgent = $_SERVER['HTTP_USER_AGENT']  ?? null;

        $sql = "INSERT INTO portal_access_log
                   (portal_user_id, action, resource_type, resource_id, ip_address, user_agent)
                VALUES
                   (:uid, :action, :rtype, :rid, :ip, :ua)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':uid'   => $portalUserId,
            ':action'=> $action,
            ':rtype' => $resourceType,
            ':rid'   => $resourceId,
            ':ip'    => $ip,
            ':ua'    => $userAgent ? substr($userAgent, 0, 255) : null,
        ]);
    }
}
