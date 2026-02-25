<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Security;

use App\Infrastructure\Security\RateLimiter;
use App\Support\FileCache;
use PHPUnit\Framework\TestCase;

class RateLimiterTest extends TestCase
{
    private string $cacheDir;

    private FileCache $cache;

    private RateLimiter $limiter;

    protected function setUp(): void
    {
        parent::setUp();
        // Cria diretório temporário isolado para cada teste
        $this->cacheDir = sys_get_temp_dir() . '/nimbus_test_cache_' . uniqid();
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir);
        }

        $this->cache = new FileCache($this->cacheDir);
        $this->limiter = new RateLimiter($this->cache);
    }

    protected function tearDown(): void
    {
        // Limpa cache e remove diretório
        $this->cache->clear();
        $this->removeDirectory($this->cacheDir);
        parent::tearDown();
    }

    private function removeDirectory($dir)
    {
        if (!is_dir($dir)) {
            return;
        }
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? $this->removeDirectory("$dir/$file") : unlink("$dir/$file");
        }
        rmdir($dir);
    }

    public function test_it_allows_requests_within_limit(): void
    {
        $key = 'user_1';
        $limit = 5;

        // Executa 5 vezes, deve permitir todas (retornar false para tooManyAttempts)
        for ($i = 0; $i < 5; $i++) {
            $this->assertFalse(
                $this->limiter->tooManyAttempts($key, $limit),
                "Attempt $i should be allowed"
            );
        }
    }

    public function test_it_blocks_requests_exceeding_limit(): void
    {
        $key = 'user_2';
        $limit = 3;

        // Consome o limite
        $this->limiter->tooManyAttempts($key, $limit); // 1
        $this->limiter->tooManyAttempts($key, $limit); // 2
        $this->limiter->tooManyAttempts($key, $limit); // 3

        // A próxima (4ª) deve ser bloqueada
        $this->assertTrue(
            $this->limiter->tooManyAttempts($key, $limit),
            'Should be blocked after exceeding limit'
        );
    }

    public function test_counters_are_isolated_by_key(): void
    {
        $limit = 2;

        // Bloqueia user A
        $this->limiter->tooManyAttempts('user_A', $limit);
        $this->limiter->tooManyAttempts('user_A', $limit);
        $this->assertTrue($this->limiter->tooManyAttempts('user_A', $limit));

        // User B deve estar livre
        $this->assertFalse($this->limiter->tooManyAttempts('user_B', $limit));
    }
}
