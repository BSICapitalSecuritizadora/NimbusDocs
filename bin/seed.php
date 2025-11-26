<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

$config = require __DIR__ . '/../bootstrap/app.php';
$pdo    = $config['pdo'];

$adminEmail = $_ENV['SEED_ADMIN_EMAIL'] ?? 'admin@example.com';
$adminPassword = $_ENV['SEED_ADMIN_PASSWORD'] ?? 'Admin@123';
$portalEmail = $_ENV['SEED_PORTAL_EMAIL'] ?? 'cliente@example.com';
$portalPassword = $_ENV['SEED_PORTAL_PASSWORD'] ?? 'Cliente@123';

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

// Portal user seed
$stmt = $pdo->prepare('SELECT id FROM portal_users WHERE email = :email LIMIT 1');
$stmt->execute([':email' => $portalEmail]);
$portalId = $stmt->fetchColumn();

if ($portalId === false) {
    $pdo->prepare('INSERT INTO portal_users (full_name, email, status, password_hash) VALUES (:n,:e,"ACTIVE",:p)')
        ->execute([
            ':n' => 'Cliente Demo',
            ':e' => $portalEmail,
            ':p' => password_hash($portalPassword, PASSWORD_DEFAULT),
        ]);
    echo "Usuário final seed criado com email {$portalEmail}\n";
} else {
    echo "Usuário final já existe ({$portalEmail})\n";
}

