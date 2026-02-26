<?php
require __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;
use PDO;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$host = $_ENV['DB_HOST'] ?? '127.0.0.1';
$port = $_ENV['DB_PORT'] ?? '3306';
$db   = $_ENV['DB_DATABASE'] ?? 'nimbusdocs';
$user = $_ENV['DB_USERNAME'] ?? 'root';
$pass = $_ENV['DB_PASSWORD'] ?? '';

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
$pdo = new PDO($dsn, $user, $pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sql = "INSERT INTO notification_outbox (type, recipient_email, recipient_name, subject, template, payload_json, status, attempts) VALUES ('TEST', 'lais_rodrigues@oi.com.br', 'Lais', 'Test Email', 'test', '{}', 'PENDING', 0)";
$pdo->exec($sql);
echo "Enqueued test email.";
