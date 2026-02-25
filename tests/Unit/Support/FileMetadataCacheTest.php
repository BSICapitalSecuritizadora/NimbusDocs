<?php

declare(strict_types=1);

namespace Tests\Unit\Support;

use App\Support\FileMetadataCache;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Support\FileMetadataCache
 */
class FileMetadataCacheTest extends TestCase
{
    private string $cacheDir;

    private FileMetadataCache $cache;

    protected function setUp(): void
    {
        $this->cacheDir = sys_get_temp_dir() . '/metadata_test_' . uniqid();
        $this->cache = new FileMetadataCache($this->cacheDir, 3600);
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

    public function testGetReturnsNullWhenNotCached(): void
    {
        $result = $this->cache->get('document', 123);
        $this->assertNull($result);
    }

    public function testSetAndGet(): void
    {
        $metadata = [
            'id' => 123,
            'name' => 'test.pdf',
            'mime' => 'application/pdf',
            'size' => 1024,
        ];

        $this->cache->set('document', 123, $metadata);

        $result = $this->cache->get('document', 123);

        $this->assertEquals($metadata, $result);
    }

    public function testInvalidateRemovesFromCache(): void
    {
        $metadata = ['id' => 123, 'name' => 'test.pdf'];

        $this->cache->set('document', 123, $metadata);
        $this->assertNotNull($this->cache->get('document', 123));

        $this->cache->invalidate('document', 123);

        $this->assertNull($this->cache->get('document', 123));
    }

    public function testRememberReturnsCachedValue(): void
    {
        $metadata = ['id' => 123, 'name' => 'test.pdf'];
        $this->cache->set('document', 123, $metadata);

        $callbackCalled = false;
        $result = $this->cache->remember('document', 123, function () use (&$callbackCalled) {
            $callbackCalled = true;

            return ['id' => 456, 'name' => 'other.pdf'];
        });

        // Callback não deve ser chamado pois valor está em cache
        $this->assertFalse($callbackCalled);
        $this->assertEquals($metadata, $result);
    }

    public function testRememberCallsCallbackWhenNotCached(): void
    {
        $callbackCalled = false;
        $metadata = ['id' => 789, 'name' => 'new.pdf'];

        $result = $this->cache->remember('document', 789, function () use (&$callbackCalled, $metadata) {
            $callbackCalled = true;

            return $metadata;
        });

        $this->assertTrue($callbackCalled);
        $this->assertEquals($metadata, $result);

        // Deve estar em cache agora
        $this->assertEquals($metadata, $this->cache->get('document', 789));
    }

    public function testRememberReturnsNullWhenCallbackReturnsNull(): void
    {
        $result = $this->cache->remember('document', 999, function () {
            return null;
        });

        $this->assertNull($result);
        $this->assertNull($this->cache->get('document', 999));
    }

    public function testClearRemovesAllCachedData(): void
    {
        $this->cache->set('document', 1, ['id' => 1]);
        $this->cache->set('document', 2, ['id' => 2]);
        $this->cache->set('submission', 1, ['id' => 1]);

        $this->cache->clear();

        $this->assertNull($this->cache->get('document', 1));
        $this->assertNull($this->cache->get('document', 2));
        $this->assertNull($this->cache->get('submission', 1));
    }

    public function testDifferentTypesAreIndependent(): void
    {
        $docMetadata = ['type' => 'document', 'id' => 1];
        $subMetadata = ['type' => 'submission', 'id' => 1];

        $this->cache->set('document', 1, $docMetadata);
        $this->cache->set('submission', 1, $subMetadata);

        $this->assertEquals($docMetadata, $this->cache->get('document', 1));
        $this->assertEquals($subMetadata, $this->cache->get('submission', 1));
    }
}
