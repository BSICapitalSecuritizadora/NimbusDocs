<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Admin;

use App\Infrastructure\Persistence\MySqlDocumentCategoryRepository;
use App\Support\Auth;
use App\Support\Session;
use App\Support\Csrf;

final class DocumentCategoryAdminController
{
    private MySqlDocumentCategoryRepository $repo;

    public function __construct(private array $config)
    {
        $this->repo = new MySqlDocumentCategoryRepository($config['pdo']);
    }

    public function index(): void
    {
        Auth::requireRole('ADMIN', 'SUPER_ADMIN');

        $categories = $this->repo->all();

        $pageTitle   = 'Categorias de documentos gerais';
        $contentView = __DIR__ . '/../../View/admin/document_categories/index.php';

        $viewData = [
            'categories' => $categories,
            'csrfToken'  => Csrf::token(),
            'success'    => Session::getFlash('success'),
            'error'      => Session::getFlash('error'),
        ];

        require __DIR__ . '/../../View/admin/layouts/base.php';
    }

    public function createForm(): void
    {
        Auth::requireRole('ADMIN', 'SUPER_ADMIN');

        $pageTitle   = 'Nova categoria de documento';
        $contentView = __DIR__ . '/../../View/admin/document_categories/form.php';

        $viewData = [
            'mode'     => 'create',
            'data'     => ['name' => '', 'description' => '', 'sort_order' => 0],
            'csrfToken' => Csrf::token(),
        ];

        require __DIR__ . '/../../View/admin/layouts/base.php';
    }

    public function editForm(array $vars): void
    {
        Auth::requireRole('ADMIN', 'SUPER_ADMIN');

        $id   = (int)($vars['id'] ?? 0);
        $cat  = $this->repo->find($id);

        if (!$cat) {
            Session::flash('error', 'Categoria não encontrada.');
            header('Location: /admin/document-categories');
            exit;
        }

        $pageTitle   = 'Editar categoria de documento';
        $contentView = __DIR__ . '/../../View/admin/document_categories/form.php';

        $viewData = [
            'mode'     => 'edit',
            'data'     => $cat,
            'csrfToken' => Csrf::token(),
        ];

        require __DIR__ . '/../../View/admin/layouts/base.php';
    }

    public function store(): void
    {
        Auth::requireRole('ADMIN', 'SUPER_ADMIN');

        if (!Csrf::validate($_POST['_token'] ?? '')) {
            Session::flash('error', 'Sessão expirada.');
            header('Location: /admin/document-categories');
            exit;
        }

        $name       = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $sortOrder  = (int)($_POST['sort_order'] ?? 0);

        if ($name === '') {
            Session::flash('error', 'Nome é obrigatório.');
            header('Location: /admin/document-categories/new');
            exit;
        }

        $this->repo->create([
            'name'        => $name,
            'description' => $description,
            'sort_order'  => $sortOrder,
        ]);

        Session::flash('success', 'Categoria criada com sucesso.');
        header('Location: /admin/document-categories');
        exit;
    }

    public function update(array $vars): void
    {
        Auth::requireRole('ADMIN', 'SUPER_ADMIN');

        if (!Csrf::validate($_POST['_token'] ?? '')) {
            Session::flash('error', 'Sessão expirada.');
            header('Location: /admin/document-categories');
            exit;
        }

        $id   = (int)($vars['id'] ?? 0);
        $cat  = $this->repo->find($id);
        if (!$cat) {
            Session::flash('error', 'Categoria não encontrada.');
            header('Location: /admin/document-categories');
            exit;
        }

        $name       = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $sortOrder  = (int)($_POST['sort_order'] ?? 0);

        if ($name === '') {
            Session::flash('error', 'Nome é obrigatório.');
            header("Location: /admin/document-categories/{$id}/edit");
            exit;
        }

        $this->repo->update($id, [
            'name'        => $name,
            'description' => $description,
            'sort_order'  => $sortOrder,
        ]);

        Session::flash('success', 'Categoria atualizada com sucesso.');
        header('Location: /admin/document-categories');
        exit;
    }

    public function delete(array $vars): void
    {
        Auth::requireRole('SUPER_ADMIN'); // só super admin remove

        if (!Csrf::validate($_POST['_token'] ?? '')) {
            Session::flash('error', 'Sessão expirada.');
            header('Location: /admin/document-categories');
            exit;
        }

        $id = (int)($vars['id'] ?? 0);
        $this->repo->delete($id);

        Session::flash('success', 'Categoria removida.');
        header('Location: /admin/document-categories');
        exit;
    }
}
