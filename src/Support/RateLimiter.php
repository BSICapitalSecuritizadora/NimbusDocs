<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Proteção contra brute force com rate limiting
 * Rastreia tentativas de login falhadas por IP/identificador
 */
final class RateLimiter
{
    private const SESSION_KEY = 'rate_limiter';
    private const STORAGE_FILE = __DIR__ . '/../../storage/rate_limiter.json';

    private int $maxAttempts;
    private int $windowSeconds;
    private string $storageFile;

    /**
     * Instância opcional para uso com arquivos e limites customizados (compatível com testes).
     */
    public function __construct(int $maxAttempts = 5, int $windowSeconds = 900, ?string $storageFile = null)
    {
        $this->maxAttempts = $maxAttempts;
        $this->windowSeconds = $windowSeconds;
        $this->storageFile = $storageFile ?? self::STORAGE_FILE;
    }

    /**
     * Verifica se está dentro do limite (API estática original)
     * 
     * @param string $identifier IP ou identificador único
     * @param int $maxAttempts Máximo de tentativas
     * @param int $windowSeconds Janela de tempo em segundos
     */
    public static function isAllowed(string $identifier, int $maxAttempts = 5, int $windowSeconds = 900): bool
    {
        $now = time();
        $data = self::loadData();
        
        // Limpar entradas antigas
        foreach ($data as $key => $record) {
            if (($record['expires_at'] ?? 0) < $now) {
                unset($data[$key]);
            }
        }
        
        $key = hash('sha256', $identifier);
        
        if (!isset($data[$key])) {
            return true;
        }
        
        return ($data[$key]['count'] ?? 0) < $maxAttempts;
    }
    
    /**
     * Registra uma tentativa falha (API estática original)
     */
    public static function recordAttempt(string $identifier, int $windowSeconds = 900): void
    {
        $now = time();
        $data = self::loadData();
        $key = hash('sha256', $identifier);
        
        if (!isset($data[$key])) {
            $data[$key] = [
                'count' => 0,
                'expires_at' => $now + $windowSeconds,
            ];
        }
        
        $data[$key]['count']++;
        $data[$key]['expires_at'] = $now + $windowSeconds;
        
        self::saveData($data);
    }
    
    /**
     * Reseta as tentativas para um identificador (API estática original)
     */
    public static function reset(string $identifier): void
    {
        $data = self::loadData();
        $key = hash('sha256', $identifier);
        
        if (isset($data[$key])) {
            unset($data[$key]);
            self::saveData($data);
        }
    }
    
    /**
     * Obtém informações de tempo restante (API estática original)
     */
    public static function getTimeRemaining(string $identifier): int
    {
        $data = self::loadData();
        $key = hash('sha256', $identifier);
        
        if (!isset($data[$key])) {
            return 0;
        }
        
        $remaining = ($data[$key]['expires_at'] ?? time()) - time();
        return max(0, $remaining);
    }
    
    private static function loadData(): array
    {
        if (!file_exists(self::STORAGE_FILE)) {
            return [];
        }
        
        $json = file_get_contents(self::STORAGE_FILE);
        return json_decode($json, true) ?? [];
    }
    
    private static function saveData(array $data): void
    {
        $dir = dirname(self::STORAGE_FILE);
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        
        file_put_contents(
            self::STORAGE_FILE,
            json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
            LOCK_EX
        );
    }

    // ---------------- Instance API (para compatibilidade com testes) ----------------

    public function check(string $identifier): bool
    {
        $now = time();
        $data = $this->loadInstanceData();

        // Limpa expirados
        foreach ($data as $id => $record) {
            if (($record['expires_at'] ?? 0) < $now) {
                unset($data[$id]);
            }
        }

        // Persist cleanup so remaining()/increment() read a clean state
        $this->saveInstanceData($data);

        if (!isset($data[$identifier])) {
            return true;
        }
        return ((int)($data[$identifier]['count'] ?? 0)) < $this->maxAttempts;
    }

    public function increment(string $identifier): void
    {
        $now = time();
        $data = $this->loadInstanceData();

        if (!isset($data[$identifier])) {
            $data[$identifier] = [
                'count' => 0,
                'expires_at' => $now + $this->windowSeconds,
            ];
        }
        $data[$identifier]['count'] = ((int)($data[$identifier]['count'] ?? 0)) + 1;
        $data[$identifier]['expires_at'] = $now + $this->windowSeconds;

        $this->saveInstanceData($data);
    }

    public function remaining(string $identifier): int
    {
        $now = time();
        $data = $this->loadInstanceData();

        // Remove expirados para cálculo correto
        $changed = false;
        foreach ($data as $id => $record) {
            if (($record['expires_at'] ?? 0) < $now) {
                unset($data[$id]);
                $changed = true;
            }
        }
        if ($changed) {
            $this->saveInstanceData($data);
        }

        $count = (int)($data[$identifier]['count'] ?? 0);
        $remaining = $this->maxAttempts - $count;
        return max(0, $remaining);
    }

    public function resetInstance(string $identifier): void
    {
        $data = $this->loadInstanceData();
        if (isset($data[$identifier])) {
            unset($data[$identifier]);
            $this->saveInstanceData($data);
        }
    }

    private function loadInstanceData(): array
    {
        if (!file_exists($this->storageFile)) {
            return [];
        }
        $json = file_get_contents($this->storageFile);
        return json_decode($json, true) ?? [];
    }

    private function saveInstanceData(array $data): void
    {
        $dir = dirname($this->storageFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        file_put_contents(
            $this->storageFile,
            json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
            LOCK_EX
        );
    }
}
