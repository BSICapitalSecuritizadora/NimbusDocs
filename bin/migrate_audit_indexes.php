<?php

require __DIR__ . '/../bootstrap/app.php';

use App\Infrastructure\Persistence\Connection;

echo "Applying Audit Log Indexes...\n";

try {
    $pdo->exec("
        CREATE INDEX idx_audit_logs_occurred_at ON audit_logs (occurred_at);
        CREATE INDEX idx_audit_logs_actor ON audit_logs (actor_type, actor_id);
        CREATE INDEX idx_audit_logs_context ON audit_logs (context_type, context_id);
        CREATE INDEX idx_audit_logs_action ON audit_logs (action);
    ");
    echo "Indexes created successfully.\n";
} catch (PDOException $e) {
    if (str_contains($e->getMessage(), 'Duplicate key name')) {
        echo "Indexes already exist.\n";
    } else {
        echo "Error: " . $e->getMessage() . "\n";
        exit(1);
    }
}
