<?php

namespace NimbusDocs\Presentation\Controller;

use App\Support\Session;

abstract class AbstractAdminController
{
    protected function currentAdmin(): array
    {
        return Session::get('admin') ?? [];
    }

    protected function requireAdmin(): array
    {
        $admin = $this->currentAdmin();
        if (!$admin) {
            header('Location: /admin/login');
            exit;
        }

        return $admin;
    }

    protected function requireRole(string ...$roles): array
    {
        $admin = $this->requireAdmin();

        $role = $admin['role'] ?? 'ANON';
        if (!in_array($role, $roles, true)) {
            http_response_code(403);
            echo 'Acesso negado.';
            exit;
        }

        return $admin;
    }
}
