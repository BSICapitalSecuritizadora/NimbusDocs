<?php
require __DIR__ . "/vendor/autoload.php";
$pdo = new PDO("mysql:host=db;dbname=nimbusdocs;charset=utf8mb4", "root", "root");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Try announcement query
$now = "2026-02-27 15:00:00";
$sql = "SELECT id, title, body, level, starts_at, ends_at
        FROM portal_announcements
        WHERE is_active = 1
          AND (starts_at IS NULL OR starts_at <= :now1)
          AND (ends_at   IS NULL OR ends_at   >= :now2)
        ORDER BY created_at DESC";
try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute([":now1" => $now, ":now2" => $now]);
    echo "Announcement: Success\n";
} catch (Exception $e) {
    echo "Announcement: " . $e->getMessage() . "\n";
}

// Try duplicated param query
$sql = "SELECT * FROM general_documents WHERE title LIKE :term OR description LIKE :term";
try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute([":term" => "%test%"]);
    echo "General Doc (Duplicate): Success\n";
} catch (Exception $e) {
    echo "General Doc (Duplicate): " . $e->getMessage() . "\n";
}

// Emulate prepares off
$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
echo "--- WITH EMULATE_PREPARES = FALSE ---\n";
try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute([":term" => "%test%"]);
    echo "General Doc (Duplicate): Success\n";
} catch (Exception $e) {
    echo "General Doc (Duplicate): " . $e->getMessage() . "\n";
}

try {
    $sql = "SELECT id, title, body, level, starts_at, ends_at
        FROM portal_announcements
        WHERE is_active = 1
          AND (starts_at IS NULL OR starts_at <= :now1)
          AND (ends_at   IS NULL OR ends_at   >= :now2)
        ORDER BY created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([":now1" => $now, ":now2" => $now]);
    echo "Announcement: Success\n";
} catch (Exception $e) {
    echo "Announcement: " . $e->getMessage() . "\n";
}

