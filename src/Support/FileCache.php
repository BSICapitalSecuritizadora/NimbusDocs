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
     * Incrementa o valor de um item no cache.
     * Se não existir, cria com o valor inicial.
     *
     * @param string $key Chave do cache
     * @param int $amount Quantidade a incrementar
     * @param int|null $ttl Tempo de vida em segundos (apenas se criar novo)
     * @return int Novo valor
     */
    public function increment(string $key, int $amount = 1, ?int $ttl = null): int
    {
        $file = $this->getFilePath($key);

        // Bloqueio exclusivo para garantir atomicidade
        $fp = fopen($file, 'c+');
        if (!$fp) {
            return 0; // Falha ao abrir
        }

        if (!flock($fp, LOCK_EX)) {
            fclose($fp);

            return 0; // Falha ao bloquear
        }

        $content = '';
        while (!feof($fp)) {
            $content .= fread($fp, 8192);
        }

        $data = $content ? @unserialize($content) : null;
        $currentValue = 0;
        $expiresAt = null;

        if ($data && is_array($data)) {
            // Verifica expiração
            if (isset($data['expires_at']) && $data['expires_at'] !== null && $data['expires_at'] < time()) {
                // Expirou, reseta
                $data = null;
            } else {
                $currentValue = (int) ($data['value'] ?? 0);
                $expiresAt = $data['expires_at'] ?? null;
            }
        }

        $newValue = $currentValue + $amount;

        // Se criou agora ou resetou, define novo expire
        if ($data === null) {
            $ttl = $ttl ?? $this->defaultTtl;
            $expiresAt = $ttl > 0 ? time() + $ttl : null;
        }

        $newData = [
            'value' => $newValue,
            'expires_at' => $expiresAt,
            'created_at' => $data['created_at'] ?? time(),
        ];

        ftruncate($fp, 0); // Limpa arquivo
        rewind($fp);      // Volta para o início
        fwrite($fp, serialize($newData));
        fflush($fp);      // Garante escrita
        flock($fp, LOCK_UN);
        fclose($fp);

        return $newValue;
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
