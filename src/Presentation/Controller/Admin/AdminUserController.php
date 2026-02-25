<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Admin;

use App\Infrastructure\Persistence\MySqlAdminUserRepository;
use App\Support\AuditLogger;
use App\Support\Auth;
use App\Support\Csrf;
use App\Support\PasswordValidator;
use App\Support\Session;
use Respect\Validation\Validator as v;

final class AdminUserController
{
    private MySqlAdminUserRepository $repo;

    private AuditLogger $audit;

    public function __construct(private array $config)
    {
        $this->repo = new MySqlAdminUserRepository($config['pdo']);
        $this->audit = new AuditLogger($config['pdo']);
    }

    private function requireSuperAdmin(): void
    {
        Auth::requireRole('SUPER_ADMIN');
    }

    public function index(array $vars = []): void
    {
        $this->requireSuperAdmin();

        $page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
        $perPage = 10;

        $pagination = $this->repo->paginate($page, $perPage);

        $pageTitle = 'Administradores - NimbusDocs';
        $contentView = __DIR__ . '/../../../View/admin/admin_users/index.php';
        $viewData = [
            'pagination' => $pagination,
            'csrfToken' => Csrf::token(),
            'flash' => [
                'success' => Session::getFlash('success'),
                'error' => Session::getFlash('error'),
            ],
        ];

        require __DIR__ . '/../../View/admin/layouts/base.php';
    }

    public function showCreateForm(array $vars = []): void
    {
        $this->requireSuperAdmin();

        $pageTitle = 'Novo Administrador';
        $contentView = __DIR__ . '/../../../View/admin/admin_users/form.php';
        $viewData = [
            'mode' => 'create',
            'user' => null,
            'errors' => Session::getFlash('errors') ?? [],
            'old' => Session::getFlash('old') ?? [],
            'csrfToken' => Csrf::token(),
        ];

        require __DIR__ . '/../../View/admin/layouts/base.php';
    }

