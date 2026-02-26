<?php
$pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=nimbusdocs;charset=utf8mb4', 'root', '');
$stmt = $pdo->query("SELECT id, status, recipient_email FROM notification_outbox ORDER BY id DESC LIMIT 15");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
