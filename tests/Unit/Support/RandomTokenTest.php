<?php

declare(strict_types=1);

namespace Tests\Unit\Support;

use App\Support\RandomToken;
use PHPUnit\Framework\TestCase;

class RandomTokenTest extends TestCase
{
    public function testShortCodeReturnsCorrectLength(): void
    {
        $this->assertEquals(12, strlen(RandomToken::shortCode(12)));
        $this->assertEquals(16, strlen(RandomToken::shortCode(16)));
    }

    public function testShortCodeCharactersAreValid(): void
    {
        $code = RandomToken::shortCode(100);

        // Alfabeto permitido: ABCDEFGHJKMNPQRSTUVWXYZ23456789 (sem ambíguos)
        $this->assertMatchesRegularExpression('/^[A-Z2-9]+$/', $code);

        // Não deve conter caracteres ambíguos
        $this->assertStringNotContainsString('I', $code);
        $this->assertStringNotContainsString('L', $code); // O alfabeto original não tem L? Conferindo...
        $this->assertStringNotContainsString('O', $code);
        $this->assertStringNotContainsString('0', $code);
        $this->assertStringNotContainsString('1', $code);
    }

    public function testShortCodeIsUnique(): void
    {
        $code1 = RandomToken::shortCode();
        $code2 = RandomToken::shortCode();

        $this->assertNotEquals($code1, $code2);
    }
}
