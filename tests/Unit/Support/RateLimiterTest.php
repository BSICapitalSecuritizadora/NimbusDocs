<?php

declare(strict_types=1);

namespace Tests\Unit\Support;

use PHPUnit\Framework\TestCase;
use App\Support\RateLimiter;

class RateLimiterTest extends TestCase
{
    private string $testFile;
    private string $testIdentifier;

    protected function setUp(): void
    {
        parent::setUp();
        $this->testFile = sys_get_temp_dir() . '/test_rate_limiter_' . uniqid() . '.json';
        $this->testIdentifier = 'test_user_' . time();
    }

    protected function tearDown(): void
    {
        if (file_exists($this->testFile)) {
            unlink($this->testFile);
        }
        parent::tearDown();
    }

    public function testInitialAttemptsAllowed(): void
    {
        $limiter = new RateLimiter(5, 900, $this->testFile);
        
        $this->assertTrue($limiter->check($this->testIdentifier), 'First attempt should be allowed');
    }

    public function testMultipleAttemptsWithinLimit(): void
    {
        $limiter = new RateLimiter(5, 900, $this->testFile);
        
        for ($i = 0; $i < 5; $i++) {
            $this->assertTrue(
                $limiter->check($this->testIdentifier),
                "Attempt {$i} should be allowed within limit"
            );
            $limiter->increment($this->testIdentifier);
        }
    }

    public function testExceedingRateLimit(): void
    {
        $limiter = new RateLimiter(3, 900, $this->testFile);
        
        // Allow 3 attempts
        for ($i = 0; $i < 3; $i++) {
            $limiter->check($this->testIdentifier);
            $limiter->increment($this->testIdentifier);
        }
        
        // 4th attempt should fail
        $this->assertFalse(
            $limiter->check($this->testIdentifier),
            'Attempts exceeding limit should be blocked'
        );
    }

    public function testRemainingAttempts(): void
    {
        $limiter = new RateLimiter(5, 900, $this->testFile);
        
        $this->assertEquals(5, $limiter->remaining($this->testIdentifier), 'Should have 5 attempts initially');
        
        $limiter->increment($this->testIdentifier);
        $this->assertEquals(4, $limiter->remaining($this->testIdentifier), 'Should have 4 attempts after 1 increment');
        
        $limiter->increment($this->testIdentifier);
        $this->assertEquals(3, $limiter->remaining($this->testIdentifier), 'Should have 3 attempts after 2 increments');
    }

    public function testReset(): void
    {
        $limiter = new RateLimiter(5, 900, $this->testFile);
        
        // Use all attempts
        for ($i = 0; $i < 5; $i++) {
            $limiter->increment($this->testIdentifier);
        }
        
        $this->assertEquals(0, $limiter->remaining($this->testIdentifier));
        
        // Reset
        $limiter->resetInstance($this->testIdentifier);
        
        $this->assertEquals(5, $limiter->remaining($this->testIdentifier), 'Attempts should reset to limit');
        $this->assertTrue($limiter->check($this->testIdentifier), 'Check should pass after reset');
    }

    public function testDifferentIdentifiers(): void
    {
        $limiter = new RateLimiter(3, 900, $this->testFile);
        
        $identifier1 = 'user1';
        $identifier2 = 'user2';
        
        // Exhaust user1
        for ($i = 0; $i < 3; $i++) {
            $limiter->increment($identifier1);
        }
        
        $this->assertFalse($limiter->check($identifier1), 'User1 should be blocked');
        $this->assertTrue($limiter->check($identifier2), 'User2 should not be affected');
    }

    public function testPersistence(): void
    {
        $limiter1 = new RateLimiter(5, 900, $this->testFile);
        $limiter1->increment($this->testIdentifier);
        $limiter1->increment($this->testIdentifier);
        
        // Create new instance with same file
        $limiter2 = new RateLimiter(5, 900, $this->testFile);
        
        $this->assertEquals(
            3,
            $limiter2->remaining($this->testIdentifier),
            'Attempts should persist across instances'
        );
    }

    public function testWindowExpiration(): void
    {
        // Short window for testing (2 seconds)
        $limiter = new RateLimiter(2, 2, $this->testFile);
        
        $limiter->increment($this->testIdentifier);
        $limiter->increment($this->testIdentifier);
        
        $this->assertFalse($limiter->check($this->testIdentifier), 'Should be blocked');
        
        // Wait for window to expire
        sleep(3);
        
        $this->assertTrue(
            $limiter->check($this->testIdentifier),
            'Should be allowed after window expiration'
        );
        $this->assertEquals(
            2,
            $limiter->remaining($this->testIdentifier),
            'Attempts should reset after window expiration'
        );
    }

    public function testIncrementWithoutCheck(): void
    {
        $limiter = new RateLimiter(5, 900, $this->testFile);
        
        $limiter->increment($this->testIdentifier);
        $limiter->increment($this->testIdentifier);
        
        $this->assertEquals(3, $limiter->remaining($this->testIdentifier));
    }

    public function testZeroRemaining(): void
    {
        $limiter = new RateLimiter(2, 900, $this->testFile);
        
        $limiter->increment($this->testIdentifier);
        $limiter->increment($this->testIdentifier);
        
        $this->assertEquals(0, $limiter->remaining($this->testIdentifier));
        $this->assertFalse($limiter->check($this->testIdentifier));
    }

    public function testFileCreation(): void
    {
        $this->assertFileDoesNotExist($this->testFile);
        
        $limiter = new RateLimiter(5, 900, $this->testFile);
        $limiter->increment($this->testIdentifier);
        
        $this->assertFileExists($this->testFile, 'Rate limiter file should be created');
    }

    public function testJsonFormat(): void
    {
        $limiter = new RateLimiter(5, 900, $this->testFile);
        $limiter->increment($this->testIdentifier);
        
        $content = file_get_contents($this->testFile);
        $data = json_decode($content, true);
        
        $this->assertIsArray($data, 'File content should be valid JSON');
        $this->assertArrayHasKey($this->testIdentifier, $data, 'Identifier should be in data');
    }
}
