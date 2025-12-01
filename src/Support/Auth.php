<?php

declare(strict_types=1);

namespace App\Support;

use App\Support\Session;

final class Auth
{
    public static function admin(): ?array
    {
        /** @var array|null $admin */
        $admin = Session::get('admin');
        return $admin ?: null;
    }

    public static function requireAdmin(): array
    {
        $admin = self::admin();
        if (!$admin) {
            header('Location: /admin/login');
            exit;
        }

        // bloqueia admin inativo
        if (!empty($admin['is_active']) && (int)$admin['is_active'] === 0) {
            Session::destroy();
            http_response_code(403);
            echo 'Conta administrativa desativada.';
            exit;
        }

        return $admin;
    }

    public static function requireRole(string ...$roles): array
    {
        $admin = self::requireAdmin();
        $role  = $admin['role'] ?? 'ADMIN';

        if (!in_array($role, $roles, true)) {
            http_response_code(403);
            echo 'Acesso negado.';
            exit;
        }

        return $admin;
    }

    public static function portalUser(): ?array
    {
        /** @var array|null $user */
        $user = Session::get('portal_user');
        return $user ?: null;
    }

    public static function requirePortalUser(): array
    {
        $user = self::portalUser();
        if (!$user) {
            header('Location: /portal/login');
            exit;
        }

        if (!empty($user['is_active']) && (int)$user['is_active'] === 0) {
            Session::destroy();
            http_response_code(403);
            echo 'Seu acesso ao portal foi desativado, entre em contato com o administrador.';
            exit;
        }

        return $user;
    }
}
