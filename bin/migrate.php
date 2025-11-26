<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use PDO;
use RuntimeException;

$config = require __DIR__ . '/../bootstrap/app.php';
$pdo    = $config['pdo'];

$migrationsDir = dirname(__DIR__) . '/database/migrations';
$files = glob($migrationsDir . '/*.sql');
sort($files);

$pdo->exec('CREATE TABLE IF NOT EXISTS migrations (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, filename VARCHAR(255) NOT NULL UNIQUE, executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)');

$stmt = $pdo->query('SELECT filename FROM migrations');
$applied = $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];

foreach ($files as $file) {
    $filename = basename($file);
    if (in_array($filename, $applied, true)) {
        echo "[skip] {$filename}\n";
        continue;
    }

    $sql = file_get_contents($file);
    if ($sql === false) {
        throw new RuntimeException("Não foi possível ler {$filename}");
    }

    echo "[run] {$filename}\n";
    $pdo->exec($sql);

    $insert = $pdo->prepare('INSERT INTO migrations (filename) VALUES (:f)');
    $insert->execute([':f' => $filename]);
}

echo "Migrations concluídas.\n";
