<?php

declare(strict_types=1);

namespace App\Presentation\Controller;

use PDO;
use Throwable;

final class HealthController
{
    private PDO $pdo;
    private string $storagePath;

    public function __construct(array $config)
    {
        $this->pdo = $config['pdo'];
        // Define caminho do storage para check de escrita (fallback para base path se não definido)
        $this->storagePath = __DIR__ . '/../../../storage';
    }

    public function check(): void
    {
        $start = microtime(true);
        $status = 'ok';
        $checks = [];
        $httpCode = 200;

        // 1. Database Check
        try {
            $dbStart = microtime(true);
            $this->pdo->query('SELECT 1');
            $checks['database'] = [
                'status' => 'ok',
                'latency_ms' => round((microtime(true) - $dbStart) * 1000, 2)
            ];
        } catch (Throwable $e) {
            $status = 'error';
            $httpCode = 503;
            $checks['database'] = [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }

        // 2. Storage Write Check
        try {
            $diskStart = microtime(true);
            if (!is_dir($this->storagePath)) {
                throw new \RuntimeException('Storage directory not found');
            }
            
            $testFile = $this->storagePath . '/health_check_' . uniqid() . '.tmp';
            if (file_put_contents($testFile, 'test') === false) {
                throw new \RuntimeException('Cannot write to storage');
            }
            unlink($testFile);

            $checks['storage'] = [
                'status' => 'ok',
                'latency_ms' => round((microtime(true) - $diskStart) * 1000, 2),
                'free_space_mb' => round(disk_free_space($this->storagePath) / 1024 / 1024, 2)
            ];
        } catch (Throwable $e) {
            $status = 'error';
            $httpCode = 503;
            $checks['storage'] = [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }

        // 3. System Info
        $checks['system'] = [
            'php_version' => PHP_VERSION,
            'memory_usage_mb' => round(memory_get_usage(true) / 1024 / 1024, 2),
            'uptime_s' => (int)(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'])
        ];

        // Response
        http_response_code($httpCode);
        header('Content-Type: application/json');
        
        $response = [
            'status' => $status,
            'timestamp' => date('c'),
            'total_latency_ms' => round((microtime(true) - $start) * 1000, 2),
            'environment' => $_ENV['APP_ENV'] ?? 'production',
            'checks' => $checks
        ];

        echo json_encode($response, JSON_PRETTY_PRINT);
    }
}
