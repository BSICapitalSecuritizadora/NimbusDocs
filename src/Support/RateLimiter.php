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

    /**
     * Verifica se está dentro do limite
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
            if ($record['expires_at'] < $now) {
                unset($data[$key]);
            }
        }
        
        $key = hash('sha256', $identifier);
        
        if (!isset($data[$key])) {
            return true;
        }
        
        return $data[$key]['count'] < $maxAttempts;
    }
    
    /**
     * Registra uma tentativa falha
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
     * Reseta as tentativas para um identificador
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
     * Obtém informações de tempo restante
     */
    public static function getTimeRemaining(string $identifier): int
    {
        $data = self::loadData();
        $key = hash('sha256', $identifier);
        
        if (!isset($data[$key])) {
            return 0;
        }
        
        $remaining = $data[$key]['expires_at'] - time();
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
}
