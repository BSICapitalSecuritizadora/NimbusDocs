<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Admin;

use App\Infrastructure\Persistence\MySqlAdminUserRepository;
use App\Support\Csrf;
use App\Support\Session;

final class AdminMicrosoftLinkController
{
    private MySqlAdminUserRepository $repo;

    public function __construct(private array $config)
    {
        $this->repo = new MySqlAdminUserRepository($config['pdo']);
    }

    public function showForm(array $vars = []): void
    {
        $pageTitle   = 'Vincular Conta Microsoft ao Admin';
        $contentView = __DIR__ . '/../../View/admin/ms_link/form.php';

        $viewData = [
            'csrfToken' => Csrf::token(),
            'error'     => Session::getFlash('error'),
            'success'   => Session::getFlash('success'),
            'old'       => Session::getFlash('old') ?? [],
        ];

        require __DIR__ . '/../../View/admin/layouts/base.php';
    }

    public function store(array $vars = []): void
    {
        $post = $_POST ?? [];
        $token = $post['_token'] ?? '';

        if (!Csrf::validate($token)) {
            Session::flash('error', 'Sessão expirada.');
            Session::flash('old', $post);
            header('Location: /admin/ms-link');
            exit;
        }

        $email = trim((string)($post['email'] ?? ''));
        $oid   = trim((string)($post['oid'] ?? ''));
        $tenant= trim((string)($post['tenant'] ?? ''));
        $upn   = trim((string)($post['upn'] ?? ''));

        if ($email === '' || $oid === '') {
            Session::flash('error', 'Informe ao menos e-mail e OID.');
            Session::flash('old', $post);
            header('Location: /admin/ms-link');
            exit;
        }

        $user = $this->repo->findActiveByEmail($email);
        if (!$user) {
            Session::flash('error', 'Admin não encontrado ou inativo.');
            Session::flash('old', $post);
            header('Location: /admin/ms-link');
            exit;
        }

        $this->repo->update((int)$user['id'], [
            'ms_object_id' => $oid,
            'azure_oid'    => $oid,
            'ms_tenant_id' => $tenant !== '' ? $tenant : null,
            'azure_tenant_id' => $tenant !== '' ? $tenant : null,
            'ms_upn'       => $upn !== '' ? $upn : null,
            'azure_upn'    => $upn !== '' ? $upn : null,
        ]);

        Session::flash('success', 'Conta Microsoft vinculada com sucesso.');
        header('Location: /admin/ms-link');
        exit;
    }
}
