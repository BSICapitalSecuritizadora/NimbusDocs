<?php

declare(strict_types=1);

namespace Tests;

use PDO;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected ?PDO $pdo = null;

    protected function setUp(): void
    {
        if (!defined('PHPUNIT_RUNNING')) {
            define('PHPUNIT_RUNNING', true);
        }

        parent::setUp();

        $this->setUpDatabase();
        $this->cleanDatabase();
    }

    protected function tearDown(): void
    {
        $this->cleanDatabase();
        $this->pdo = null;

        parent::tearDown();
    }

    private function cleanDatabase(): void
    {
        if (!$this->pdo) {
            return;
        }

        $this->pdo->exec('SET FOREIGN_KEY_CHECKS=0;');

        $tables = $this->pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
        foreach ($tables as $table) {
            if ($table !== 'migrations') {
                $this->pdo->exec("TRUNCATE TABLE `{$table}`");
            }
        }

        $this->pdo->exec('SET FOREIGN_KEY_CHECKS=1;');
    }

    private function setUpDatabase(): void
    {
        $host = $_ENV['DB_HOST'] ?? '127.0.0.1';
        $port = $_ENV['DB_PORT'] ?? '3306';
        $user = $_ENV['DB_USERNAME'] ?? 'root';
        $pass = $_ENV['DB_PASSWORD'] ?? '';
        $dbName = $_ENV['DB_DATABASE'] ?? 'nimbusdocs_test';

        // Conectar sem banco para garantir que exista
        $pdoInit = new PDO("mysql:host={$host};port={$port};charset=utf8mb4", $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);

        $pdoInit->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdoInit = null;

        // Conectar ao banco de teste
        $this->pdo = new PDO("mysql:host={$host};port={$port};dbname={$dbName};charset=utf8mb4", $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        // Rodar as migrações no banco de teste
        // $this->runMigrations();
    }

    private function runMigrations(): void
    {
        $migrationsDir = dirname(__DIR__) . '/database/migrations';
        $files = glob($migrationsDir . '/*.sql');
        sort($files);

        // self::runMigrations(); // Disabled: schema is fully seeded by importing db.sql via cmd  $this->pdo->exec('CREATE TABLE IF NOT EXISTS migrations (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, filename VARCHAR(255) NOT NULL UNIQUE, executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)');

        $stmt = $this->pdo->query('SELECT filename FROM migrations');
        $applied = $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];

        foreach ($files as $file) {
            $filename = basename($file);
            if (in_array($filename, $applied, true)) {
                continue;
            }

            $sql = file_get_contents($file);
            if ($sql === false) {
                throw new \RuntimeException("Não foi possível ler {$filename} para migração de teste.");
            }

            $this->pdo->exec($sql);

            $insert = $this->pdo->prepare('INSERT INTO migrations (filename) VALUES (:f)');
            $insert->execute([':f' => $filename]);
        }
    }

    /**
     * Helper to mock session data for controllers
     */
    protected function setSession(array $data): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
        $_SESSION = $data;
    }
}
