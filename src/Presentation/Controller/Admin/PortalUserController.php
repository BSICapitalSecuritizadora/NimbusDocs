<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Admin;

use App\Infrastructure\Persistence\MySqlPortalUserRepository;
use App\Support\Csrf;
use App\Support\Session;
use Respect\Validation\Validator as v;

final class PortalUserController
{
    private MySqlPortalUserRepository $repo;

    public function __construct(private array $config)
    {
        $this->repo = new MySqlPortalUserRepository($config['pdo']);
    }

    private function requireAdmin(): void
    {
        $admin = Session::get('admin');
        if (!$admin) {
            http_response_code(403);
            echo '403 - Não autorizado';
            exit;
        }
    }

    public function index(array $vars = []): void
    {
        $this->requireAdmin();

        $page    = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = 10;

        $pagination = $this->repo->paginate($page, $perPage);

        $pageTitle   = 'Usuários Finais - NimbusDocs';
        $contentView = __DIR__ . '/../../View/admin/portal_users/index.php';
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

    public function showCreateForm(array $vars = []): void
    {
        $this->requireAdmin();

        $pageTitle   = 'Novo Usuário Final';
        $contentView = __DIR__ . '/../../View/admin/portal_users/form.php';
        $viewData    = [
            'mode'      => 'create',
            'user'      => null,
            'errors'    => Session::getFlash('errors') ?? [],
            'old'       => Session::getFlash('old') ?? [],
            'csrfToken' => Csrf::token(),
        ];

        require __DIR__ . '/../../View/admin/layouts/base.php';
    }

    public function store(array $vars = []): void
    {
        $this->requireAdmin();

        $post  = $_POST;
        $token = $post['_token'] ?? '';

        if (!Csrf::validate($token)) {
            Session::flash('error', 'Sessão expirada. Tente novamente.');
            $this->redirect('/admin/portal-users/create');
        }

        $data = [
            'full_name'       => trim($post['full_name'] ?? ''),
            'email'           => trim($post['email'] ?? ''),
            'document_number' => trim($post['document_number'] ?? ''),
            'phone_number'    => trim($post['phone_number'] ?? ''),
            'external_id'     => trim($post['external_id'] ?? ''),
            'notes'           => trim($post['notes'] ?? ''),
            'status'          => $post['status'] ?? 'INVITED',
        ];

        $errors = $this->validateData($data);

        if ($errors) {
            Session::flash('errors', $errors);
            Session::flash('old', $data);
            $this->redirect('/admin/portal-users/create');
        }

        $this->repo->create($data);

        Session::flash('success', 'Usuário final criado com sucesso.');
        $this->redirect('/admin/portal-users');
    }

    public function showEditForm(array $vars = []): void
    {
        $this->requireAdmin();

        $id   = (int)($vars['id'] ?? 0);
        $user = $this->repo->findById($id);

        if (!$user) {
            Session::flash('error', 'Usuário não encontrado.');
            $this->redirect('/admin/portal-users');
        }

        $pageTitle   = 'Editar Usuário Final';
        $contentView = __DIR__ . '/../../View/admin/portal_users/form.php';
        $viewData    = [
            'mode'      => 'edit',
            'user'      => $user,
            'errors'    => Session::getFlash('errors') ?? [],
            'old'       => Session::getFlash('old') ?? [],
            'csrfToken' => Csrf::token(),
        ];

        require __DIR__ . '/../../View/admin/layouts/base.php';
    }

    public function update(array $vars = []): void
    {
        $this->requireAdmin();

        $id    = (int)($vars['id'] ?? 0);
        $post  = $_POST;
        $token = $post['_token'] ?? '';

        if (!Csrf::validate($token)) {
            Session::flash('error', 'Sessão expirada. Tente novamente.');
            $this->redirect("/admin/portal-users/{$id}/edit");
        }

        $data = [
            'full_name'       => trim($post['full_name'] ?? ''),
            'email'           => trim($post['email'] ?? ''),
            'document_number' => trim($post['document_number'] ?? ''),
            'phone_number'    => trim($post['phone_number'] ?? ''),
            'external_id'     => trim($post['external_id'] ?? ''),
            'notes'           => trim($post['notes'] ?? ''),
            'status'          => $post['status'] ?? 'INVITED',
        ];

        $errors = $this->validateData($data);

        if ($errors) {
            Session::flash('errors', $errors);
            Session::flash('old', $data);
            $this->redirect("/admin/portal-users/{$id}/edit");
        }

        $this->repo->update($id, $data);

        Session::flash('success', 'Usuário final atualizado com sucesso.');
        $this->redirect('/admin/portal-users');
    }

    public function deactivate(array $vars = []): void
    {
        $this->requireAdmin();

        $id    = (int)($vars['id'] ?? 0);
        $post  = $_POST;
        $token = $post['_token'] ?? '';

        if (!Csrf::validate($token)) {
            Session::flash('error', 'Sessão expirada. Tente novamente.');
            $this->redirect('/admin/portal-users');
        }

        $this->repo->deactivate($id);

        Session::flash('success', 'Usuário final desativado com sucesso.');
        $this->redirect('/admin/portal-users');
    }

    // ----------------- helpers -----------------

    private function validateData(array $data): array
    {
        $errors = [];

        if (!v::stringType()->length(3, 190)->validate($data['full_name'])) {
            $errors['full_name'] = 'Nome deve ter pelo menos 3 caracteres.';
        }

        if ($data['email'] !== '' && !v::email()->length(1, 190)->validate($data['email'])) {
            $errors['email'] = 'E-mail inválido.';
        }

        if ($data['status'] && !in_array($data['status'], ['ACTIVE', 'INACTIVE', 'INVITED', 'BLOCKED'], true)) {
            $errors['status'] = 'Status inválido.';
        }

        return $errors;
    }

    private function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }
}
