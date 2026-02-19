<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Utilitário de criptografia simétrica (AES-256-CBC)
 * Usa a chave APP_SECRET definida no .env
 */
class Encrypter
{
    private const CIPHER = 'aes-256-cbc';

    /**
     * Criptografa um valor.
     */
    public static function encrypt(string $value): string
    {
        $key = self::getKey();
        $iv = random_bytes(openssl_cipher_iv_length(self::CIPHER));
        
        $encrypted = openssl_encrypt($value, self::CIPHER, $key, 0, $iv);
        
        if ($encrypted === false) {
            throw new \RuntimeException('Falha ao criptografar dados.');
        }

        $mac = hash_hmac('sha256', $iv . $encrypted, $key);

        $json = json_encode([
            'iv'      => base64_encode($iv),
            'value'   => $encrypted,
            'mac'     => $mac
        ], JSON_THROW_ON_ERROR);

        return base64_encode($json);
    }

    /**
     * Descriptografa um valor (modo seguro).
     * Retorna null se o payload for inválido, adulterado ou não for formato criptografado.
     */
    public static function decrypt(?string $payload): ?string
    {
        if (!$payload || !is_string($payload)) {
            return null;
        }

        try {
            $json = base64_decode($payload, true);
            if (!$json) {
                return null;
            }

            $data = json_decode($json, true);
            
            if (!is_array($data) || !isset($data['iv'], $data['value'], $data['mac'])) {
                return null;
            }

            $key = self::getKey();
            $iv = base64_decode($data['iv'], true);
            
            // Valida MAC (integridade)
            $expectedMac = hash_hmac('sha256', $iv . $data['value'], $key);
            if (!hash_equals($expectedMac, $data['mac'])) {
                return null;
            }

            $decrypted = openssl_decrypt($data['value'], self::CIPHER, $key, 0, $iv);

            return $decrypted !== false ? $decrypted : null;

        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Descriptografa com fallback para dados legados (texto plano).
     * 
     * Usar APENAS no repositório de dados que possam ter registros antigos
     * ainda em texto puro. Loga quando o fallback é ativado para que você
     * saiba exatamente quais registros precisam ser re-criptografados.
     * 
     * @param string|null $payload O valor do banco
     * @param string $context Identificador para o log (ex: "portal_user.document_number:42")
     */
    public static function decryptOrFallback(?string $payload, string $context = ''): ?string
    {
        if (!$payload || !is_string($payload)) {
            return null;
        }

        // Tenta descriptografar normalmente
        $decrypted = self::decrypt($payload);
        if ($decrypted !== null) {
            return $decrypted;
        }

        // Se falhou, verifica se o valor parece texto legível (dado legado plain text).
        // Dados corrompidos/binários NÃO são retornados.
        if (self::looksLikePlainText($payload)) {
            // Loga para rastreamento — ajuda a identificar dados não migrados
            $logMsg = sprintf(
                '[Encrypter] Fallback para texto plano detectado (%s). Valor deve ser re-criptografado.',
                $context ?: 'contexto desconhecido'
            );
            error_log($logMsg);

            return $payload;
        }

        // Nem criptografado válido, nem texto plano legível → dado corrompido
        return null;
    }

    /**
     * Verifica se uma string parece texto plano legível (CPF, telefone, nome, etc.)
     * e NÃO lixo binário ou payload corrompido.
     */
    private static function looksLikePlainText(string $value): bool
    {
        // Texto legível = só caracteres imprimíveis UTF-8 (letras, números, pontuação, espaços)
        // Rejeita strings com bytes de controle ou sequências binárias
        if (!mb_check_encoding($value, 'UTF-8')) {
            return false;
        }

        // Se contiver caracteres de controle (exceto \n, \r, \t), não é texto plano
        if (preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', $value)) {
            return false;
        }

        // CPFs, telefones e nomes geralmente têm até 100 chars
        if (strlen($value) > 255) {
            return false;
        }

        return true;
    }

    private static function getKey(): string
    {
        $secret = $_ENV['APP_SECRET'] ?? '';
        if (strlen($secret) < 32) {
             return hash('sha256', $secret, true);
        }
        return substr($secret, 0, 32);
    }

    /**
     * Gera um hash seguro (HMAC-SHA256) do valor usando a chave da aplicação.
     * Determinístico: a mesma entrada gera sempre o mesmo hash.
     */
    public static function hash(string $value): string
    {
        return hash_hmac('sha256', $value, self::getKey());
    }
}

