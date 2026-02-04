#!/usr/bin/env php
<?php
/**
 * Notifications Worker - Production Ready
 * 
 * Processa a fila de notificações (outbox) com:
 * - Loop contínuo com graceful shutdown
 * - Logs estruturados (PSR-3)
 * - Backoff exponencial
 * - Métricas em memória
 * 
 * Uso:
 *   php bin/notifications-worker.php          # Loop contínuo (daemon)
 *   php bin/notifications-worker.php --once   # Execução única
 *   php bin/notifications-worker.php --help   # Ajuda
 * 
 * Variáveis de ambiente:
 *   WORKER_SLEEP_SECONDS=5       # Intervalo entre ciclos (padrão: 5)
 *   WORKER_BATCH_SIZE=20         # Tamanho do lote (padrão: 20)
 *   OUTBOX_RESCUE_MINUTES=30     # Libera jobs travados após N minutos
 */

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Infrastructure\Persistence\MySqlNotificationOutboxRepository;
use Psr\Log\LoggerInterface;

// Parse argumentos
$args = getopt('', ['once', 'help']);
$runOnce = isset($args['once']);
$showHelp = isset($args['help']);

if ($showHelp) {
    echo <<<HELP
NimbusDocs Notifications Worker

Usage:
  php bin/notifications-worker.php [options]

Options:
  --once    Processa um lote e encerra
  --help    Mostra esta mensagem

Environment:
  WORKER_SLEEP_SECONDS   Intervalo entre ciclos (padrão: 5)
  WORKER_BATCH_SIZE      Tamanho do lote (padrão: 20)
  OUTBOX_RESCUE_MINUTES  Libera jobs travados (padrão: 30)

HELP;
    exit(0);
}

// Bootstrap
$config = require __DIR__ . '/../bootstrap/app.php';

/** @var LoggerInterface|null $logger */
$logger = $config['logger'] ?? null;

$outbox = new MySqlNotificationOutboxRepository($config['pdo'], $logger);
$mail   = $config['mail']; // GraphMailService

// Configurações
$sleepSeconds = (int)($_ENV['WORKER_SLEEP_SECONDS'] ?? 5);
$batchSize    = (int)($_ENV['WORKER_BATCH_SIZE'] ?? 20);

if ($sleepSeconds < 1) $sleepSeconds = 5;
if ($batchSize < 1) $batchSize = 20;

// Métricas em memória
$metrics = [
    'started_at'      => date('Y-m-d H:i:s'),
    'cycles'          => 0,
    'jobs_processed'  => 0,
    'jobs_sent'       => 0,
    'jobs_failed'     => 0,
    'total_time_ms'   => 0,
];

// Graceful shutdown
$running = true;

if (function_exists('pcntl_signal')) {
    pcntl_async_signals(true);
    
    $shutdown = function (int $signal) use (&$running, $logger) {
        $running = false;
        $sigName = match($signal) {
            SIGTERM => 'SIGTERM',
            SIGINT  => 'SIGINT',
            default => "Signal {$signal}"
        };
        
        if ($logger) {
            $logger->info('Worker shutdown requested', ['signal' => $sigName]);
        }
        echo "[worker] Shutdown requested ({$sigName}), finishing current batch...\n";
    };
    
    pcntl_signal(SIGTERM, $shutdown);
    pcntl_signal(SIGINT, $shutdown);
}

// Log de início
$startMsg = sprintf(
    '[worker] Started at %s | Mode: %s | Batch: %d | Sleep: %ds',
    $metrics['started_at'],
    $runOnce ? 'once' : 'daemon',
    $batchSize,
    $sleepSeconds
);
echo $startMsg . "\n";

if ($logger) {
    $logger->info('Notifications worker started', [
        'mode'       => $runOnce ? 'once' : 'daemon',
        'batch_size' => $batchSize,
        'sleep_sec'  => $sleepSeconds,
        'pid'        => getmypid(),
    ]);
}

