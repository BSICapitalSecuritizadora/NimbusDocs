<?php

declare(strict_types=1);

namespace Tests\Unit\Support;

use PHPUnit\Framework\TestCase;
use App\Support\FileUpload;

class FileUploadTest extends TestCase
{
    private string $tempDir;
    private string $testFile;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tempDir = sys_get_temp_dir() . '/test_uploads_' . uniqid();
        mkdir($this->tempDir, 0777, true);
        
        $this->testFile = sys_get_temp_dir() . '/test_file_' . uniqid() . '.txt';
        file_put_contents($this->testFile, 'Test content');
    }

    protected function tearDown(): void
    {
        if (file_exists($this->testFile)) {
            unlink($this->testFile);
        }
        
        if (is_dir($this->tempDir)) {
            $this->rrmdir($this->tempDir);
        }
        
        parent::tearDown();
    }

    private function rrmdir(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }
        
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                $path = $dir . DIRECTORY_SEPARATOR . $object;
                if (is_dir($path)) {
                    $this->rrmdir($path);
                } else {
                    unlink($path);
                }
            }
        }
        rmdir($dir);
    }

    public function testValidateAllowedMimeType(): void
    {
        $allowedMimes = ['text/plain', 'application/pdf'];
        
        $result = FileUpload::validate(
            $this->testFile,
            'test.txt',
            1024,
            $allowedMimes
        );
        
        $this->assertTrue($result);
    }

    public function testValidateDisallowedMimeType(): void
    {
        $allowedMimes = ['application/pdf', 'image/jpeg'];
        
        $result = FileUpload::validate(
            $this->testFile,
            'test.txt',
            1024,
            $allowedMimes
        );
        
        $this->assertFalse($result);
    }

    public function testValidateFileSize(): void
    {
        $allowedMimes = ['text/plain'];
        $maxSize = 5;
        
        $result = FileUpload::validate(
            $this->testFile,
            'test.txt',
            $maxSize,
            $allowedMimes
        );
        
        $this->assertFalse($result);
    }

    public function testValidateFileSizeWithinLimit(): void
    {
        $allowedMimes = ['text/plain'];
        $maxSize = 1024 * 1024;
        
        $result = FileUpload::validate(
            $this->testFile,
            'test.txt',
            $maxSize,
            $allowedMimes
        );
        
        $this->assertTrue($result);
    }

    public function testValidateNonexistentFile(): void
    {
        $allowedMimes = ['text/plain'];
        
        $result = FileUpload::validate(
            '/nonexistent/file.txt',
            'file.txt',
            1024,
            $allowedMimes
        );
        
        $this->assertFalse($result);
    }

    public function testValidateDangerousExtension(): void
    {
        $allowedMimes = ['text/plain'];
        
        $result = FileUpload::validate(
            $this->testFile,
            'malicious.php',
            1024,
            $allowedMimes
        );
        
        $this->assertFalse($result);
    }

    public function testValidateExecutableExtension(): void
    {
        $allowedMimes = ['text/plain'];
        $dangerousExtensions = ['.exe', '.sh', '.bat', '.cmd', '.com'];
        
        foreach ($dangerousExtensions as $ext) {
            $result = FileUpload::validate(
                $this->testFile,
                'file' . $ext,
                1024,
                $allowedMimes
            );
            
            $this->assertFalse($result);
        }
    }

    public function testStore(): void
    {
        $stored = FileUpload::store($this->testFile, $this->tempDir);
        
        $this->assertIsString($stored);
        $this->assertFileExists($stored);
        
        $storedContent = file_get_contents($stored);
        $this->assertEquals('Test content', $storedContent);
    }

    public function testStoreCreatesUniqueFilename(): void
    {
        $stored1 = FileUpload::store($this->testFile, $this->tempDir);
        $stored2 = FileUpload::store($this->testFile, $this->tempDir);
        
        $this->assertNotEquals($stored1, $stored2);
        $this->assertFileExists($stored1);
        $this->assertFileExists($stored2);
    }

    public function testStoreCreatesDirectory(): void
    {
        $newDir = $this->tempDir . '/subdir/nested';
        
        $this->assertDirectoryDoesNotExist($newDir);
        
        $stored = FileUpload::store($this->testFile, $newDir);
        
        $this->assertDirectoryExists($newDir);
        $this->assertFileExists($stored);
    }

    public function testSanitizeFilename(): void
    {
        $dangerous = "../../../etc/passwd";
        $sanitized = FileUpload::sanitizeFilename($dangerous);
        
        $this->assertStringNotContainsString('..', $sanitized);
        $this->assertStringNotContainsString('/', $sanitized);
    }

    public function testSanitizeFilenameRemovesSpecialChars(): void
    {
        $special = "file<>:\"/\\|?*name.txt";
        $sanitized = FileUpload::sanitizeFilename($special);
        
        $this->assertMatchesRegularExpression('/^[a-zA-Z0-9._-]+$/', $sanitized);
    }

    public function testGetSafeFilename(): void
    {
        $filename = FileUpload::getSafeFilename('document.pdf');
        
        $this->assertStringEndsWith('.pdf', $filename);
        $this->assertMatchesRegularExpression('/^[a-f0-9]+\.pdf$/', $filename);
    }

    public function testGetSafeFilenameUniqueness(): void
    {
        $filename1 = FileUpload::getSafeFilename('test.txt');
        $filename2 = FileUpload::getSafeFilename('test.txt');
        
        $this->assertNotEquals($filename1, $filename2);
    }

    public function testValidateWithEmptyMimeList(): void
    {
        $result = FileUpload::validate(
            $this->testFile,
            'test.txt',
            1024,
            []
        );
        
        $this->assertFalse($result);
    }

    public function testValidateZeroSize(): void
    {
        $emptyFile = sys_get_temp_dir() . '/empty_' . uniqid() . '.txt';
        file_put_contents($emptyFile, '');
        
        $result = FileUpload::validate(
            $emptyFile,
            'empty.txt',
            1024,
            ['text/plain']
        );
        
        unlink($emptyFile);
        
        $this->assertFalse($result);
    }
}
