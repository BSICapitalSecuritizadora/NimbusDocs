<?php

declare(strict_types=1);

namespace Tests\Unit\Support;

use App\Support\Csrf;
use PHPUnit\Framework\TestCase;

class CsrfTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $_SESSION = [];
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
        parent::tearDown();
    }

    public function testTokenGeneration(): void
    {
        $token = Csrf::token();

        $this->assertNotEmpty($token);
        $this->assertEquals(64, strlen($token));
        $this->assertMatchesRegularExpression('/^[a-f0-9]{64}$/', $token);
        $this->assertIsString($_SESSION['_csrf_token']);
    }

    public function testTokenPersistence(): void
    {
        $token1 = Csrf::token();
        $token2 = Csrf::token();

        $this->assertEquals($token1, $token2);
    }

    public function testValidTokenValidation(): void
    {
        $token = Csrf::token();

        $this->assertTrue(Csrf::validate($token));
    }

    public function testInvalidTokenValidation(): void
    {
        Csrf::token();

        $this->assertFalse(Csrf::validate('invalid_token'));
        $this->assertFalse(Csrf::validate(''));
        $this->assertFalse(Csrf::validate(null));
        $this->assertFalse(Csrf::validate('1234567890123456789012345678901234567890123456789012345678901234'));
    }

    public function testValidationWithoutToken(): void
    {
        $this->assertFalse(Csrf::validate('any_token'));
    }

    public function testTokenRegenerationSecurity(): void
    {
        $token1 = Csrf::token();
        $_SESSION['_csrf_token'] = null;
        $_SESSION['_csrf_token_ts'] = null;
        $token2 = Csrf::token();

        $this->assertNotEquals($token1, $token2);
        $this->assertFalse(Csrf::validate($token1));
    }

    public function testTokenFormatConsistency(): void
    {
        for ($i = 0; $i < 10; $i++) {
            $_SESSION = [];
            $token = Csrf::token();
            $this->assertEquals(64, strlen($token));
            $this->assertMatchesRegularExpression('/^[a-f0-9]{64}$/', $token);
        }
    }

    /**
     * Após validação, o token é rotacionado. O token anterior
     * continua válido durante a janela de graça (60s).
     */
    public function testTokenRotatesAfterValidation(): void
    {
        $token1 = Csrf::token();

        // Primeira validação: sucede e rotaciona
        $this->assertTrue(Csrf::validate($token1));

        // O token atual mudou
        $token2 = Csrf::token();
        $this->assertNotEquals($token1, $token2);
    }

    /**
     * O token anterior ainda vale dentro da janela de graça (60s),
     * permitindo multi-tab e submissões quase simultâneas.
     */
    public function testPreviousTokenValidInGraceWindow(): void
    {
        $token1 = Csrf::token();

        // Valida e rotaciona
        $this->assertTrue(Csrf::validate($token1));

        // token1 agora é o "anterior" — ainda aceito na janela de graça
        $this->assertTrue(Csrf::validate($token1));
    }

    /**
     * Após a janela de graça expirar, o token anterior é rejeitado.
     */
    public function testPreviousTokenRejectedAfterGrace(): void
    {
        $token1 = Csrf::token();

        // Valida e rotaciona
        $this->assertTrue(Csrf::validate($token1));

        // Simula que a janela de graça expirou (61s atrás)
        $_SESSION['_csrf_token_prev_ts'] = time() - 61;

        // Token anterior agora é rejeitado
        $this->assertFalse(Csrf::validate($token1));
    }

    public function testCaseSensitivity(): void
    {
        $token = Csrf::token();
        $upperToken = strtoupper($token);

        if ($token !== $upperToken) {
            $this->assertFalse(Csrf::validate($upperToken));
        }
    }

    public function testRegenerateCreatesNewToken(): void
    {
        $token1 = Csrf::token();
        Csrf::regenerate();
        $token2 = Csrf::token();

        $this->assertNotEquals($token1, $token2);
    }
}
