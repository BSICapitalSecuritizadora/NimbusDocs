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

$portalUser = [
    'email' => 'lais_rodrigues@oi.com.br',
    'full_name' => 'Lais Rodrigues'
];

$token = [
    'token' => 'teste-de-envio-123456',
    'expires_at' => date('Y-m-d H:i:s', strtotime('+1 day'))
];

$payload = json_encode(['user' => $portalUser, 'token' => $token], JSON_UNESCAPED_UNICODE);

$sql = "INSERT INTO notification_outbox 
        (type, recipient_email, recipient_name, subject, template, payload_json, status, attempts) 
        VALUES 
        ('TOKEN_CREATED', :email, :name, '[NimbusDocs] Teste Definitivo de E-mail', 'token_created', :payload, 'PENDING', 0)";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':email' => $portalUser['email'],
    ':name' => $portalUser['full_name'],
    ':payload' => $payload
]);

echo "E-mail de teste enfileirado com sucesso!\n";
