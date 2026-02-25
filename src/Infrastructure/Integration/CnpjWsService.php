<?php

declare(strict_types=1);

namespace App\Infrastructure\Integration;

use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;

final class CnpjWsService
{
    private Client $client;

    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->client = new Client([
            'base_uri' => 'https://www.receitaws.com.br/v1/',
            'timeout' => 10,
            'http_errors' => false,
        ]);
    }

    /**
     * Busca informações da empresa pelo CNPJ
     * @param string $cnpj CNPJ com ou sem formatação
     * @return array|null Retorna dados da empresa ou null em caso de erro
     */
    public function getCompanyData(string $cnpj): ?array
    {
        // Remove caracteres não numéricos
        $cnpj = preg_replace('/\D/', '', $cnpj);

        if (strlen($cnpj) !== 14) {
            $this->logger->warning('CNPJ inválido', ['cnpj' => $cnpj]);

            return null;
        }

        try {
            $response = $this->client->get("cnpj/{$cnpj}");
            $statusCode = $response->getStatusCode();
            $body = (string) $response->getBody();

            if ($statusCode !== 200) {
                $this->logger->error('Erro ao buscar CNPJ', [
                    'cnpj' => $cnpj,
                    'status' => $statusCode,
                    'body' => $body,
                ]);

                return null;
            }

            $data = json_decode($body, true);

            if (!$data || isset($data['status']) && $data['status'] === 'ERROR') {
                $this->logger->warning('CNPJ não encontrado ou erro na API', [
                    'cnpj' => $cnpj,
                    'response' => $data,
                ]);

                return null;
            }

            // Mapeia dados importantes
            return [
                'cnpj' => $data['cnpj'] ?? null,
                'name' => $data['nome'] ?? null,
                'trade_name' => $data['fantasia'] ?? null,
                'main_activity' => $data['atividade_principal'][0]['text'] ?? null,
                'phone' => $data['telefone'] ?? null,
                'email' => $data['email'] ?? null,
                'address' => [
                    'street' => $data['logradouro'] ?? null,
                    'number' => $data['numero'] ?? null,
                    'complement' => $data['complemento'] ?? null,
                    'neighborhood' => $data['bairro'] ?? null,
                    'city' => $data['municipio'] ?? null,
                    'state' => $data['uf'] ?? null,
                    'zip_code' => $data['cep'] ?? null,
                ],
                'status' => $data['situacao'] ?? null,
                'opening_date' => $data['abertura'] ?? null,
                'capital' => $data['capital_social'] ?? null,
                'legal_nature' => $data['natureza_juridica'] ?? null,
                'partners' => $data['qsa'] ?? [],
                'raw_data' => $data,
            ];
        } catch (\Throwable $e) {
            $this->logger->error('Exceção ao buscar CNPJ', [
                'cnpj' => $cnpj,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Formata CNPJ para exibição (00.000.000/0000-00)
     */
    public static function formatCnpj(string $cnpj): string
    {
        $cnpj = preg_replace('/\D/', '', $cnpj);

        if (strlen($cnpj) !== 14) {
            return $cnpj;
        }

        return sprintf(
            '%s.%s.%s/%s-%s',
            substr($cnpj, 0, 2),
            substr($cnpj, 2, 3),
            substr($cnpj, 5, 3),
            substr($cnpj, 8, 4),
            substr($cnpj, 12, 2)
        );
    }

    /**
     * Valida CNPJ (algoritmo oficial)
     */
    public static function isValidCnpj(string $cnpj): bool
    {
        $cnpj = preg_replace('/\D/', '', $cnpj);

        if (strlen($cnpj) != 14) {
            return false;
        }

        // Elimina CNPJs inválidos conhecidos
        if (preg_match('/^(\d)\1*$/', $cnpj)) {
            return false;
        }

        // Valida primeiro dígito verificador
        $sum = 0;
        $weights = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];

        for ($i = 0; $i < 12; $i++) {
            $sum += (int) $cnpj[$i] * $weights[$i];
        }

        $remainder = $sum % 11;
        $digit1 = $remainder < 2 ? 0 : 11 - $remainder;

        if ($cnpj[12] != $digit1) {
            return false;
        }

        // Valida segundo dígito verificador
        $sum = 0;
        $weights = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];

        for ($i = 0; $i < 13; $i++) {
            $sum += (int) $cnpj[$i] * $weights[$i];
        }

        $remainder = $sum % 11;
        $digit2 = $remainder < 2 ? 0 : 11 - $remainder;

        return $cnpj[13] == $digit2;
    }
}
