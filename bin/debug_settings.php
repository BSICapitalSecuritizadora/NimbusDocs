<?php

require __DIR__ . '/../bootstrap/app.php';

$pdo = $config['pdo'];
$stmt = $pdo->query('SELECT * FROM app_settings');
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "--- APP SETTINGS ---\n";
foreach ($rows as $row) {
    echo "{$row['key']} = {$row['value']}\n";
}
echo "--------------------\n";
