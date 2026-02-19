<?php

declare(strict_types=1);

/**
 * Script to run post-deployment tasks.
 * - Migrates database
 * - Clears cache
 * - Warms up cache (optional)
 */

echo "🚀 Starting Post-Deploy Tasks...\n";

// 1. Run Migrations
echo "\n[1/3] Running Migrations...\n";
passthru('php ' . __DIR__ . '/migrate.php');

// 2. Clear Cache
echo "\n[2/3] Clearing Cache...\n";
passthru('php ' . __DIR__ . '/clear_cache.php');

// 3. Health Check Verification (Self-check)
echo "\n[3/3] Verifying Health...\n";
// In a real CLI environment, we might not be able to curl localhost easily if it's not served,
// but we can check if critical files exist.
if (file_exists(__DIR__ . '/../public/health.php')) {
    echo "✅ Health endpoint file exists.\n";
} else {
    echo "❌ Health endpoint file MISSING!\n";
}

echo "\n✅ Post-Deploy Tasks Completed.\n";
