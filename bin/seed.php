<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

$config = require __DIR__ . '/../bootstrap/app.php';
$pdo = $config['pdo'];

$adminEmail = $_ENV['SEED_ADMIN_EMAIL'] ?? 'admin@example.com';
$adminPassword = $_ENV['SEED_ADMIN_PASSWORD'] ?? 'Admin@123';
$portalEmail = $_ENV['SEED_PORTAL_EMAIL'] ?? 'cliente@example.com';

// Admin seed
$stmt = $pdo->prepare('SELECT id FROM admin_users WHERE email = :email LIMIT 1');
$stmt->execute([':email' => $adminEmail]);
$adminId = $stmt->fetchColumn();

if ($adminId === false) {
    $pdo->prepare('INSERT INTO admin_users (name, email, password_hash, auth_mode, role, status) VALUES (:n,:e,:p,"LOCAL_ONLY","SUPER_ADMIN","ACTIVE")')
        ->execute([
            ':n' => 'Super Admin',
            ':e' => $adminEmail,
            ':p' => password_hash($adminPassword, PASSWORD_DEFAULT),
        ]);
    echo "Admin seed criado com email {$adminEmail}\n";
} else {
    echo "Admin já existe ({$adminEmail})\n";
}

// Portal user seed + token de acesso (sem senha)
$stmt = $pdo->prepare('SELECT id FROM portal_users WHERE email = :email LIMIT 1');
$stmt->execute([':email' => $portalEmail]);
$portalId = $stmt->fetchColumn();

if ($portalId === false) {
    $pdo->prepare('INSERT INTO portal_users (full_name, email, status) VALUES (:n,:e,"ACTIVE")')
        ->execute([
            ':n' => 'Cliente Demo',
            ':e' => $portalEmail,
        ]);
    $portalId = (int) $pdo->lastInsertId();
    echo "Usuário final seed criado com email {$portalEmail}\n";
} else {
    $portalId = (int) $portalId;
    echo "Usuário final já existe ({$portalEmail})\n";
}

// gera um código válido por 24h
$code = \App\Support\RandomToken::shortCode(12);
$pdo->prepare('INSERT INTO portal_access_tokens (portal_user_id, code, status, expires_at) VALUES (:uid,:c,"PENDING", DATE_ADD(NOW(), INTERVAL 1 DAY))')
    ->execute([
        ':uid' => $portalId,
        ':c' => $code,
    ]);

echo "Código de acesso inicial para {$portalEmail}: {$code} (validade 24h)\n";
