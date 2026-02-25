<?php

declare(strict_types=1);

namespace Tests\Unit\Support;

use App\Support\Encrypter;
use PHPUnit\Framework\TestCase;

class EncrypterTest extends TestCase
{
    protected function setUp(): void
    {
        // Mock environment variable for key
        $_ENV['APP_SECRET'] = 'test-secret-key-must-be-long-enough-32-chars';
    }

    public function testEncryptReturnsString(): void
    {
        $encrypted = Encrypter::encrypt('test value');
        $this->assertIsString($encrypted);
        $this->assertNotEquals('test value', $encrypted);
    }

    public function testDecryptRestoresValue(): void
    {
        $original = 'sensitive data 123';
        $encrypted = Encrypter::encrypt($original);
        $decrypted = Encrypter::decrypt($encrypted);

        $this->assertEquals($original, $decrypted);
    }

    public function testDecryptReturnsNullForInvalidPayload(): void
    {
        $this->assertNull(Encrypter::decrypt('invalid-base64'));
        $this->assertNull(Encrypter::decrypt(base64_encode('not-json')));
        $this->assertNull(Encrypter::decrypt(base64_encode(json_encode(['foo' => 'bar']))));
    }

    public function testDecryptReturnsNullForTamperedData(): void
    {
        $encrypted = Encrypter::encrypt('foo');
        $decoded = json_decode(base64_decode($encrypted), true);

        // Tamper with the encrypted data
        $decoded['value'] = base64_encode('tampered');

        // Re-encode
        $tampered = base64_encode(json_encode($decoded));

        // Should fail MAC check
        $this->assertNull(Encrypter::decrypt($tampered));
    }

    public function testDecryptOrFallbackReturnsDecrypted(): void
    {
        $original = 'secret';
        $encrypted = Encrypter::encrypt($original);

        $this->assertEquals($original, Encrypter::decryptOrFallback($encrypted));
    }

    public function testDecryptOrFallbackReturnsPlainTextIfReadable(): void
    {
        // Legacy plain text value
        $legacy = '12345678900';

        // Should return as-is (and log error, but we can't easily assert error_log here without deeper mocking)
        $this->assertEquals($legacy, Encrypter::decryptOrFallback($legacy));
    }

    public function testDecryptOrFallbackReturnsNullForBinaryGarbage(): void
    {
        // Binary garbage that is neither valid encrypted JSON nor readable text
        $garbage = "\x00\x01\x02\xFF";

        $this->assertNull(Encrypter::decryptOrFallback($garbage));
    }

    public function testHashIsDeterministic(): void
    {
        $val1 = Encrypter::hash('test');
        $val2 = Encrypter::hash('test');
        $val3 = Encrypter::hash('other');

        $this->assertEquals($val1, $val2);
        $this->assertNotEquals($val1, $val3);
    }
}
