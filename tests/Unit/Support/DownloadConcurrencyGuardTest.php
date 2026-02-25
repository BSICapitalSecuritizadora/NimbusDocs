<?php

declare(strict_types=1);

namespace Tests\Unit\Support;

use App\Support\DownloadConcurrencyGuard;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Support\DownloadConcurrencyGuard
 */
class DownloadConcurrencyGuardTest extends TestCase
{
    private string $cacheDir;

    private DownloadConcurrencyGuard $guard;

    protected function setUp(): void
    {
        $this->cacheDir = sys_get_temp_dir() . '/concurrency_test_' . uniqid();
        $this->guard = new DownloadConcurrencyGuard($this->cacheDir, 3, 60);
    }

    protected function tearDown(): void
    {
        // Limpa cache de teste
        $files = glob($this->cacheDir . '/*.cache');
        if ($files) {
            foreach ($files as $file) {
                unlink($file);
            }
        }
        if (is_dir($this->cacheDir)) {
            rmdir($this->cacheDir);
        }
    }

    public function testAcquireReturnsTrueWhenUnderLimit(): void
    {
        $result = $this->guard->acquire('test_ip');
        $this->assertTrue($result);
    }

    public function testAcquireReturnsFalseWhenLimitReached(): void
    {
        // Adquire o limite máximo
        $this->guard->acquire('test_ip');
        $this->guard->acquire('test_ip');
        $this->guard->acquire('test_ip');

        // Deve retornar false agora
        $result = $this->guard->acquire('test_ip');
        $this->assertFalse($result);
    }

    public function testReleaseDecrementsCounter(): void
    {
        $this->guard->acquire('test_ip');
        $this->guard->acquire('test_ip');
        $this->guard->acquire('test_ip');

        // Limite atingido
        $this->assertFalse($this->guard->acquire('test_ip'));

        // Libera um slot
        $this->guard->release('test_ip');

        // Agora deve permitir
        $this->assertTrue($this->guard->acquire('test_ip'));
    }

    public function testGetActiveCountReturnsCorrectValue(): void
    {
        $this->assertEquals(0, $this->guard->getActiveCount('test_ip'));

        $this->guard->acquire('test_ip');
        $this->assertEquals(1, $this->guard->getActiveCount('test_ip'));

        $this->guard->acquire('test_ip');
        $this->assertEquals(2, $this->guard->getActiveCount('test_ip'));

        $this->guard->release('test_ip');
        $this->assertEquals(1, $this->guard->getActiveCount('test_ip'));
    }

    public function testCanAcquireReturnsCorrectValue(): void
    {
        $this->assertTrue($this->guard->canAcquire('test_ip'));

        $this->guard->acquire('test_ip');
        $this->guard->acquire('test_ip');
        $this->guard->acquire('test_ip');

        $this->assertFalse($this->guard->canAcquire('test_ip'));
    }

    public function testGetMaxConcurrentReturnsConfiguredValue(): void
    {
        $this->assertEquals(3, $this->guard->getMaxConcurrent());

        $customGuard = new DownloadConcurrencyGuard($this->cacheDir . '_custom', 5, 60);
        $this->assertEquals(5, $customGuard->getMaxConcurrent());

        // Cleanup
        if (is_dir($this->cacheDir . '_custom')) {
            rmdir($this->cacheDir . '_custom');
        }
    }

    public function testDifferentIdentifiersAreIndependent(): void
    {
        // Atinge limite para IP1
        $this->guard->acquire('ip1');
        $this->guard->acquire('ip1');
        $this->guard->acquire('ip1');

        // IP2 ainda pode adquirir
        $this->assertTrue($this->guard->acquire('ip2'));

        // IP1 não pode
        $this->assertFalse($this->guard->acquire('ip1'));
    }
}
