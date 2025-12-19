<?php

declare(strict_types=1);

/**
 * Script simples para criar Super Admin
 */

// Carrega .env
$envFile = __DIR__ . '/../.env';
if (!file_exists($envFile)) {
    die("Arquivo .env não encontrado!\n");
}

$env = [];
foreach (file($envFile) as $line) {
    $line = trim($line);
    if (empty($line) || $line[0] === '#') continue;
    if (strpos($line, '=') === false) continue;
    
    [$key, $val] = explode('=', $line, 2);
    $env[trim($key)] = trim($val, '\'"');
}

$host = $env['DB_HOST'] ?? 'localhost';
$user = $env['DB_USER'] ?? 'root';
$pass = $env['DB_PASS'] ?? '';
$database = $env['DB_NAME'] ?? 'nimbusdocs';
$port = (int)($env['DB_PORT'] ?? 3306);

try {
    $pdo = new PDO(
        "mysql:host={$host};port={$port};dbname={$database}",
        $user,
        $pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // Dados do novo super admin
    $adminData = [
        'name' => 'superadmin',
        'email' => 'superadmin@nimbusdocs.local',
        'full_name' => 'Super Administrador',
        'password' => 'Admin@123456',
        'auth_mode' => 'LOCAL_ONLY',
        'role' => 'SUPER_ADMIN',
        'status' => 'ACTIVE',
        'is_active' => 1,
    ];

    // Verifica se email já existe
    $check = $pdo->prepare('SELECT id FROM admin_users WHERE email = ?');
    $check->execute([$adminData['email']]);
    
    if ($check->fetch()) {
        echo "✗ Super Admin com este e-mail já existe.\n";
        exit(1);
    }

    // Insere novo super admin
    $stmt = $pdo->prepare(
        'INSERT INTO admin_users 
        (name, email, full_name, password_hash, auth_mode, role, status, is_active) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
    );

    $stmt->execute([
        $adminData['name'],
        $adminData['email'],
        $adminData['full_name'],
        password_hash($adminData['password'], PASSWORD_DEFAULT),
        $adminData['auth_mode'],
        $adminData['role'],
        $adminData['status'],
        $adminData['is_active'],
    ]);

    $id = $pdo->lastInsertId();

    echo "\n✓ Super Admin criado com sucesso!\n\n";
    echo "ID: {$id}\n";
    echo "E-mail: {$adminData['email']}\n";
    echo "Senha: {$adminData['password']}\n";
    echo "Papel: {$adminData['role']}\n\n";
    echo "Acesse: http://nimbusdocs.local/admin/login\n";

} catch (Exception $e) {
    echo "✗ Erro: " . $e->getMessage() . "\n";
    exit(1);
}
