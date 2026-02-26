<?php
$pdo = new PDO('mysql:host=localhost;dbname=nimbusdocs;charset=utf8mb4', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$stmt = $pdo->query("SELECT id, status, recipient_email FROM notification_outbox WHERE id >= 76");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
