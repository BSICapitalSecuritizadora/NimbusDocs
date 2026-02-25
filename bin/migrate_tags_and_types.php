<?php

require __DIR__ . '/../bootstrap/app.php';

echo "Applying Tags and Submission Types Schema...\n";

try {
    // 1. Add submission_type column
    try {
        $pdo->exec("ALTER TABLE portal_submissions ADD COLUMN submission_type VARCHAR(50) DEFAULT 'REGISTRATION' AFTER reference_code");
        $pdo->exec('CREATE INDEX idx_portal_submissions_type ON portal_submissions (submission_type)');
        echo "Column 'submission_type' added and indexed.\n";
    } catch (PDOException $e) {
        if (str_contains($e->getMessage(), 'Duplicate column name')) {
            echo "Column 'submission_type' already exists.\n";
        } else {
            throw $e;
        }
    }

    // 2. Create tags table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS tags (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(50) NOT NULL,
            color VARCHAR(7) DEFAULT '#666666',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY idx_tags_name (name)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    echo "Table 'tags' created/verified.\n";

    // 3. Create submission_tags pivot table
    $pdo->exec('
        CREATE TABLE IF NOT EXISTS submission_tags (
            submission_id INT UNSIGNED NOT NULL,
            tag_id INT UNSIGNED NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (submission_id, tag_id),
            FOREIGN KEY (submission_id) REFERENCES portal_submissions(id) ON DELETE CASCADE,
            FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ');
    echo "Table 'submission_tags' created/verified.\n";

} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
    exit(1);
}
