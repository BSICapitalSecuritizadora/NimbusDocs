<?php
require __DIR__ . '/../bootstrap/app.php';
$config = require __DIR__ . '/../config/database.php';
$pdo = $config();

$stmt = $pdo->query("SELECT id, status, recipient_email FROM notification_outbox ORDER BY id DESC LIMIT 15");
echo "<pre>";
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
echo "</pre>";
