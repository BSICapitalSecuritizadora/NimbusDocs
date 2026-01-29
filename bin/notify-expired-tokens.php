<?php

declare(strict_types=1);

/**
 * Script para notificar usuários sobre tokens expirados.
 * Executar via cron ou agendador de tarefas:
 * 
 * Linux/Mac:
 *   0 9 * * * /usr/bin/php /path/to/NimbusDocs/bin/notify-expired-tokens.php
 * 
 * Windows (Agendador de Tarefas):
 *   C:\xampp\php\php.exe C:\xampp\htdocs\NimbusDocs\bin\notify-expired-tokens.php
 */

require __DIR__ . '/../vendor/autoload.php';

$config = require __DIR__ . '/../bootstrap/app.php';
$pdo = $config['pdo'];

// Importa dependências
use App\Infrastructure\Persistence\MySqlPortalAccessTokenRepository;
use App\Infrastructure\Persistence\MySqlPortalUserRepository;
use App\Aplication\Service\NotificationService;

try {
    echo "[" . date('Y-m-d H:i:s') . "] Iniciando notificação de tokens expirados...\n";

    // Instancia repositórios
    $tokenRepo = new MySqlPortalAccessTokenRepository($pdo);
    $userRepo = new MySqlPortalUserRepository($pdo);

    // Busca tokens que expiraram nas últimas 24 horas (não usados, expirados)
    $sql = "SELECT t.id, t.code, t.expires_at, t.portal_user_id, u.full_name, u.email
            FROM portal_access_tokens t
            JOIN portal_users u ON t.portal_user_id = u.id
            WHERE t.status = 'PENDING'
              AND t.used_at IS NULL
              AND t.expires_at < NOW()
              AND t.expires_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
            ORDER BY t.expires_at DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $expiredTokens = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    echo "Encontrados " . count($expiredTokens) . " tokens expirados nas últimas 24 horas.\n";

    if (empty($expiredTokens)) {
        echo "[" . date('Y-m-d H:i:s') . "] Nenhum token expirado para notificar.\n";
        exit(0);
    }

    // Instancia serviço de notificação (se disponível)
    if (!isset($config['notification'])) {
        echo "[ERRO] NotificationService não configurado em bootstrap/app.php\n";
        exit(1);
    }

    $notificationService = $config['notification'];
    $notified = 0;
    $failed = 0;

    foreach ($expiredTokens as $tokenRecord) {
        try {
            $portalUser = [
                'id'        => (int)$tokenRecord['portal_user_id'],
                'full_name' => $tokenRecord['full_name'],
                'email'     => $tokenRecord['email'],
            ];

            $token = [
                'id'         => (int)$tokenRecord['id'],
                'code'       => $tokenRecord['code'],
                'expires_at' => $tokenRecord['expires_at'],
                'portal_user_id' => (int)$tokenRecord['portal_user_id'],
            ];

            // Envia notificação
            $notificationService->notifyTokenExpired($portalUser, $token);

            echo "[OK] Notificação enviada para {$portalUser['email']} (Token: {$tokenRecord['code']})\n";
            $notified++;

            // Log no audit
            if (isset($config['audit'])) {
                $config['audit']->log(
                    null, // sem actor específico (job automático)
                    'token.expired.notification.sent',
                    'portal_access_token',
                    (int)$tokenRecord['id'],
                    ['user_email' => $portalUser['email']]
                );
            }
        } catch (\Throwable $t) {
            echo "[ERRO] Falha ao notificar {$tokenRecord['email']}: " . $t->getMessage() . "\n";
            $failed++;
        }
    }

    echo "\n[" . date('Y-m-d H:i:s') . "] Conclusão:\n";
    echo "  - Notificados com sucesso: $notified\n";
    echo "  - Falhas: $failed\n";
    echo "  - Total processado: " . count($expiredTokens) . "\n";

    exit($failed > 0 ? 1 : 0);

} catch (\Throwable $e) {
    echo "[ERRO CRÍTICO] " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(2);
}