    public function store(array $vars = []): void
    {
        $this->requireSuperAdmin();

        $post = $_POST;
        $token = $post['_token'] ?? '';

        if (!Csrf::validate($token)) {
            Session::flash('error', 'Sessão expirada. Tente novamente.');
            $this->redirect('/admin/users/create');
        }

        $data = [
            'name' => trim($post['name'] ?? ''),
            'email' => trim($post['email'] ?? ''),
            'auth_mode' => $post['auth_mode'] ?? 'LOCAL_ONLY',
            'role' => $post['role'] ?? 'ADMIN',
            'status' => $post['status'] ?? 'ACTIVE',
            'password' => (string) ($post['password'] ?? ''),
            'password_confirmation' => (string) ($post['password_confirmation'] ?? ''),
        ];

        $errors = $this->validateData($data, true);

        if ($errors) {
            Session::flash('errors', $errors);
            Session::flash('old', $data);
            $this->redirect('/admin/users/create');
        }

        $passwordHash = !empty($data['password'])
            ? password_hash($data['password'], PASSWORD_DEFAULT)
            : null;

        $newId = $this->repo->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'auth_mode' => $data['auth_mode'],
            'role' => $data['role'],
            'status' => $data['status'],
            'password_hash' => $passwordHash,
        ]);

        $this->audit->log('ADMIN', (int) Auth::requireAdmin()['id'], 'ADMIN_USER_CREATED', 'ADMIN_USER', $newId);

        Session::flash('success', 'Administrador criado com sucesso.');
        $this->redirect('/admin/users');
    }

    public function showEditForm(array $vars = []): void
    {
        $this->requireSuperAdmin();

        $id = (int) ($vars['id'] ?? 0);
        $user = $this->repo->findById($id);

        if (!$user) {
            Session::flash('error', 'Administrador não encontrado.');
            $this->redirect('/admin/users');
        }

        $pageTitle = 'Editar Administrador';
        $contentView = __DIR__ . '/../../../View/admin/admin_users/form.php';
        $viewData = [
            'mode' => 'edit',
            'user' => $user,
            'errors' => Session::getFlash('errors') ?? [],
            'old' => Session::getFlash('old') ?? [],
            'csrfToken' => Csrf::token(),
        ];

        require __DIR__ . '/../../View/admin/layouts/base.php';
    }

    public function update(array $vars = []): void
    {
        $this->requireSuperAdmin();

        $id = (int) ($vars['id'] ?? 0);
        $post = $_POST;
        $token = $post['_token'] ?? '';

        if (!Csrf::validate($token)) {
            Session::flash('error', 'Sessão expirada. Tente novamente.');
            $this->redirect("/admin/users/{$id}/edit");
        }

        $data = [
            'name' => trim($post['name'] ?? ''),
            'email' => trim($post['email'] ?? ''),
            'auth_mode' => $post['auth_mode'] ?? 'LOCAL_ONLY',
            'role' => $post['role'] ?? 'ADMIN',
            'status' => $post['status'] ?? 'ACTIVE',
            'password' => (string) ($post['password'] ?? ''),
            'password_confirmation' => (string) ($post['password_confirmation'] ?? ''),
        ];

        $errors = $this->validateData($data, false);

        if ($errors) {
            Session::flash('errors', $errors);
            Session::flash('old', $data);
            $this->redirect("/admin/users/{$id}/edit");
        }

        $update = [
            'name' => $data['name'],
            'email' => $data['email'],
            'auth_mode' => $data['auth_mode'],
            'role' => $data['role'],
            'status' => $data['status'],
        ];

        if (!empty($data['password'])) {
            $update['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        $this->repo->update($id, $update);
        $this->audit->log('ADMIN', (int) Auth::requireAdmin()['id'], 'ADMIN_USER_UPDATED', 'ADMIN_USER', $id);

        Session::flash('success', 'Administrador atualizado com sucesso.');
        $this->redirect('/admin/users');
    }

    public function deactivate(array $vars = []): void
    {
        $this->requireSuperAdmin();

        $id = (int) ($vars['id'] ?? 0);
        $post = $_POST;
        $token = $post['_token'] ?? '';

        if (!Csrf::validate($token)) {
            Session::flash('error', 'Sessão expirada. Tente novamente.');
            $this->redirect('/admin/users');
        }

        $this->repo->deactivate($id);
        $this->audit->log('ADMIN', (int) Auth::requireAdmin()['id'], 'ADMIN_USER_DEACTIVATED', 'ADMIN_USER', $id);

        Session::flash('success', 'Administrador desativado com sucesso.');
        $this->redirect('/admin/users');
    }

    // ----------------- helpers -----------------

    private function validateData(array $data, bool $isCreate): array
    {
        $errors = [];

        if (!v::stringType()->length(3, 150)->validate($data['name'])) {
            $errors['name'] = 'Nome deve ter entre 3 e 150 caracteres.';
        }

        if (!v::email()->length(1, 190)->validate($data['email'])) {
            $errors['email'] = 'E-mail inválido.';
        }

        if (!in_array($data['auth_mode'], ['LOCAL_ONLY', 'MS_ONLY', 'LOCAL_AND_MS'], true)) {
            $errors['auth_mode'] = 'Modo de autenticação inválido.';
        }

        if (!in_array($data['role'], ['SUPER_ADMIN', 'ADMIN'], true)) {
            $errors['role'] = 'Perfil inválido.';
        }

        if (!in_array($data['status'], ['ACTIVE', 'INACTIVE', 'BLOCKED'], true)) {
            $errors['status'] = 'Status inválido.';
        }

        // Regras de senha
        $checkPassword = $isCreate || !empty($data['password']);

        if ($checkPassword) {
            $pwdErrors = PasswordValidator::validate($data['password']);
            if (!empty($pwdErrors)) {
                $errors['password'] = implode(' ', $pwdErrors);
            } elseif ($data['password'] !== $data['password_confirmation']) {
                $errors['password_confirmation'] = 'A confirmação de senha não confere.';
            }
        }

        return $errors;
    }

    private function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }
}
