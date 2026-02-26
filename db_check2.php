<?php
require __DIR__ . '/bootstrap/app.php';
$container = require __DIR__ . '/config/dependencies.php';
$pdo = $container['pdo']();

$stmt = $pdo->query("SELECT id, status, recipient_email FROM notification_outbox ORDER BY id DESC LIMIT 15");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
