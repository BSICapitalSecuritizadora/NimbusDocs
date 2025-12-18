<?php

declare(strict_types=1);

namespace Tests\Unit\Support;

use PHPUnit\Framework\TestCase;
use App\Support\Csrf;

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

    public function testConcurrentValidation(): void
    {
        $token = Csrf::token();
        
        // Multiple validations of same token (should all pass now)
        $this->assertTrue(Csrf::validate($token));
        $this->assertTrue(Csrf::validate($token));
        $this->assertTrue(Csrf::validate($token));
    }

    public function testCaseSensitivity(): void
    {
        $token = Csrf::token();
        $upperToken = strtoupper($token);
        
        if ($token !== $upperToken) {
            $this->assertFalse(Csrf::validate($upperToken));
        }
    }
}