// Loop principal
do {
    $metrics['cycles']++;
    $cycleStart = microtime(true);
    
    $batch = $outbox->claimBatch($batchSize);
    
    if (!$batch) {
        if ($runOnce) {
            echo "[worker] No pending jobs\n";
            break;
        }
        
        // Debug a cada 12 ciclos (1 min com sleep=5)
        if ($metrics['cycles'] % 12 === 0) {
            echo sprintf(
                "[worker] Idle | Cycles: %d | Sent: %d | Failed: %d\n",
                $metrics['cycles'],
                $metrics['jobs_sent'],
                $metrics['jobs_failed']
            );
        }
        
        sleep($sleepSeconds);
        continue;
    }
    
    foreach ($batch as $job) {
        if (!$running) break;
        
        $id = (int)$job['id'];
        $attempts = (int)$job['attempts'] + 1;
        $jobStart = microtime(true);
        
        $metrics['jobs_processed']++;
        
        try {
            $payload = json_decode((string)$job['payload_json'], true) ?: [];
            
            // Render do template
            ob_start();
            extract($payload);
            $templatePath = __DIR__ . '/../src/Presentation/Email/' . $job['template'] . '.php';
            
            if (!file_exists($templatePath)) {
                throw new RuntimeException("Template not found: {$job['template']}");
            }
            
            require $templatePath;
            $html = (string)ob_get_clean();
            
            // Envia email com correlation_id para rastreamento
            $correlationId = $job['correlation_id'] ?? null;
            $mail->sendMail(
                to: $job['recipient_email'],
                subject: $job['subject'],
                htmlBody: $html,
                correlationId: $correlationId
            );
            
            $outbox->markSent($id);
            $metrics['jobs_sent']++;
            
            $elapsed = round((microtime(true) - $jobStart) * 1000);
            $metrics['total_time_ms'] += $elapsed;
            
            echo sprintf(
                "[worker] ✓ Sent #%d to %s (%dms)\n",
                $id,
                $job['recipient_email'],
                $elapsed
            );
            
            if ($logger) {
                $logger->info('Notification sent', [
                    'job_id'    => $id,
                    'type'      => $job['type'] ?? 'unknown',
                    'recipient' => $job['recipient_email'],
                    'elapsed_ms'=> $elapsed,
                ]);
            }
            
        } catch (\Throwable $e) {
            $metrics['jobs_failed']++;
            
            // Backoff exponencial: 1m, 5m, 15m, 60m, 6h
            $backoff = [60, 300, 900, 3600, 21600];
            $idx = min($attempts - 1, count($backoff) - 1);
            $next = (new DateTimeImmutable('now'))
                ->modify('+' . $backoff[$idx] . ' seconds')
                ->format('Y-m-d H:i:s');
            
            $outbox->markFailed($id, $e->getMessage(), $attempts, $next);
            
            $maxAttempts = (int)($job['max_attempts'] ?? 5);
            $isFinal = $attempts >= $maxAttempts;
            
            echo sprintf(
                "[worker] %s #%d attempt %d/%d: %s\n",
                $isFinal ? '✗ FAILED' : '⚠ Retry',
                $id,
                $attempts,
                $maxAttempts,
                mb_substr($e->getMessage(), 0, 100)
            );
            
            if ($logger) {
                $logLevel = $isFinal ? 'error' : 'warning';
                $logger->$logLevel('Notification failed', [
                    'job_id'      => $id,
                    'type'        => $job['type'] ?? 'unknown',
                    'recipient'   => $job['recipient_email'],
                    'attempt'     => $attempts,
                    'max_attempts'=> $maxAttempts,
                    'next_retry'  => $isFinal ? null : $next,
                    'error'       => $e->getMessage(),
                    'error_class' => get_class($e),
                ]);
            }
        }
    }
    
    if ($runOnce) break;
    
    // Pequena pausa entre lotes
    if ($running) {
        usleep(500000); // 500ms
    }
    
} while ($running);

// Métricas finais
$avgTime = $metrics['jobs_sent'] > 0 
    ? round($metrics['total_time_ms'] / $metrics['jobs_sent']) 
    : 0;

$summary = sprintf(
    "[worker] Stopped | Cycles: %d | Processed: %d | Sent: %d | Failed: %d | Avg: %dms",
    $metrics['cycles'],
    $metrics['jobs_processed'],
    $metrics['jobs_sent'],
    $metrics['jobs_failed'],
    $avgTime
);
echo $summary . "\n";

if ($logger) {
    $logger->info('Notifications worker stopped', [
        'cycles'         => $metrics['cycles'],
        'jobs_processed' => $metrics['jobs_processed'],
        'jobs_sent'      => $metrics['jobs_sent'],
        'jobs_failed'    => $metrics['jobs_failed'],
        'avg_time_ms'    => $avgTime,
        'uptime_seconds' => time() - strtotime($metrics['started_at']),
    ]);
}

exit(0);
