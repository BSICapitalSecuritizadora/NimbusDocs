<?php

/**
 * Cross-platform PHP syntax checker.
 * Usage: php bin/lint.php [directory...]
 * Default: src/ bootstrap/ public/ bin/
 */

$dirs = array_slice($argv, 1) ?: ['src', 'bootstrap', 'public', 'bin'];
$errors = 0;
$checked = 0;

foreach ($dirs as $dir) {
    if (!is_dir($dir)) continue;

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS)
    );

    foreach ($iterator as $file) {
        if ($file->getExtension() !== 'php') continue;

        $path = $file->getRealPath();
        $output = [];
        $exitCode = 0;

        exec(sprintf('php -l %s 2>&1', escapeshellarg($path)), $output, $exitCode);
        $checked++;

        if ($exitCode !== 0) {
            echo "❌ " . implode(PHP_EOL, $output) . PHP_EOL;
            $errors++;
        }
    }
}

echo PHP_EOL;
if ($errors > 0) {
    echo "Found {$errors} file(s) with syntax errors out of {$checked} checked." . PHP_EOL;
    exit(1);
}

echo "✅ All {$checked} PHP files have valid syntax." . PHP_EOL;
exit(0);
