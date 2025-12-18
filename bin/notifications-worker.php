#!/usr/bin/env php
<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';
$config = require __DIR__ . '/../bootstrap/app.php';

use App\Infrastructure\Persistence\MySqlNotificationOutboxRepository;

$outbox = new MySqlNotificationOutboxRepository($config['pdo']);
$mail   = $config['mail']; // GraphMailService

// Rescue: libera jobs travados em 'SENDING' há X minutos (default 30)
// Configurável via .env: OUTBOX_RESCUE_MINUTES ou NOTIFICATION_WORKER_RESCUE_MINUTES
// (usa o primeiro que estiver definido)
\assert(isset($_ENV));
\assert(is_array($_ENV));
$rescueMinutes = (int)($_ENV['OUTBOX_RESCUE_MINUTES']
    ?? $_ENV['NOTIFICATION_WORKER_RESCUE_MINUTES']
    ?? 30);
if ($rescueMinutes < 1) {
    $rescueMinutes = 30; // sanidade mínima
}
try {
    $rescued = $config['pdo']->exec(
        "UPDATE notification_outbox\n" .
        "SET status='PENDING'\n" .
        "WHERE status='SENDING'\n" .
        "  AND created_at < (NOW() - INTERVAL {$rescueMinutes} MINUTE)"
    );
    if ($rescued) {
        echo "[worker] rescued {$rescued} stuck SENDING job(s) older than {$rescueMinutes}m\n";
    }
} catch (\Throwable $e) {
    // não impede o processamento do lote
    echo "[worker] rescue step failed: {$e->getMessage()}\n";
}

$batch = $outbox->claimBatch(20);

if (!$batch) {
    echo "[worker] no pending jobs\n";
    exit(0);
}

foreach ($batch as $job) {
    $id = (int)$job['id'];

    $attempts = (int)$job['attempts'] + 1;

    try {
        $payload = json_decode((string)$job['payload_json'], true) ?: [];

        // render do template
        ob_start();
        extract($payload);
        require __DIR__ . '/../src/Presentation/Email/' . $job['template'] . '.php';
        $html = (string)ob_get_clean();

        $mail->sendMail(
            to: $job['recipient_email'],
            subject: $job['subject'],
            htmlBody: $html
        );

        $outbox->markSent($id);
        echo "[worker] sent #{$id} to {$job['recipient_email']}\n";
    } catch (\Throwable $e) {
        // backoff simples: 1m, 5m, 15m, 60m, 6h
        $backoff = [60, 300, 900, 3600, 21600];
        $idx = min($attempts - 1, count($backoff) - 1);
        $next = (new DateTimeImmutable('now'))
            ->modify('+' . $backoff[$idx] . ' seconds')
            ->format('Y-m-d H:i:s');

        $outbox->markFailed($id, $e->getMessage(), $attempts, $next);

        echo "[worker] failed #{$id} attempt {$attempts}: {$e->getMessage()}\n";
    }
}
