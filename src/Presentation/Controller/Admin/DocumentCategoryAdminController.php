<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Admin;

use App\Infrastructure\Persistence\MySqlDocumentCategoryRepository;
use App\Infrastructure\Persistence\MySqlGeneralDocumentRepository;
use App\Support\Auth;
use App\Support\Session;
use App\Support\Csrf;

final class DocumentCategoryAdminController
{
    private MySqlDocumentCategoryRepository $repo;
    private MySqlGeneralDocumentRepository $docsRepo;

    public function __construct(private array $config)
    {
        $this->repo = new MySqlDocumentCategoryRepository($config['pdo']);
        $this->docsRepo = new MySqlGeneralDocumentRepository($config['pdo']);
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
        $admin = Auth::requireRole('ADMIN', 'SUPER_ADMIN');
        error_log("Delete Category requested. User: " . $admin['id'] . " Role: " . $admin['role']);

        if (!Csrf::validate($_POST['_token'] ?? '')) {
            error_log("CSRF Validation failed for delete category.");
            Session::flash('error', 'Sessão expirada.');
            header('Location: /admin/document-categories');
            exit;
        }

        $id = (int)($vars['id'] ?? 0);
        error_log("Attempting to delete category ID: " . $id);

        // Check for dependencies
        $count = $this->docsRepo->countByCategory($id);
        error_log("Category dependency count: " . $count);

        if ($count > 0) {
             error_log("Found $count documents. Cleaning them up before category deletion (User requested fix).");
             $this->docsRepo->deleteByCategoryId($id);
        }

        try {
            $this->repo->delete($id);
            error_log("Category deletion successful in DB.");
            Session::flash('success', 'Categoria removida.');
        } catch (\Exception $e) {
            error_log("DB Error during category delete: " . $e->getMessage());
            Session::flash('error', 'Erro ao excluir categoria.');
        }

        header('Location: /admin/document-categories');
        exit;
    }
}
