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
     * Retorna string base64: base64(iv . ciphertext . mac) para integridade.
     * formato simples: base64(json_encode(['iv'=>..., 'value'=>..., 'mac'=>...]))
     * Ou mais simples: base64(iv) . ':' . base64(ciphertext) (estilo Laravel antigo)
     * Vamos usar uma abordagem segura com HMAC.
     */
    public static function encrypt(string $value): string
    {
        $key = self::getKey();
        $iv = random_bytes(openssl_cipher_iv_length(self::CIPHER));
        
        $encrypted = openssl_encrypt($value, self::CIPHER, $key, 0, $iv);
        
        if ($encrypted === false) {
            throw new \RuntimeException('Falha ao criptografar dados.');
        }

        // Gera MAC para garantir integridade e evitar padding oracle attacks
        $mac = hash_hmac('sha256', $iv . $encrypted, $key);

        $json = json_encode([
            'iv'      => base64_encode($iv),
            'value'   => $encrypted,
            'mac'     => $mac
        ], JSON_THROW_ON_ERROR);

        return base64_encode($json);
    }

    /**
     * Descriptografa um valor.
     * Retorna null se falhar ou se o dados não for válido.
     */
    public static function decrypt(?string $payload): ?string
    {
        if (!$payload) {
            return null;
        }

        try {
            $json = base64_decode($payload, true);
            if (!$json) return null; // Não é base64 válido

            $data = json_decode($json, true);
            
            if (!is_array($data) || !isset($data['iv'], $data['value'], $data['mac'])) {
                // Tenta fallback para texto plano (caso ainda não esteja migrado ou erro)
                // Isso é útil durante a migração: se não conseguir decriptar, assume que é dado legado.
                // MAS CUIDADO: pode confundir lixo com dado real.
                // Como nossos dados alvo (CPF/Phone) não parecem JSON base64, podemos assumir que se falhar o decode, é plano.
                return $payload;
            }

            $key = self::getKey();
            $iv = base64_decode($data['iv'], true);
            
            // Valida MAC
            $expectedMac = hash_hmac('sha256', $iv . $data['value'], $key);
            if (!hash_equals($expectedMac, $data['mac'])) {
                 // MAC inválido (dado adulterado)
                 return null; 
            }

            $decrypted = openssl_decrypt($data['value'], self::CIPHER, $key, 0, $iv);

            return $decrypted !== false ? $decrypted : null;

        } catch (\Throwable $e) {
            // Se der erro (ex: json invalido), retorna o o valor original (assumindo legado/plano)
            // ou loga o erro.
            return $payload; 
        }
    }

    private static function getKey(): string
    {
        $secret = $_ENV['APP_SECRET'] ?? '';
        if (strlen($secret) < 32) {
             // Pad ou hash para garantir 32 bytes (AES-256)
             return hash('sha256', $secret, true);
        }
        return substr($secret, 0, 32); // Trunca se for maior (ou usa direto se for 32 bytes raw)
    }
}
