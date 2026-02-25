<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Auth;

use App\Infrastructure\Auth\TwoFactorAuthService;
use PHPUnit\Framework\TestCase;

/**
 * Smoke tests for TwoFactorAuthService
 */
class TwoFactorAuthServiceTest extends TestCase
{
    private TwoFactorAuthService $service;

    protected function setUp(): void
    {
        $this->service = new TwoFactorAuthService();
    }

    public function testGenerateSecretReturns16CharBase32(): void
    {
        $secret = $this->service->generateSecret();

        $this->assertNotEmpty($secret);
        $this->assertEquals(16, strlen($secret), 'Secret should be 16 characters');
        $this->assertMatchesRegularExpression(
            '/^[A-Z2-7]+$/',
            $secret,
            'Secret should be valid Base32'
        );
    }

    public function testGenerateSecretIsUnique(): void
    {
        $secrets = [];
        for ($i = 0; $i < 10; $i++) {
            $secrets[] = $this->service->generateSecret();
        }

        $this->assertCount(
            10,
            array_unique($secrets),
            'Each generated secret should be unique'
        );
    }

    public function testGetOtpAuthUrlContainsRequiredParts(): void
    {
        $secret = $this->service->generateSecret();
        $email = 'admin@nimbusdocs.local';
        $issuer = 'NimbusDocs';

        $url = $this->service->getOtpAuthUrl($email, $secret, $issuer);

        $this->assertStringStartsWith('otpauth://totp/', $url);
        $this->assertStringContainsString($secret, $url);
        $this->assertStringContainsString(urlencode($issuer), $url);
    }

    public function testVerifyRejectsEmptyCode(): void
    {
        $secret = $this->service->generateSecret();

        $this->assertFalse($this->service->verify($secret, ''));
    }

    public function testVerifyRejectsInvalidCode(): void
    {
        $secret = $this->service->generateSecret();

        $this->assertFalse($this->service->verify($secret, '000000'));
    }

    public function testVerifyRejectsNonNumericCode(): void
    {
        $secret = $this->service->generateSecret();

        $this->assertFalse($this->service->verify($secret, 'abcdef'));
    }

    public function testVerifyRejectsShortCode(): void
    {
        $secret = $this->service->generateSecret();

        $this->assertFalse($this->service->verify($secret, '123'));
    }

    public function testVerifyAcceptsValidCode(): void
    {
        $secret = $this->service->generateSecret();

        // Gera TOTP válido para o momento atual
        $code = $this->generateTOTP($secret);

        $this->assertTrue(
            $this->service->verify($secret, $code),
            'Service should accept a valid TOTP code'
        );
    }

    /**
     * Gera um código TOTP válido para testes (RFC 6238)
     */
    private function generateTOTP(string $secret, ?int $timeSlice = null): string
    {
        $timeSlice = $timeSlice ?? (int) floor(time() / 30);

        // Decode Base32
        $base32Chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $binary = '';
        $buffer = 0;
        $bitsLeft = 0;

        for ($i = 0; $i < strlen($secret); $i++) {
            $val = strpos($base32Chars, strtoupper($secret[$i]));
            if ($val === false) {
                continue;
            }

            $buffer = ($buffer << 5) | $val;
            $bitsLeft += 5;

            if ($bitsLeft >= 8) {
                $bitsLeft -= 8;
                $binary .= chr(($buffer >> $bitsLeft) & 0xFF);
            }
        }

        // HMAC-SHA1 over time counter
        $time = pack('N*', 0, $timeSlice);
        $hmac = hash_hmac('sha1', $time, $binary, true);

        // Dynamic truncation
        $offset = ord($hmac[strlen($hmac) - 1]) & 0x0F;
        $code = (
            ((ord($hmac[$offset]) & 0x7F) << 24) |
            ((ord($hmac[$offset + 1]) & 0xFF) << 16) |
            ((ord($hmac[$offset + 2]) & 0xFF) << 8) |
            (ord($hmac[$offset + 3]) & 0xFF)
        ) % 1000000;

        return str_pad((string) $code, 6, '0', STR_PAD_LEFT);
    }
}
