#!/usr/bin/env php
<?php
/**
 * Generic Queue Worker
 * 
 * Processes jobs from the `jobs` table.
 * 
 * Usage:
 *   php bin/queue-worker.php [options]
 * 
 * Options:
 *   --queue=name   Specify queue name (default: default)
 *   --once         Process one job and exit
 *   --help         Show this help
 */

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Infrastructure\Queue\DatabaseQueue;
use App\Application\Queue\JobInterface;
use Psr\Log\LoggerInterface;

// Bootstrap
$config = require __DIR__ . '/../bootstrap/app.php';

/** @var PDO $pdo */
$pdo = $config['pdo'];
/** @var LoggerInterface|null $logger */
$logger = $config['logger'] ?? null;

$queueDriver = new DatabaseQueue($pdo);

// Parse Args
$args = getopt('', ['queue::', 'once', 'help']);
$queueName = $args['queue'] ?? 'default';
$runOnce   = isset($args['once']);

if (isset($args['help'])) {
    echo "Usage: php bin/queue-worker.php [--queue=default] [--once]\n";
    exit(0);
}

$running = true;

// Signal Handling
if (function_exists('pcntl_signal')) {
    pcntl_async_signals(true);
    $shutdown = function () use (&$running) {
        echo "[queue-worker] Shutdown requested...\n";
        $running = false;
    };
    pcntl_signal(SIGTERM, $shutdown);
    pcntl_signal(SIGINT, $shutdown);
}

echo "[queue-worker] Started processing queue: '{$queueName}'\n";

while ($running) {
    try {
        $jobRow = $queueDriver->pop($queueName);

        if (!$jobRow) {
            if ($runOnce) {
                echo "[queue-worker] No jobs found.\n";
                break;
            }
            sleep(3); // Wait before polling again
            continue;
        }

        $jobId = (int)$jobRow['id'];
        $payload = json_decode($jobRow['payload'], true, 512, JSON_THROW_ON_ERROR);
        $jobClass = $payload['job'];
        $jobData = $payload['data'] ?? [];

        echo "[queue-worker] Processing Job #{$jobId} [" . $jobClass . "]... ";

        // Instantiate and Run
        if (!class_exists($jobClass)) {
            throw new RuntimeException("Job class not found: {$jobClass}");
        }

        $jobInstance = new $jobClass(...$jobData); // Constructor injection of data

        if (!$jobInstance instanceof JobInterface) {
            throw new RuntimeException("Job {$jobClass} must implement JobInterface");
        }

        $jobInstance->handle();

        $queueDriver->delete($jobId);
        echo "DONE\n";

    } catch (\Throwable $e) {
        echo "FAILED: " . $e->getMessage() . "\n";
        
        if (isset($jobRow)) {
            $attempts = (int)$jobRow['attempts'];
            $max = (int)$jobRow['max_attempts'];
            
            if ($attempts >= $max) {
                 // Fail permanently (maybe log to failed_jobs here in future)
                 echo "[queue-worker] Job #{$jobRow['id']} failed permanently after {$max} attempts.\n";
                 // For now, we delete it or we could leave it. 
                 // Let's release it with a very long available_at to "bury" it? 
                 // Or better yet, delete it to stop loop, and log error clearly.
                 if ($logger) {
                     $logger->error("Job failed permanently", ['id' => $jobRow['id'], 'error' => $e->getMessage()]);
                 }
                 // Delete to clean up
                 $queueDriver->delete((int)$jobRow['id']);
            } else {
                // Release with backoff
                $backoff = 10 * $attempts; // 10s, 20s, 30s
                $queueDriver->release((int)$jobRow['id'], $backoff);
                echo "[queue-worker] Released Job #{$jobRow['id']} for retry in {$backoff}s\n";
            }
        }
    }

    if ($runOnce) break;
}
