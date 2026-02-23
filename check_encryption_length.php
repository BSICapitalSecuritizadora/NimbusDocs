<?php
require __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$doc = '123.456.789-00';
$encrypted = \App\Support\Encrypter::encrypt($doc);
echo "Document: {$doc}\n";
echo "Length: " . strlen($encrypted) . "\n";
echo "Value: {$encrypted}\n";
