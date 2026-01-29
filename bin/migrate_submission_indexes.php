<?php

require __DIR__ . '/../bootstrap/app.php';

use App\Infrastructure\Persistence\Connection;

echo "Applying Portal Submission Indexes...\n";

try {
    // Index for Date Range Filtering (critical for history)
    $pdo->exec("CREATE INDEX idx_portal_submissions_submitted_at ON portal_submissions (submitted_at)");
    echo "Index 'idx_portal_submissions_submitted_at' created.\n";

    // Index for Search by Reference Code (critical for lookup)
    $pdo->exec("CREATE INDEX idx_portal_submissions_reference_code ON portal_submissions (reference_code)");
    echo "Index 'idx_portal_submissions_reference_code' created.\n";

} catch (PDOException $e) {
    if (str_contains($e->getMessage(), 'Duplicate key name')) {
        echo "Indexes already exist.\n";
    } else {
        echo "Error: " . $e->getMessage() . "\n";
        exit(1);
    }
}
