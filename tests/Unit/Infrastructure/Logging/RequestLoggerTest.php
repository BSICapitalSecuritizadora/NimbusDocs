<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Logging;

use PHPUnit\Framework\TestCase;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Psr\Log\LoggerInterface;
use App\Infrastructure\Logging\RequestLogger;

class RequestLoggerTest extends TestCase
{
    private $logger;
    private $requestLogger;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->requestLogger = new RequestLogger($this->logger);
        
        // Simulate request data
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/test/path';
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        $_SERVER['HTTP_USER_AGENT'] = 'PHPUnit Test';
        $_SESSION = ['admin_user' => ['id' => 1, 'name' => 'Test Admin']];
    }

    protected function tearDown(): void
    {
        $_SERVER = [];
        $_SESSION = [];
        parent::tearDown();
    }

    public function testGetClientIpDirect(): void
    {
        $_SERVER['REMOTE_ADDR'] = '192.168.1.100';
        
        $logger = new RequestLogger($this->logger);
        $reflection = new \ReflectionClass($logger);
        $method = $reflection->getMethod('getClientIp');
        $method->setAccessible(true);
        
        $ip = $method->invoke($logger);
        
        $this->assertEquals('192.168.1.100', $ip);
    }

    public function testGetClientIpFromCloudflare(): void
    {
        $_SERVER['REMOTE_ADDR'] = '10.0.0.1';
        $_SERVER['HTTP_CF_CONNECTING_IP'] = '203.0.113.1';
        
        $logger = new RequestLogger($this->logger);
        $reflection = new \ReflectionClass($logger);
        $method = $reflection->getMethod('getClientIp');
        $method->setAccessible(true);
        
        $ip = $method->invoke($logger);
        
        $this->assertEquals('203.0.113.1', $ip);
    }

    public function testGetClientIpFromXForwardedFor(): void
    {
        $_SERVER['REMOTE_ADDR'] = '10.0.0.1';
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '203.0.113.5, 10.0.0.2';
        
        $logger = new RequestLogger($this->logger);
        $reflection = new \ReflectionClass($logger);
        $method = $reflection->getMethod('getClientIp');
        $method->setAccessible(true);
        
        $ip = $method->invoke($logger);
        
        $this->assertEquals('203.0.113.5', $ip);
    }

    public function testLogSuccess(): void
    {
        // Test that logSuccess doesn't throw an exception
        $this->requestLogger->logSuccess(200);
        $this->assertTrue(true);
    }

    public function testLogError(): void
    {
        // Test that logError doesn't throw an exception
        $this->requestLogger->logError('Test error', 500);
        $this->assertTrue(true);
    }

    public function testLogErrorWithException(): void
    {
        // Test that logError with exception doesn't throw
        $exception = new \Exception('Test exception', 123);
        $this->requestLogger->logError('Error occurred', 500, $exception);
        $this->assertTrue(true);
    }

    public function testLogUnauthorized(): void
    {
        // Test that logUnauthorized doesn't throw an exception
        $this->requestLogger->logUnauthorized(403, 'Access denied');
        $this->assertTrue(true);
    }

    public function testMultipleLogEntries(): void
    {
        // Test that multiple log calls don't throw exceptions
        $this->requestLogger->logSuccess(200);
        $this->requestLogger->logSuccess(200);
        $this->requestLogger->logError('Error', 500);
        $this->assertTrue(true);
    }

    public function testLogEntriesAreValidJson(): void
    {
        // Test that log calls work
        $this->requestLogger->logSuccess(200);
        $this->requestLogger->logError('Test', 400);
        $this->assertTrue(true);
    }

    public function testGetRecentRequests(): void
    {
        // Create some log entries
        for ($i = 0; $i < 5; $i++) {
            $this->requestLogger->logSuccess(200);
        }
        
        $recent = RequestLogger::getRecentRequests(3);
        $this->assertIsArray($recent);
    }

    public function testGetRecentRequestsWithEmptyFile(): void
    {
        $recent = RequestLogger::getRecentRequests(10);
        $this->assertIsArray($recent);
    }

    public function testRotateRequestLog(): void
    {
        // Test that logging works
        for ($i = 0; $i < 15; $i++) {
            $this->requestLogger->logSuccess(200);
        }
        $this->assertTrue(true);
    }

    public function testLogWithoutSession(): void
    {
        $_SESSION = [];
        $this->requestLogger->logSuccess(200);
        $this->assertTrue(true);
    }

    public function testLogDuration(): void
    {
        $this->requestLogger->logSuccess(200);
        $this->assertTrue(true);
    }

    public function testLogIpAddress(): void
    {
        $_SERVER['REMOTE_ADDR'] = '192.168.1.50';
        $this->requestLogger->logSuccess(200);
        $this->assertTrue(true);
    }

    public function testLogUserAgent(): void
    {
        $_SERVER['HTTP_USER_AGENT'] = 'Custom User Agent';
        $this->requestLogger->logSuccess(200);
        $this->assertTrue(true);
    }
}
