<?php
$pdo = new PDO('mysql:host=localhost;dbname=nimbusdocs;charset=utf8mb4', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$stmt = $pdo->prepare("UPDATE notification_outbox SET status='PENDING', next_attempt_at = NULL, last_error = NULL WHERE status IN ('SENDING', 'FAILED', 'PENDING')");
$stmt->execute();
echo "Resetted " . $stmt->rowCount() . " rows back to PENDING.\n";
