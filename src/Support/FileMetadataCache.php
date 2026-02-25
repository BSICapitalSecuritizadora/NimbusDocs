<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Cache de metadata de arquivos.
 * Evita queries repetidas ao banco para informações que mudam raramente.
 */
final class FileMetadataCache
{
    /**
     * TTL padrão em segundos (1 hora).
     */
    private const DEFAULT_TTL = 3600;

    private FileCache $cache;

    private int $ttl;

    /**
     * @param string|null $cacheDir Diretório de cache (null = usa padrão)
     * @param int $ttl TTL em segundos
     */
    public function __construct(
        ?string $cacheDir = null,
        int $ttl = self::DEFAULT_TTL
    ) {
        $cacheDir = $cacheDir ?? dirname(__DIR__, 2) . '/var/cache/file_metadata';
        $this->cache = new FileCache($cacheDir, $ttl);
        $this->ttl = $ttl;
    }

    /**
     * Obtém metadata do cache.
     *
     * @param string $type Tipo do arquivo (e.g., 'submission_file', 'general_document')
     * @param int $id ID do arquivo
     * @return array|null Metadata ou null se não encontrado/expirado
     */
    public function get(string $type, int $id): ?array
    {
        $key = $this->getCacheKey($type, $id);
        $data = $this->cache->get($key);

        return is_array($data) ? $data : null;
    }

    /**
     * Armazena metadata no cache.
     *
     * @param string $type Tipo do arquivo
     * @param int $id ID do arquivo
     * @param array $metadata Metadata do arquivo
     * @param int|null $ttl TTL customizado em segundos (null = usa padrão)
     */
    public function set(string $type, int $id, array $metadata, ?int $ttl = null): void
    {
        $key = $this->getCacheKey($type, $id);
        $this->cache->set($key, $metadata, $ttl ?? $this->ttl);
    }

    /**
     * Obtém ou define metadata usando callback.
     * Se não estiver em cache, executa o callback e armazena o resultado.
     *
     * @param string $type Tipo do arquivo
     * @param int $id ID do arquivo
     * @param callable $callback Função que retorna metadata (executada se cache miss)
     * @param int|null $ttl TTL customizado
     * @return array|null Metadata ou null se callback retornar null
     */
    public function remember(string $type, int $id, callable $callback, ?int $ttl = null): ?array
    {
        $cached = $this->get($type, $id);

        if ($cached !== null) {
            return $cached;
        }

        $data = $callback();

        if ($data !== null && is_array($data)) {
            $this->set($type, $id, $data, $ttl);
        }

        return $data;
    }

    /**
     * Invalida o cache de um arquivo específico.
     *
     * @param string $type Tipo do arquivo
     * @param int $id ID do arquivo
     */
    public function invalidate(string $type, int $id): void
    {
        $key = $this->getCacheKey($type, $id);
        $this->cache->delete($key);
    }

    /**
     * Invalida todo o cache de um tipo de arquivo.
     * Nota: Esta operação pode ser lenta se houver muitos arquivos.
     */
    public function invalidateType(string $type): void
    {
        // O FileCache não suporta invalidação por prefixo,
        // então fazemos garbage collection geral
        $this->cache->gc();
    }

    /**
     * Limpa todo o cache de metadata.
     */
    public function clear(): void
    {
        $this->cache->clear();
    }

    /**
     * Gera a chave de cache para o arquivo.
     */
    private function getCacheKey(string $type, int $id): string
    {
        return 'file_meta:' . $type . ':' . $id;
    }
}
