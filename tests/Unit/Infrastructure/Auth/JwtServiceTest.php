<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Auth;

use App\Infrastructure\Auth\JwtService;
use PHPUnit\Framework\TestCase;

/**
 * Smoke tests for JwtService
 */
class JwtServiceTest extends TestCase
{
    private JwtService $jwt;
    private string $secret = 'test-secret-key-for-ci-pipeline-12345';

    protected function setUp(): void
    {
        $this->jwt = new JwtService($this->secret, 3600, 'nimbusdocs-test');
    }

    public function testGenerateReturnsThreePartToken(): void
    {
        $token = $this->jwt->generate(['sub' => 1]);

        $parts = explode('.', $token);
        $this->assertCount(3, $parts, 'JWT should have 3 parts: header.payload.signature');
    }

    public function testVerifyValidToken(): void
    {
        $payload = ['sub' => 42, 'role' => 'ADMIN'];
        $token = $this->jwt->generate($payload);

        $decoded = $this->jwt->verify($token);

        $this->assertNotNull($decoded, 'Valid token should be verified');
        $this->assertEquals(42, $decoded['sub']);
        $this->assertEquals('ADMIN', $decoded['role']);
    }

    public function testVerifyContainsStandardClaims(): void
    {
        $token = $this->jwt->generate(['sub' => 1]);
        $decoded = $this->jwt->verify($token);

        $this->assertNotNull($decoded);
        $this->assertArrayHasKey('iat', $decoded, 'Should have issued-at');
        $this->assertArrayHasKey('exp', $decoded, 'Should have expiration');
        $this->assertArrayHasKey('nbf', $decoded, 'Should have not-before');
        $this->assertArrayHasKey('iss', $decoded, 'Should have issuer');
        $this->assertEquals('nimbusdocs-test', $decoded['iss']);
    }

    public function testVerifyRejectsExpiredToken(): void
    {
        // Cria um serviço com expiração de 0 segundos
        $jwt = new JwtService($this->secret, 0, 'nimbusdocs-test');
        $token = $jwt->generate(['sub' => 1]);

        // Espera 1 segundo para expirar
        sleep(1);

        $decoded = $jwt->verify($token);
        $this->assertNull($decoded, 'Expired token should return null');
    }

    public function testVerifyRejectsTamperedToken(): void
    {
        $token = $this->jwt->generate(['sub' => 1, 'role' => 'USER']);
        
        // Adultera o payload (troca role para ADMIN)
        $parts = explode('.', $token);
        $payload = json_decode(base64_decode($parts[1]), true);
        $payload['role'] = 'SUPER_ADMIN';
        $parts[1] = rtrim(base64_encode(json_encode($payload)), '=');
        $tampered = implode('.', $parts);

        $decoded = $this->jwt->verify($tampered);
        $this->assertNull($decoded, 'Tampered token should be rejected');
    }

    public function testVerifyRejectsGarbage(): void
    {
        $this->assertNull($this->jwt->verify('not.a.jwt'));
        $this->assertNull($this->jwt->verify(''));
        $this->assertNull($this->jwt->verify('garbage'));
    }

    public function testDifferentSecretsCannotVerify(): void
    {
        $other = new JwtService('different-secret-key', 3600, 'nimbusdocs-test');
        $token = $this->jwt->generate(['sub' => 1]);

        $decoded = $other->verify($token);
        $this->assertNull($decoded, 'Token signed with different secret should be rejected');
    }

    public function testDecodeReturnsPayloadRegardlessOfSignature(): void
    {
        $token = $this->jwt->generate(['sub' => 99]);

        $decoded = $this->jwt->decode($token);
        $this->assertNotNull($decoded);
        $this->assertEquals(99, $decoded['sub']);
    }
}
