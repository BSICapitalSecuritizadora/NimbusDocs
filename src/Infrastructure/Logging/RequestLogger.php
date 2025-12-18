<?php

declare(strict_types=1);

namespace App\Infrastructure\Logging;

use Monolog\Logger;
use App\Support\Session;

/**
 * Logger avançado para requisições HTTP
 * Rastreia IP, endpoint, tempo de resposta, status code, usuário, etc.
 */
final class RequestLogger
{
    private Logger $logger;
    private float $startTime;
    private string $requestId;
    private string $clientIp;
    private string $method;
    private string $uri;
    private ?string $user;
    private array $context;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
        $this->startTime = microtime(true);
        $this->requestId = bin2hex(random_bytes(8));
        $this->clientIp = $this->getClientIp();
        $this->method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $this->uri = $_SERVER['REQUEST_URI'] ?? '/';
        $this->user = Session::has('admin_user') ? Session::get('admin_user')['email'] ?? 'unknown' : (Session::has('portal_user') ? 'portal_user_' . Session::get('portal_user')['id'] : null);
        $this->context = [];
    }

    /**
     * Extrai IP real do cliente
     * Considera proxies como Cloudflare, load balancers, etc.
     */
    private function getClientIp(): string
    {
        // CloudFlare
        if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            return trim(explode(',', $_SERVER['HTTP_CF_CONNECTING_IP'])[0]);
        }

        // X-Forwarded-For (proxies)
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return trim(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0]);
        }

        // X-Forwarded-For (AWS)
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        }

        // IP remoto direto
        return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    }

    /**
     * Log de requisição bem-sucedida
     */
    public function logSuccess(int $statusCode = 200): void
    {
        $duration = round((microtime(true) - $this->startTime) * 1000, 2);

        $context = [
            'request_id' => $this->requestId,
            'ip' => $this->clientIp,
            'method' => $this->method,
            'uri' => $this->uri,
            'status_code' => $statusCode,
            'duration_ms' => $duration,
            'user' => $this->user,
        ];

        // Adiciona context customizado
        $context = array_merge($context, $this->context);

        // Warning se for lento (>2s)
        if ($duration > 2000) {
            $this->logger->warning("Requisição lenta detectada", $context);
        } else {
            $this->logger->info("Requisição bem-sucedida", $context);
        }

        // Salva em arquivo de requisições
        $this->saveToRequestLog($context, 'success');
    }

    /**
     * Log de erro
     */
    public function logError(string $error, int $statusCode = 500, ?\Throwable $exception = null): void
    {
        $duration = round((microtime(true) - $this->startTime) * 1000, 2);

        $context = [
            'request_id' => $this->requestId,
            'ip' => $this->clientIp,
            'method' => $this->method,
            'uri' => $this->uri,
            'status_code' => $statusCode,
            'duration_ms' => $duration,
            'error' => $error,
            'user' => $this->user,
        ];

        if ($exception) {
            $context['exception'] = [
                'class' => get_class($exception),
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ];
        }

        // Adiciona context customizado
        $context = array_merge($context, $this->context);

        $this->logger->error("Erro em requisição", $context);
        $this->saveToRequestLog($context, 'error');
    }

    /**
     * Log de acesso negado (401, 403)
     */
    public function logUnauthorized(int $statusCode = 403, string $reason = ''): void
    {
        $duration = round((microtime(true) - $this->startTime) * 1000, 2);

        $context = [
            'request_id' => $this->requestId,
            'ip' => $this->clientIp,
            'method' => $this->method,
            'uri' => $this->uri,
            'status_code' => $statusCode,
            'duration_ms' => $duration,
            'reason' => $reason,
            'user' => $this->user,
        ];

        $this->logger->warning("Acesso negado", $context);
        $this->saveToRequestLog($context, 'unauthorized');
    }

    /**
     * Adiciona contexto customizado
     */
    public function addContext(string $key, mixed $value): void
    {
        $this->context[$key] = $value;
    }

    /**
     * Salva requisição em arquivo JSON (para dashboard)
     */
    private function saveToRequestLog(array $data, string $type): void
    {
        $logDir = dirname(__DIR__, 3) . '/storage/logs';
        $logFile = $logDir . '/requests.jsonl';

        // Cria diretório se não existir
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        // Adiciona timestamp e tipo
        $data['timestamp'] = date('Y-m-d H:i:s');
        $data['type'] = $type;

        // Salva como JSONL (uma linha por requisição)
        file_put_contents($logFile, json_encode($data) . "\n", FILE_APPEND);

        // Mantém apenas os últimos 10.000 logs (~2MB)
        $this->rotateRequestLog($logFile);
    }

    /**
     * Rotação de arquivo de requisições
     */
    private function rotateRequestLog(string $logFile): void
    {
        if (!file_exists($logFile)) {
            return;
        }

        $lines = file($logFile);
        if (count($lines) > 10000) {
            // Remove os primeiros 2000 logs
            $lines = array_slice($lines, 2000);
            file_put_contents($logFile, implode('', $lines));
        }
    }

    /**
     * Retorna requisições recentes para dashboard
     */
    public static function getRecentRequests(int $limit = 100): array
    {
        $logFile = dirname(__DIR__, 3) . '/storage/logs/requests.jsonl';

        if (!file_exists($logFile)) {
            return [];
        }

        $lines = file($logFile);
        $requests = [];

        // Lê de trás para frente (mais recentes primeiro)
        foreach (array_reverse($lines) as $line) {
            if (trim($line) === '') {
                continue;
            }

            try {
                $data = json_decode(trim($line), true, 512, JSON_THROW_ON_ERROR);
                $requests[] = $data;

                if (count($requests) >= $limit) {
                    break;
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        return $requests;
    }

    /**
     * Retorna estatísticas de requisições
     */
    public static function getStatistics(int $lastHours = 24): array
    {
        $logFile = dirname(__DIR__, 3) . '/storage/logs/requests.jsonl';

        if (!file_exists($logFile)) {
            return [
                'total_requests' => 0,
                'success' => 0,
                'errors' => 0,
                'unauthorized' => 0,
                'avg_duration_ms' => 0,
                'slow_requests' => 0,
                'top_endpoints' => [],
                'top_ips' => [],
            ];
        }

        $lines = file($logFile);
        $now = time();
        $timeWindow = $now - ($lastHours * 3600);

        $stats = [
            'total_requests' => 0,
            'success' => 0,
            'errors' => 0,
            'unauthorized' => 0,
            'total_duration' => 0,
            'slow_requests' => 0,
            'endpoints' => [],
            'ips' => [],
        ];

        foreach ($lines as $line) {
            if (trim($line) === '') {
                continue;
            }

            try {
                $data = json_decode(trim($line), true, JSON_THROW_ON_ERROR);

                // Verifica se está dentro da janela de tempo
                if (strtotime($data['timestamp'] ?? 'now') < $timeWindow) {
                    continue;
                }

                $stats['total_requests']++;

                // Conta por tipo
                match ($data['type'] ?? '') {
                    'success' => $stats['success']++,
                    'error' => $stats['errors']++,
                    'unauthorized' => $stats['unauthorized']++,
                    default => null,
                };

                // Acumula duração
                $stats['total_duration'] += $data['duration_ms'] ?? 0;

                // Conta requisições lentas (>2s)
                if (($data['duration_ms'] ?? 0) > 2000) {
                    $stats['slow_requests']++;
                }

                // Endpoints
                $uri = $data['uri'] ?? 'unknown';
                $stats['endpoints'][$uri] = ($stats['endpoints'][$uri] ?? 0) + 1;

                // IPs
                $ip = $data['ip'] ?? 'unknown';
                $stats['ips'][$ip] = ($stats['ips'][$ip] ?? 0) + 1;
            } catch (\Exception $e) {
                continue;
            }
        }

        // Ordena top endpoints e IPs
        arsort($stats['endpoints']);
        arsort($stats['ips']);

        $stats['top_endpoints'] = array_slice($stats['endpoints'], 0, 10, true);
        $stats['top_ips'] = array_slice($stats['ips'], 0, 10, true);

        // Calcula média de duração
        $stats['avg_duration_ms'] = $stats['total_requests'] > 0
            ? round($stats['total_duration'] / $stats['total_requests'], 2)
            : 0;

        return [
            'total_requests' => $stats['total_requests'],
            'success' => $stats['success'],
            'errors' => $stats['errors'],
            'unauthorized' => $stats['unauthorized'],
            'avg_duration_ms' => $stats['avg_duration_ms'],
            'slow_requests' => $stats['slow_requests'],
            'top_endpoints' => $stats['top_endpoints'],
            'top_ips' => $stats['top_ips'],
        ];
    }

    /**
     * Retorna alertas (requisições com erro ou muito lentas)
     */
    public static function getAlerts(int $limit = 50): array
    {
        $logFile = dirname(__DIR__, 3) . '/storage/logs/requests.jsonl';

        if (!file_exists($logFile)) {
            return [];
        }

        $lines = file($logFile);
        $alerts = [];

        // Lê de trás para frente (mais recentes primeiro)
        foreach (array_reverse($lines) as $line) {
            if (trim($line) === '') {
                continue;
            }

            try {
                $data = json_decode(trim($line), true, JSON_THROW_ON_ERROR);

                // Considera alerta se for erro ou muito lento
                if ($data['type'] === 'error' || $data['type'] === 'unauthorized' || ($data['duration_ms'] ?? 0) > 5000) {
                    $alerts[] = $data;

                    if (count($alerts) >= $limit) {
                        break;
                    }
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        return $alerts;
    }

    /**
     * Retorna ID da requisição
     */
    public function getRequestId(): string
    {
        return $this->requestId;
    }
}
