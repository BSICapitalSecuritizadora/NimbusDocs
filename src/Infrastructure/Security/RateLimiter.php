<?php

declare(strict_types=1);

namespace App\Infrastructure\Security;

use App\Support\FileCache;

class RateLimiter
{
    private FileCache $cache;

    public function __construct(FileCache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Verifica se a chave excedeu o limite de requisições.
     * Incrementa o contador automaticamente.
     *
     * @param string $key Identificador único (ex: IP)
     * @param int $maxAttempts Número máximo de tentativas
     * @param int $decaySeconds Janela de tempo em segundos
     * @return bool True se excedeu o limite, False se permitido
     */
    public function tooManyAttempts(string $key, int $maxAttempts, int $decaySeconds = 60): bool
    {
        $cacheKey = 'rate_limit:' . $key;
        
        // Verifica valor atual sem incrementar
        $current = (int)$this->cache->get($cacheKey, 0);
        
        if ($current >= $maxAttempts) {
            return true;
        }

        // Incrementa (cria se não existir, com TTL correto)
        $this->cache->increment($cacheKey, 1, $decaySeconds);

        return false;
    }

    /**
     * Retorna quantos segundos faltam para liberar o bloqueio.
     * (Simulação aproximada, já que FileCache simples não expõe TTL restante facilmente,
     *  mas retornaremos o decaySeconds como fallback conservador se bloqueado)
     */
    public function availableIn(string $key): int
    {
        // Como o FileCache não tem método ttl(), retornamos um valor padrão ou 
        // precisaríamos implementar ttl() no FileCache. Para MVP, retornamos 60s.
        return 60;
    }
}
