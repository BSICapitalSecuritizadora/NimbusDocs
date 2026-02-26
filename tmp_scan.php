<?php
try {
    $pdo = new PDO('mysql:host=localhost;port=3306', 'root', '');
    $dbs = $pdo->query('SHOW DATABASES')->fetchAll(PDO::FETCH_COLUMN);
    $found = false;
    foreach ($dbs as $db) {
        try {
            $stmt = $pdo->query("SELECT MAX(id) FROM `$db`.notification_outbox");
            $max = $stmt->fetchColumn();
            if ($max) {
                echo "Database `$db` has notification_outbox with MAX(id) = $max\n";
                $found = true;
            }
        } catch(Exception $e) {
            // Table doesn't exist in this DB, ignore
        }
    }
    if (!$found) echo "No database has a notification_outbox table with rows.\n";
} catch (Exception $e) {
    echo "Connection failed: " . $e->getMessage();
}
