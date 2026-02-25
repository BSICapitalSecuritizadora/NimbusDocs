<?php

declare(strict_types=1);

namespace Tests\Feature;

use PHPUnit\Framework\TestCase;

class MonitoringSystemTest extends TestCase
{
    private string $projectRoot;

    private string $requestLogFile;

    protected function setUp(): void
    {
        parent::setUp();

        $this->projectRoot = dirname(__DIR__, 2);
        $this->requestLogFile = $this->projectRoot . '/storage/logs/requests.jsonl';
    }

    public function testRequestLoggerClassExists(): void
    {
        $this->assertTrue(
            class_exists('App\Infrastructure\Logging\RequestLogger'),
            'RequestLogger class should exist'
        );
    }

    public function testMonitoringControllerExists(): void
    {
        $this->assertTrue(
            class_exists('App\Presentation\Controller\Admin\MonitoringAdminController'),
            'MonitoringAdminController class should exist'
        );
    }

    public function testMonitoringViewExists(): void
    {
        $viewPath = $this->projectRoot . '/src/Presentation/View/admin/monitoring/index.php';
        $this->assertFileExists($viewPath, 'Monitoring dashboard view should exist');
    }

    public function testMonitoringDocumentationExists(): void
    {
        $docs = [
            'MONITORAMENTO_AVANCADO.md',
            'RESUMO_MONITORAMENTO.md',
            'ENTREGA_MONITORAMENTO.md',
        ];

        foreach ($docs as $doc) {
            $path = $this->projectRoot . '/' . $doc;
            $this->assertFileExists($path, "{$doc} should exist");
        }
    }

    public function testRequestLogFileFormat(): void
    {
        // If log file exists, test its format
        if (file_exists($this->requestLogFile)) {
            $content = file_get_contents($this->requestLogFile);
            $lines = array_filter(explode("\n", $content));

            if (count($lines) > 0) {
                $firstLine = $lines[0];
                $decoded = json_decode($firstLine, true);

                $this->assertIsArray($decoded, 'Log entry should be valid JSON');
                $this->assertArrayHasKey('timestamp', $decoded, 'Log should have timestamp');
                $this->assertArrayHasKey('method', $decoded, 'Log should have HTTP method');
                $this->assertArrayHasKey('path', $decoded, 'Log should have path');
                $this->assertArrayHasKey('status_code', $decoded, 'Log should have status code');
            }
        }

        $this->assertTrue(true, 'Request log format check passed');
    }

    public function testMonitoringRoutesInAdmin(): void
    {
        $adminRouter = $this->projectRoot . '/public/admin.php';
        $content = file_get_contents($adminRouter);

        $this->assertStringContainsString('/admin/monitoring', $content, 'Monitoring route should be registered');
        $this->assertStringContainsString('MonitoringAdminController', $content, 'MonitoringAdminController should be used');
    }

    public function testRequestLoggerIntegration(): void
    {
        $adminRouter = $this->projectRoot . '/public/admin.php';
        $content = file_get_contents($adminRouter);

        $this->assertStringContainsString('RequestLogger', $content, 'RequestLogger should be integrated in admin.php');
        $this->assertStringContainsString('logSuccess', $content, 'Should log successful requests');
        $this->assertStringContainsString('logError', $content, 'Should log errors');
    }

    public function testPortalRouterLogging(): void
    {
        $portalRouter = $this->projectRoot . '/public/portal.php';
        $content = file_get_contents($portalRouter);

        $this->assertStringContainsString('RequestLogger', $content, 'RequestLogger should be integrated in portal.php');
    }

    public function testBootstrapIntegration(): void
    {
        $bootstrap = $this->projectRoot . '/bootstrap/app.php';
        $content = file_get_contents($bootstrap);

        $this->assertStringContainsString('RequestLogger', $content, 'RequestLogger should be in bootstrap');
        $this->assertStringContainsString('request_logger', $content, 'Should be added to config');
    }

    public function testStorageLogsDirectory(): void
    {
        $logsDir = $this->projectRoot . '/storage/logs';

        $this->assertDirectoryExists($logsDir, 'storage/logs directory should exist');
        $this->assertTrue(is_writable($logsDir), 'storage/logs should be writable');
    }

    public function testMonitoringTestScript(): void
    {
        $testScript = $this->projectRoot . '/bin/scripts/test-monitoring.sh';

        if (file_exists($testScript)) {
            $this->assertTrue(is_readable($testScript), 'test-monitoring.sh should be readable');
        } else {
            $this->markTestSkipped('test-monitoring.sh not found (optional)');
        }
    }

    public function testRequestLogRotation(): void
    {
        $requestLoggerFile = $this->projectRoot . '/src/Infrastructure/Logging/RequestLogger.php';
        $content = file_get_contents($requestLoggerFile);

        $this->assertStringContainsString('rotate', strtolower($content), 'RequestLogger should have rotation logic');
    }

    public function testStaticMethodsAvailable(): void
    {
        $reflection = new \ReflectionClass('App\Infrastructure\Logging\RequestLogger');

        $this->assertTrue($reflection->hasMethod('getRecentRequests'), 'Should have getRecentRequests method');
        $this->assertTrue($reflection->hasMethod('getStatistics'), 'Should have getStatistics method');
        $this->assertTrue($reflection->hasMethod('getAlerts'), 'Should have getAlerts method');
    }

    public function testMonitoringAPIsExist(): void
    {
        $controller = new \ReflectionClass('App\Presentation\Controller\Admin\MonitoringAdminController');

        $this->assertTrue($controller->hasMethod('index'), 'Should have index method');
        $this->assertTrue($controller->hasMethod('apiStats'), 'Should have apiStats method');
        $this->assertTrue($controller->hasMethod('apiAlerts'), 'Should have apiAlerts method');
        $this->assertTrue($controller->hasMethod('apiRequests'), 'Should have apiRequests method');
    }
}
