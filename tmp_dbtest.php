<?php
try {
    $pdo = new PDO('mysql:host=localhost;port=3306;dbname=nimbusdocs', 'root', '');
    $stmt = $pdo->query('SELECT id, status, recipient_email FROM notification_outbox ORDER BY id DESC LIMIT 20');
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (Exception $e) {
    echo "Connection failed: " . $e->getMessage();
}
