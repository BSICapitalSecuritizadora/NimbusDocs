<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Controle de concorrência de downloads.
 * Limita o número de downloads simultâneos por identificador (IP/usuário).
 */
final class DownloadConcurrencyGuard
{
    /**
     * Limite padrão de downloads simultâneos por identificador.
     */
    private const DEFAULT_MAX_CONCURRENT = 3;

    /**
     * TTL padrão do slot em segundos (10 minutos).
     * Após esse tempo, o slot é liberado automaticamente mesmo sem release().
     */
    private const DEFAULT_SLOT_TTL = 600;

    private FileCache $cache;

    private int $maxConcurrent;

    private int $slotTtl;

    /**
     * @param string|null $cacheDir Diretório de cache (null = usa padrão)
     * @param int $maxConcurrent Máximo de downloads simultâneos por identificador
     * @param int $slotTtl TTL do slot em segundos
     */
    public function __construct(
        ?string $cacheDir = null,
        int $maxConcurrent = self::DEFAULT_MAX_CONCURRENT,
        int $slotTtl = self::DEFAULT_SLOT_TTL
    ) {
        $cacheDir = $cacheDir ?? dirname(__DIR__, 2) . '/var/cache/download_concurrency';
        $this->cache = new FileCache($cacheDir, $slotTtl);
        $this->maxConcurrent = $maxConcurrent;
        $this->slotTtl = $slotTtl;
    }

    /**
     * Tenta adquirir um slot de download para o identificador.
     *
     * @param string $identifier Identificador único (IP, user ID, etc)
     * @return bool True se conseguiu adquirir slot, False se limite atingido
     */
    public function acquire(string $identifier): bool
    {
        $key = $this->getCacheKey($identifier);
        $current = (int) $this->cache->get($key, 0);

        if ($current >= $this->maxConcurrent) {
            return false; // Limite atingido
        }

        // Incrementa contador
        $this->cache->increment($key, 1, $this->slotTtl);

        return true;
    }

    /**
     * Libera um slot de download do identificador.
     *
     * @param string $identifier Identificador único
     */
    public function release(string $identifier): void
    {
        $key = $this->getCacheKey($identifier);
        $current = (int) $this->cache->get($key, 0);

        if ($current <= 1) {
            // Se for 1 ou menos, remove a chave
            $this->cache->delete($key);
        } else {
            // Decrementa
            $this->cache->increment($key, -1, $this->slotTtl);
        }
    }

    /**
     * Retorna o número atual de downloads ativos para o identificador.
     *
     * @param string $identifier Identificador único
     * @return int Número de downloads ativos
     */
    public function getActiveCount(string $identifier): int
    {
        $key = $this->getCacheKey($identifier);

        return (int) $this->cache->get($key, 0);
    }

    /**
     * Verifica se o identificador pode iniciar um novo download.
     *
     * @param string $identifier Identificador único
     * @return bool True se pode iniciar download
     */
    public function canAcquire(string $identifier): bool
    {
        return $this->getActiveCount($identifier) < $this->maxConcurrent;
    }

    /**
     * Retorna o limite máximo de downloads simultâneos.
     */
    public function getMaxConcurrent(): int
    {
        return $this->maxConcurrent;
    }

    /**
     * Gera a chave de cache para o identificador.
     */
    private function getCacheKey(string $identifier): string
    {
        return 'dl_concurrent:' . md5($identifier);
    }
}
