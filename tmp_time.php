<?php
$pdo = new PDO('mysql:host=localhost;dbname=nimbusdocs;charset=utf8mb4', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$dbTime = $pdo->query('SELECT NOW()')->fetchColumn();
echo "PHP Time: " . date('Y-m-d H:i:s') . "\n";
echo "DB Time : " . $dbTime . "\n";

$stmt = $pdo->query("SELECT id, created_at FROM notification_outbox WHERE status = 'PENDING' LIMIT 5");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "Pending Jobs:\n";
print_r($rows);
