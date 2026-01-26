<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Cache simples baseado em arquivos com TTL.
 * Implementa PSR-16 SimpleCache interface parcialmente.
 */
class FileCache
{
    private string $cacheDir;
    private int $defaultTtl;

    /**
     * @param string $cacheDir Diretório para armazenar arquivos de cache
     * @param int $defaultTtl TTL padrão em segundos (default: 24 horas)
     */
    public function __construct(string $cacheDir, int $defaultTtl = 86400)
    {
        $this->cacheDir = rtrim($cacheDir, '/\\');
        $this->defaultTtl = $defaultTtl;

        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0775, true);
        }
    }

    /**
     * Obtém um valor do cache.
     *
     * @param string $key Chave do cache
     * @param mixed $default Valor padrão se não encontrado
     * @return mixed Valor do cache ou $default
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $file = $this->getFilePath($key);

        if (!file_exists($file)) {
            return $default;
        }

        $content = file_get_contents($file);
        
        if ($content === false) {
            return $default;
        }

        $data = unserialize($content);

        // Verifica se expirou
        if ($data['expires_at'] !== null && $data['expires_at'] < time()) {
            $this->delete($key);
            return $default;
        }

        return $data['value'];
    }

    /**
     * Armazena um valor no cache.
     *
     * @param string $key Chave do cache
     * @param mixed $value Valor a armazenar
     * @param int|null $ttl Tempo de vida em segundos (null = usar default)
     * @return bool Sucesso da operação
     */
    public function set(string $key, mixed $value, ?int $ttl = null): bool
    {
        $file = $this->getFilePath($key);
        $ttl = $ttl ?? $this->defaultTtl;

        $data = [
            'value' => $value,
            'expires_at' => $ttl > 0 ? time() + $ttl : null,
            'created_at' => time(),
        ];

        return file_put_contents($file, serialize($data), LOCK_EX) !== false;
    }

    /**
     * Remove um valor do cache.
     *
     * @param string $key Chave do cache
     * @return bool Sucesso da operação
     */
    public function delete(string $key): bool
    {
        $file = $this->getFilePath($key);

        if (file_exists($file)) {
            return unlink($file);
        }

        return true;
    }

    /**
     * Verifica se uma chave existe no cache.
     *
     * @param string $key Chave do cache
     * @return bool Se existe e não está expirado
     */
    public function has(string $key): bool
    {
        return $this->get($key) !== null;
    }

    /**
     * Limpa todo o cache.
     *
     * @return bool Sucesso da operação
     */
    public function clear(): bool
    {
        $files = glob($this->cacheDir . '/*.cache');
        
        if ($files === false) {
            return false;
        }

        foreach ($files as $file) {
            unlink($file);
        }

        return true;
    }

    /**
     * Remove entradas expiradas do cache.
     *
     * @return int Número de entradas removidas
     */
    public function gc(): int
    {
        $files = glob($this->cacheDir . '/*.cache');
        $removed = 0;

        if ($files === false) {
            return 0;
        }

        foreach ($files as $file) {
            $content = file_get_contents($file);
            
            if ($content === false) {
                continue;
            }

            $data = @unserialize($content);
            
            if ($data && isset($data['expires_at']) && $data['expires_at'] !== null && $data['expires_at'] < time()) {
                unlink($file);
                $removed++;
            }
        }

        return $removed;
    }

    /**
     * Obtém ou define um valor com callback.
     *
     * @param string $key Chave do cache
     * @param callable $callback Função que retorna o valor se não estiver em cache
     * @param int|null $ttl Tempo de vida em segundos
     * @return mixed Valor do cache ou do callback
     */
    public function remember(string $key, callable $callback, ?int $ttl = null): mixed
    {
        $value = $this->get($key);

        if ($value !== null) {
            return $value;
        }

        $value = $callback();

        if ($value !== null) {
            $this->set($key, $value, $ttl);
        }

        return $value;
    }

    /**
     * Gera o caminho do arquivo de cache.
     */
    private function getFilePath(string $key): string
    {
        // Sanitiza a chave para uso como nome de arquivo
        $safeKey = preg_replace('/[^a-zA-Z0-9_-]/', '_', $key);
        return $this->cacheDir . '/' . $safeKey . '.cache';
    }
}
