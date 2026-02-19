<?php

declare(strict_types=1);

/**
 * Script to clear application caches.
 * Useful for post-deployment cleanup.
 */

require __DIR__ . '/../bootstrap/app.php';

echo "🧹 Clearing application caches...\n";

$cacheDirs = [
    __DIR__ . '/../storage/cache',
    __DIR__ . '/../storage/views', // If using a template engine with cache
];

$filesCount = 0;

foreach ($cacheDirs as $dir) {
    if (!is_dir($dir)) {
        continue;
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );

    foreach ($iterator as $file) {
        if ($file->getFilename() === '.gitignore') {
            continue;
        }

        if ($file->isFile()) {
            unlink($file->getRealPath());
            $filesCount++;
        }
    }
}

echo "✅ Cleared {$filesCount} cache files.\n";
