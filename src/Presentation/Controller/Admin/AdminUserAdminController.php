<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Admin;

use App\Infrastructure\Persistence\MySqlAdminUserRepository;
use App\Support\Session;
use App\Support\Csrf;
use App\Support\PasswordValidator;
use Respect\Validation\Validator as v;

final class AdminUserAdminController
{
    private MySqlAdminUserRepository $repo;

    public function __construct(private array $config)
    {
        $this->repo = new MySqlAdminUserRepository($config['pdo']);
    }

    private function requireSuperAdmin(): array
    {
        // aqui usamos aquele helper de role; se não tiver base, faz direto:
        $admin = Session::get('admin');
        if (!$admin) {
            header('Location: /admin/login');
            exit;
        }
        if (($admin['role'] ?? 'ADMIN') !== 'SUPER_ADMIN') {
            http_response_code(403);
            echo 'Acesso restrito a super administradores.';
            exit;
        }
        return $admin;
    }

    public function index(array $vars = []): void
    {
        $this->requireSuperAdmin();

        $page    = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = 20;

        $rows  = $this->repo->paginate($page, $perPage);
        $total = $this->repo->countAll();
        $pages = (int)ceil($total / $perPage);

        // Normaliza campos para a view
        $items = array_map(function (array $r) {
            return [
                'id'             => $r['id'],
                'name'           => $r['name'] ?? '',
                'email'          => $r['email'] ?? '',
                'role'           => $r['role'] ?? 'ADMIN',
                'status'         => $r['status'] ?? 'ACTIVE',
                'last_login_at'  => $r['last_login_at'] ?? null,
            ];
        }, $rows);

        $pagination = [
            'items' => $items,
            'page'  => $page,
            'pages' => $pages,
        ];

        $pageTitle   = 'Administradores do sistema';
        $contentView = __DIR__ . '/../../View/admin/admin_users/index.php';
        $viewData    = [
            'pagination' => $pagination,
            'csrfToken'  => Csrf::token(),
            'flash'      => [
                'success' => Session::getFlash('success'),
                'error'   => Session::getFlash('error'),
            ],
        ];

        require __DIR__ . '/../../View/admin/layouts/base.php';
    }

    public function createForm(array $vars = []): void
    {
        $this->requireSuperAdmin();

        $pageTitle   = 'Novo administrador';
        $contentView = __DIR__ . '/../../View/admin/admin_users/create.php';
        $viewData    = [
            'csrfToken' => Csrf::token(),
            'errors'    => Session::getFlash('errors', []),
            'old'       => Session::getFlash('old', []),
        ];

        require __DIR__ . '/../../View/admin/layouts/base.php';
    }

