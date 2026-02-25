<?php

declare(strict_types=1);

namespace Tests\E2E;

use PDO;
use Symfony\Component\Panther\PantherTestCase;

abstract class E2ETestCase extends PantherTestCase
{
    protected ?PDO $pdo = null;

    public static function createPantherClient(array $options = [], array $kernelOptions = [], array $managerOptions = []): \Symfony\Component\Panther\Client
    {
        // NO Built-in server. We will use the existing XAMPP server.
        self::$baseUri = 'http://nimbusdocs.local';

        // Caminho absoluto pro driver no Windows
        $driverPath = realpath(__DIR__ . '/../../drivers/chromedriver.exe');

        // Cria a instância customizada
        if (null === self::$pantherClient) {
            $manager = new \Symfony\Component\Panther\ProcessManager\ChromeManager($driverPath, null, $managerOptions);
            self::$pantherClients[0] = self::$pantherClient = new \Symfony\Component\Panther\Client($manager, self::$baseUri);
        }

        return self::$pantherClient;
    }

    protected function setUp(): void
    {
        parent::setUp();
        
        // Load .env so APP_SECRET is available to Encrypter::hash() in the test process
        // (phpunit.xml bootstraps with vendor/autoload.php, which does NOT load .env)
        $dotenv = \Dotenv\Dotenv::createImmutable(dirname(__DIR__, 2));
        $dotenv->safeLoad();
        
        // Setup E2E Secure Override Token
        $this->setupE2EDatabaseOverride();
        
        $this->setUpDatabase();
        $this->cleanDatabase();
    }

    private function setupE2EDatabaseOverride(): void
    {
        // We write a file token that app.php checks to switch to test DB
        file_put_contents(__DIR__ . '/../../storage/e2e_db_override.flag', 'nimbusdocs_test');
    }

    protected function tearDown(): void
    {
        // Cleanup token
        @unlink(__DIR__ . '/../../storage/e2e_db_override.flag');
        $this->cleanDatabase();
        $this->pdo = null;
        
        parent::tearDown();
    }

    private function cleanDatabase(): void
    {
        if (!$this->pdo) return;
        
        $this->pdo->exec('SET FOREIGN_KEY_CHECKS=0;');
        
        $tables = $this->pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
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

        $pdoInit = new PDO("mysql:host={$host};port={$port};charset=utf8mb4", $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);
        
        $pdoInit->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;");
        
        $this->pdo = new PDO("mysql:host={$host};port={$port};dbname={$dbName};charset=utf8mb4", $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }
}
