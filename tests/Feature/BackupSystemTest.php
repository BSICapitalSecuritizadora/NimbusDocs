<?php

declare(strict_types=1);

namespace Tests\Feature;

use PHPUnit\Framework\TestCase;

class BackupSystemTest extends TestCase
{
    private string $projectRoot;
    private string $backupScript;
    private string $validateScript;
    private string $testBackupDir;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->projectRoot = dirname(__DIR__, 2);
        $this->backupScript = $this->projectRoot . '/bin/scripts/backup.sh';
        $this->validateScript = $this->projectRoot . '/bin/scripts/validate-backup.sh';
        $this->testBackupDir = sys_get_temp_dir() . '/test_backups_' . uniqid();
        
        mkdir($this->testBackupDir, 0777, true);
    }

    protected function tearDown(): void
    {
        if (is_dir($this->testBackupDir)) {
            $this->rrmdir($this->testBackupDir);
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

    public function testBackupScriptExists(): void
    {
        $this->assertFileExists($this->backupScript, 'backup.sh should exist');
        $this->assertTrue(is_readable($this->backupScript), 'backup.sh should be readable');
    }

    public function testValidateScriptExists(): void
    {
        $this->assertFileExists($this->validateScript, 'validate-backup.sh should exist');
        $this->assertTrue(is_readable($this->validateScript), 'validate-backup.sh should be readable');
    }

    public function testBackupScriptHasShebang(): void
    {
        $content = file_get_contents($this->backupScript);
        $this->assertStringStartsWith('#!/bin/bash', $content, 'backup.sh should have bash shebang');
    }

    public function testBackupScriptContainsChecksumGeneration(): void
    {
        $content = file_get_contents($this->backupScript);
        $this->assertStringContainsString('sha256sum', $content, 'backup.sh should generate SHA-256 checksums');
    }

    public function testBackupScriptContainsMetadataGeneration(): void
    {
        $content = file_get_contents($this->backupScript);
        $this->assertStringContainsString('.meta', $content, 'backup.sh should generate metadata file');
    }

    public function testValidateScriptHasValidationSteps(): void
    {
        $content = file_get_contents($this->validateScript);
        
        $this->assertStringContainsString('checksum', strtolower($content), 'Should validate checksum');
        $this->assertStringContainsString('metadata', strtolower($content), 'Should validate metadata');
        $this->assertStringContainsString('tar', strtolower($content), 'Should test tar extraction');
    }

    public function testBackupDirectoryStructure(): void
    {
        $backupsDir = $this->projectRoot . '/backups';
        
        // If backups dir doesn't exist, that's okay (might not have run backup yet)
        // But if it does, it should be writable
        if (is_dir($backupsDir)) {
            $this->assertTrue(is_writable($backupsDir), 'backups/ should be writable');
        }
        
        $this->assertTrue(true, 'Backup directory check passed');
    }

    public function testTestRestoreScriptExists(): void
    {
        $testRestoreScript = $this->projectRoot . '/bin/scripts/test-restore.sh';
        $this->assertFileExists($testRestoreScript, 'test-restore.sh should exist');
    }

    public function testBackupAlertScriptExists(): void
    {
        $alertScript = $this->projectRoot . '/bin/scripts/backup-alert.sh';
        $this->assertFileExists($alertScript, 'backup-alert.sh should exist');
    }

    public function testDisasterRecoveryPlanExists(): void
    {
        $drPlan = $this->projectRoot . '/docs/PLANO_RECUPERACAO_DESASTRES.md';
        $this->assertFileExists($drPlan, 'Disaster recovery plan should exist');
    }

    public function testDisasterRecoveryPlanContent(): void
    {
        $drPlan = $this->projectRoot . '/docs/PLANO_RECUPERACAO_DESASTRES.md';
        $content = file_get_contents($drPlan);
        
        $this->assertStringContainsString('RTO', $content, 'Should define RTO');
        $this->assertStringContainsString('RPO', $content, 'Should define RPO');
        $this->assertStringContainsString('restore', strtolower($content), 'Should contain restore procedures');
    }

    public function testCrontabExampleExists(): void
    {
        $crontab = $this->projectRoot . '/bin/scripts/crontab.example';
        $this->assertFileExists($crontab, 'crontab.example should exist');
    }

    public function testCrontabContainsBackupSchedule(): void
    {
        $crontab = $this->projectRoot . '/bin/scripts/crontab.example';
        if (file_exists($crontab)) {
            $content = file_get_contents($crontab);
            $this->assertStringContainsString('backup.sh', $content, 'Crontab should schedule backups');
        } else {
            $this->markTestSkipped('crontab.example not found');
        }
    }

    public function testScriptsAreShellScripts(): void
    {
        $scripts = [
            'backup.sh',
            'validate-backup.sh',
            'test-restore.sh',
            'backup-alert.sh',
        ];
        
        foreach ($scripts as $script) {
            $path = $this->projectRoot . '/bin/scripts/' . $script;
            if (file_exists($path)) {
                $content = file_get_contents($path);
                $this->assertMatchesRegularExpression(
                    '/^#!\/bin\/(ba)?sh/',
                    $content,
                    "{$script} should have valid shebang"
                );
            }
        }
    }

    public function testBackupDocumentationExists(): void
    {
        $docs = [
            'docs/ENTREGA_BACKUP_VALIDATION.md',
            'docs/PLANO_RECUPERACAO_DESASTRES.md',
        ];
        
        foreach ($docs as $doc) {
            $path = $this->projectRoot . '/' . $doc;
            $this->assertFileExists($path, "{$doc} should exist");
        }
    }

    public function testStorageLogsDirectory(): void
    {
        $logsDir = $this->projectRoot . '/storage/logs';
        
        if (is_dir($logsDir)) {
            $this->assertTrue(is_writable($logsDir), 'storage/logs should be writable for backup alerts');
        } else {
            $this->markTestSkipped('storage/logs directory not found');
        }
    }
}