    public function store(array $vars = []): void
    {
        $this->requireSuperAdmin();

        $post  = $_POST;
        $token = $post['_token'] ?? '';

        if (!Csrf::validate($token)) {
            Session::flash('error', 'Sessão expirada. Tente novamente.');
            $this->redirect('/admin/admin-users/create');
        }

        $data = [
            'name'       => trim($post['full_name'] ?? ''), // mantém campo do formulário
            'email'      => trim($post['email'] ?? ''),
            'role'       => $post['role'] ?? 'ADMIN',
            'status'     => isset($post['is_active']) ? 'ACTIVE' : 'INACTIVE',
            'auth_mode'  => 'LOCAL_ONLY',
        ];
        $password = $post['password'] ?? '';
        $passwordConfirm = $post['password_confirmation'] ?? '';

        $errors = [];

        if (!v::stringType()->length(3, 190)->validate($data['name'])) {
            $errors['full_name'] = 'Nome deve ter ao menos 3 caracteres.';
        }

        if (!v::email()->validate($data['email'])) {
            $errors['email'] = 'E-mail inválido.';
        }

        if (!in_array($data['role'], ['SUPER_ADMIN', 'ADMIN', 'AUDITOR'], true)) {
            $errors['role'] = 'Papel inválido.';
        }

        if ($password !== '') {
            if ($password !== $passwordConfirm) {
                $errors['password'] = 'As senhas não coincidem.';
            } elseif (!v::stringType()->length(8, null)->validate($password)) {
                $errors['password'] = 'Senha deve ter pelo menos 8 caracteres.';
            }
        }

        if ($errors) {
            Session::flash('errors', $errors);
            Session::flash('old', $data);
            $this->redirect('/admin/admin-users/create');
        }

        if ($password !== '') {
            $data['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $this->repo->create($data);

        Session::flash('success', 'Administrador criado com sucesso.');
        $this->redirect('/admin/admin-users');
    }

    public function editForm(array $vars = []): void
    {
        $this->requireSuperAdmin();

        $id   = (int)($vars['id'] ?? 0);
        $user = $this->repo->findById($id);

        if (!$user) {
            http_response_code(404);
            echo 'Administrador não encontrado.';
            return;
        }

        $pageTitle   = 'Editar administrador';
        $contentView = __DIR__ . '/../../View/admin/admin_users/edit.php';
        $viewData    = [
            'user'      => $user,
            'csrfToken' => Csrf::token(),
            'errors'    => Session::getFlash('errors', []),
        ];

        require __DIR__ . '/../../View/admin/layouts/base.php';
    }

    public function update(array $vars = []): void
    {
        $this->requireSuperAdmin();

        $id   = (int)($vars['id'] ?? 0);
        $user = $this->repo->findById($id);

        if (!$user) {
            http_response_code(404);
            echo 'Administrador não encontrado.';
            return;
        }

        $post  = $_POST;
        $token = $post['_token'] ?? '';

        if (!Csrf::validate($token)) {
            Session::flash('error', 'Sessão expirada. Tente novamente.');
            $this->redirect('/admin/admin-users/' . $id . '/edit');
        }

        $data = [
            'name'      => trim($post['full_name'] ?? ''),
            'email'     => trim($post['email'] ?? ''),
            'role'      => $post['role'] ?? $user['role'],
            'status'    => isset($post['is_active']) ? 'ACTIVE' : 'INACTIVE',
        ];
        $password = $post['password'] ?? '';
        $passwordConfirm = $post['password_confirmation'] ?? '';

        $errors = [];

        if (!v::stringType()->length(3, 190)->validate($data['name'])) {
            $errors['full_name'] = 'Nome deve ter ao menos 3 caracteres.';
        }

        if (!v::email()->validate($data['email'])) {
            $errors['email'] = 'E-mail inválido.';
        }

        // proteção básica: não permitir que o próprio super admin remova todos os super admins
        if ($user['role'] === 'SUPER_ADMIN' && $data['role'] !== 'SUPER_ADMIN') {
            // aqui opcionalmente você pode checar se é o último super admin etc.
        }

        if (!in_array($data['role'], ['SUPER_ADMIN', 'ADMIN', 'AUDITOR'], true)) {
            $errors['role'] = 'Papel inválido.';
        }

        if ($password !== '') {
            $pwdErrors = PasswordValidator::validate($password);
            if (!empty($pwdErrors)) {
                $errors['password'] = implode(' ', $pwdErrors);
            } elseif ($password !== $passwordConfirm) {
                $errors['password'] = 'As senhas não coincidem.';
            }
        }

        if ($errors) {
            Session::flash('errors', $errors);
            $this->redirect('/admin/admin-users/' . $id . '/edit');
        }

        if ($password !== '') {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $this->repo->updatePassword($id, $hash);
        }

        $this->repo->update($id, $data);

        Session::flash('success', 'Administrador atualizado com sucesso.');
        $this->redirect('/admin/admin-users');
    }

    public function deactivate(array $vars = []): void
    {
        $this->requireSuperAdmin();

        $id = (int)($vars['id'] ?? 0);

        // Se vier por GET (ex.: link direto), apenas redireciona com aviso
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET') {
            Session::flash('error', 'Ação inválida via GET. Use o botão Desativar.');
            $this->redirect('/admin/users');
        }

        $token = $_POST['_token'] ?? '';
        if (!Csrf::validate($token)) {
            Session::flash('error', 'Sessão expirada. Tente novamente.');
            $this->redirect('/admin/users');
        }

        $user = $this->repo->findById($id);
        if (!$user) {
            http_response_code(404);
            echo 'Administrador não encontrado.';
            return;
        }

        $this->repo->deactivate($id);
        Session::flash('success', 'Administrador desativado com sucesso.');
        $this->redirect('/admin/users');
    }

    private function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }
}
