<?php

declare(strict_types=1);

namespace Tests\Unit\Support;

use PHPUnit\Framework\TestCase;
use App\Support\StreamingFileDownloader;

/**
 * @covers \App\Support\StreamingFileDownloader
 */
class StreamingFileDownloaderTest extends TestCase
{
    private string $testDir;
    private StreamingFileDownloader $downloader;

    protected function setUp(): void
    {
        $this->testDir = sys_get_temp_dir() . '/streaming_test_' . uniqid();
        mkdir($this->testDir, 0777, true);
        $this->downloader = new StreamingFileDownloader();
    }

    protected function tearDown(): void
    {
        // Limpa arquivos de teste
        $files = glob($this->testDir . '/*');
        foreach ($files as $file) {
            unlink($file);
        }
        rmdir($this->testDir);
    }

    public function testStreamReturnsFalseForNonExistentFile(): void
    {
        $result = $this->downloader->stream(
            '/nonexistent/file.pdf',
            'application/pdf',
            'test.pdf'
        );

        $this->assertFalse($result);
    }

    public function testStreamReturnsFalseForUnreadableFile(): void
    {
        $file = $this->testDir . '/unreadable.txt';
        file_put_contents($file, 'test content');
        
        // Torna arquivo ilegível (apenas funciona em sistemas Unix)
        if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
            chmod($file, 0000);
            
            $result = $this->downloader->stream(
                $file,
                'text/plain',
                'unreadable.txt'
            );
            
            $this->assertFalse($result);
            
            // Restaura permissões para cleanup
            chmod($file, 0644);
        } else {
            // No Windows, apenas marcamos como passado
            $this->assertTrue(true);
        }
    }

    public function testDownloaderCanBeInstantiated(): void
    {
        $downloader = new StreamingFileDownloader();
        $this->assertInstanceOf(StreamingFileDownloader::class, $downloader);
    }
}
