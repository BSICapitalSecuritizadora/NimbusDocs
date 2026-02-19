<?php

declare(strict_types=1);

namespace App\Infrastructure\Auth;

/**
 * Simple JWT Service for API authentication
 * Uses HMAC-SHA256 for signing
 */
class JwtService
{
    private string $secret;
    private int $expiresIn;
    private string $issuer;

    public function __construct(string $secret, int $expiresIn = 3600, string $issuer = 'nimbusdocs')
    {
        $this->secret = $secret;
        $this->expiresIn = $expiresIn;
        $this->issuer = $issuer;
    }

    /**
     * Generate a JWT token
     * 
     * @param array $payload Custom payload data
     * @return string JWT token
     */
    public function generate(array $payload): string
    {
        $header = [
            'alg' => 'HS256',
            'typ' => 'JWT',
        ];

        $now = time();

        $payload = array_merge($payload, [
            'iss' => $this->issuer,
            'iat' => $now,
            'nbf' => $now,
            'exp' => $now + $this->expiresIn,
        ]);

        $headerEncoded = $this->base64UrlEncode(json_encode($header));
        $payloadEncoded = $this->base64UrlEncode(json_encode($payload));

        $signature = hash_hmac('sha256', "{$headerEncoded}.{$payloadEncoded}", $this->secret, true);
        $signatureEncoded = $this->base64UrlEncode($signature);

        return "{$headerEncoded}.{$payloadEncoded}.{$signatureEncoded}";
    }

    /**
     * Verify and decode a JWT token
     * 
     * @param string $token JWT token
     * @return array|null Payload if valid, null if invalid
     */
    public function verify(string $token): ?array
    {
        $parts = explode('.', $token);

        if (count($parts) !== 3) {
            return null;
        }

        [$headerEncoded, $payloadEncoded, $signatureEncoded] = $parts;

        // Verify signature
        $expectedSignature = hash_hmac('sha256', "{$headerEncoded}.{$payloadEncoded}", $this->secret, true);
        $expectedSignatureEncoded = $this->base64UrlEncode($expectedSignature);

        if (!hash_equals($expectedSignatureEncoded, $signatureEncoded)) {
            return null;
        }

        // Decode payload
        $payload = json_decode($this->base64UrlDecode($payloadEncoded), true);

        if (!$payload) {
            return null;
        }

        // Check expiration
        if (isset($payload['exp']) && $payload['exp'] < time()) {
            return null;
        }

        // Check not-before (with 30s clock skew tolerance)
        if (isset($payload['nbf']) && $payload['nbf'] > (time() + 30)) {
            return null;
        }

        // Check issuer
        if (isset($payload['iss']) && $payload['iss'] !== $this->issuer) {
            return null;
        }

        return $payload;
    }

    /**
     * Decode a token without verification (for debugging)
     */
    public function decode(string $token): ?array
    {
        $parts = explode('.', $token);

        if (count($parts) !== 3) {
            return null;
        }

        [, $payloadEncoded,] = $parts;
        return json_decode($this->base64UrlDecode($payloadEncoded), true);
    }

    /**
     * Base64 URL encode
     */
    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Base64 URL decode
     */
    private function base64UrlDecode(string $data): string
    {
        return base64_decode(strtr($data, '-_', '+/'));
    }
}
