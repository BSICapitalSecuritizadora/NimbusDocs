<?php

declare(strict_types=1);

namespace App\Infrastructure\Integration;

use App\Support\FileCache;
use Psr\Log\LoggerInterface;

/**
 * Decorator que adiciona cache ao CnpjWsService.
 * Evita chamadas repetidas à API externa para o mesmo CNPJ.
 */
final class CachedCnpjService
{
    private CnpjWsService $api;

    private FileCache $cache;

    private LoggerInterface $logger;

    private int $cacheTtl;

    /**
     * @param CnpjWsService $api Serviço original de consulta CNPJ
     * @param FileCache $cache Sistema de cache
     * @param LoggerInterface $logger Logger para debug
     * @param int $cacheTtl Tempo de vida do cache em segundos (default: 7 dias)
     */
    public function __construct(
        CnpjWsService $api,
        FileCache $cache,
        LoggerInterface $logger,
        int $cacheTtl = 604800 // 7 dias
    ) {
        $this->api = $api;
        $this->cache = $cache;
        $this->logger = $logger;
        $this->cacheTtl = $cacheTtl;
    }

    /**
     * Busca dados do CNPJ com cache.
     *
     * @param string $cnpj CNPJ com ou sem formatação
     * @return array|null Dados da empresa ou null se não encontrado
     */
    public function getCompanyData(string $cnpj): ?array
    {
        // Normaliza o CNPJ para usar como chave de cache
        $normalizedCnpj = preg_replace('/\D/', '', $cnpj);

        if (strlen($normalizedCnpj) !== 14) {
            return null;
        }

        $cacheKey = 'cnpj_' . $normalizedCnpj;

        // Tenta obter do cache
        $cached = $this->cache->get($cacheKey);

        if ($cached !== null) {
            $this->logger->debug('CNPJ obtido do cache', [
                'cnpj' => $normalizedCnpj,
            ]);

            return $cached;
        }

        // Busca da API
        $this->logger->debug('Buscando CNPJ da API', [
            'cnpj' => $normalizedCnpj,
        ]);

        $data = $this->api->getCompanyData($cnpj);

        // Só armazena em cache se houver dados válidos
        if ($data !== null) {
            $this->cache->set($cacheKey, $data, $this->cacheTtl);

            $this->logger->debug('CNPJ armazenado em cache', [
                'cnpj' => $normalizedCnpj,
                'ttl' => $this->cacheTtl,
            ]);
        }

        return $data;
    }

    /**
     * Invalida o cache de um CNPJ específico.
     *
     * @param string $cnpj CNPJ a invalidar
     * @return bool Sucesso da operação
     */
    public function invalidate(string $cnpj): bool
    {
        $normalizedCnpj = preg_replace('/\D/', '', $cnpj);
        $cacheKey = 'cnpj_' . $normalizedCnpj;

        $this->logger->info('Invalidando cache de CNPJ', [
            'cnpj' => $normalizedCnpj,
        ]);

        return $this->cache->delete($cacheKey);
    }

    /**
     * Força atualização do cache de um CNPJ.
     *
     * @param string $cnpj CNPJ a atualizar
     * @return array|null Dados atualizados ou null
     */
    public function refresh(string $cnpj): ?array
    {
        $this->invalidate($cnpj);

        return $this->getCompanyData($cnpj);
    }

    // Proxy para métodos estáticos do serviço original

    public static function formatCnpj(string $cnpj): string
    {
        return CnpjWsService::formatCnpj($cnpj);
    }

    public static function isValidCnpj(string $cnpj): bool
    {
        return CnpjWsService::isValidCnpj($cnpj);
    }
}
