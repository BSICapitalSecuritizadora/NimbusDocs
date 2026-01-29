<?php

require __DIR__ . '/../bootstrap/app.php';

echo "Verifying Schema...\n";

// Check portal_submissions columns
$stmt = $pdo->query("DESCRIBE portal_submissions");
$cols = $stmt->fetchAll(PDO::FETCH_COLUMN);
if (in_array('submission_type', $cols)) {
    echo "[OK] 'submission_type' column found in portal_submissions.\n";
} else {
    echo "[FAIL] 'submission_type' column NOT found.\n";
}

// Check tags tables
$stmt = $pdo->query("SHOW TABLES LIKE 'tags'");
if ($stmt->fetch()) {
    echo "[OK] 'tags' table found.\n";
} else {
    echo "[FAIL] 'tags' table NOT found.\n";
}

$stmt = $pdo->query("SHOW TABLES LIKE 'submission_tags'");
if ($stmt->fetch()) {
    echo "[OK] 'submission_tags' table found.\n";
} else {
    echo "[FAIL] 'submission_tags' table NOT found.\n";
}
