<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Admin;

use App\Infrastructure\Persistence\MySqlDocumentCategoryRepository;
use App\Infrastructure\Persistence\MySqlGeneralDocumentRepository;
use App\Support\Auth;
use App\Support\Csrf;
use App\Support\FileUpload;
use App\Support\Session;

final class GeneralDocumentAdminController
{
    private MySqlDocumentCategoryRepository $categories;

    private MySqlGeneralDocumentRepository $docs;

    public function __construct(private array $config)
    {
        $pdo = $config['pdo'];
        $this->categories = new MySqlDocumentCategoryRepository($pdo);
        $this->docs = new MySqlGeneralDocumentRepository($pdo);
    }

    public function index(): void
    {
        Auth::requireRole('ADMIN', 'SUPER_ADMIN');

        $documents = $this->docs->listForAdmin();
        $categories = $this->categories->all();

        $pageTitle = 'Documentos gerais';
        $contentView = __DIR__ . '/../../View/admin/general_documents/index.php';

        $viewData = [
            'documents' => $documents,
            'categories' => $categories,
            'csrfToken' => Csrf::token(),
            'success' => Session::getFlash('success'),
            'error' => Session::getFlash('error'),
        ];

        require __DIR__ . '/../../View/admin/layouts/base.php';
    }

    public function createForm(): void
    {
        Auth::requireRole('ADMIN', 'SUPER_ADMIN');

        $pageTitle = 'Novo documento geral';
        $contentView = __DIR__ . '/../../View/admin/general_documents/create.php';

        $viewData = [
            'mode' => 'create',
            'document' => ['category_id' => '', 'title' => '', 'description' => '', 'is_active' => 1],
            'categories' => $this->categories->all(),
            'csrfToken' => Csrf::token(),
        ];

        require __DIR__ . '/../../View/admin/layouts/base.php';
    }

    public function editForm(array $vars): void
    {
        Auth::requireRole('ADMIN', 'SUPER_ADMIN');

        $id = (int) ($vars['id'] ?? 0);
        $doc = $this->docs->find($id);
        if (!$doc) {
            Session::flash('error', 'Documento não encontrado.');
            header('Location: /admin/general-documents');
            exit;
        }

        $pageTitle = 'Editar documento geral';
        $contentView = __DIR__ . '/../../View/admin/general_documents/edit.php';

        $viewData = [
            'mode' => 'edit',
            'document' => $doc,
            'categories' => $this->categories->all(),
            'csrfToken' => Csrf::token(),
        ];

        require __DIR__ . '/../../View/admin/layouts/base.php';
    }

    public function store(): void
    {
        $admin = Auth::requireRole('ADMIN', 'SUPER_ADMIN');

        if (!Csrf::validate($_POST['_token'] ?? '')) {
            Session::flash('error', 'Sessão expirada.');
            header('Location: /admin/general-documents');
            exit;
        }

        $categoryId = (int) ($_POST['category_id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $desc = trim($_POST['description'] ?? '');
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        if ($categoryId <= 0 || $title === '' || empty($_FILES['file']['name'])) {
            Session::flash('error', 'Categoria, título e arquivo são obrigatórios.');
            header('Location: /admin/general-documents/new');
            exit;
        }

        // upload seguro (reaproveitando seu helper FileUpload)
        $uploadBase = __DIR__ . '/../../../../storage/general_documents/';
        $stored = FileUpload::store($_FILES['file'], $uploadBase, [
            'allowed_extensions' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'csv', 'png', 'jpg', 'jpeg'],
            'max_size_mb' => 100,
        ]);

        $id = $this->docs->create([
            'category_id' => $categoryId,
            'title' => $title,
            'description' => $desc,
            'file_path' => $stored['path'],
            'file_mime' => $stored['mime_type'],
            'file_size' => $stored['size'],
            'file_original_name' => $stored['original_name'],
            'is_active' => $isActive,
            'published_at' => (new \DateTimeImmutable('now'))->format('Y-m-d H:i:s'),
            'created_by_admin' => $admin['id'],
        ]);

        // Notifica usuários do portal sobre o novo documento
        try {
            $doc = $this->docs->find($id);
            if ($doc) {
                $category = $this->categories->find($categoryId);
                $docWithCategory = array_merge($doc, [
                    'category_name' => $category['name'] ?? 'Sem categoria',
                ]);
                $this->config['notification']->notifyNewGeneralDocument($docWithCategory);
            }
        } catch (\Exception $e) {
            // Não impede a criação do documento se notificação falhar
            error_log('Erro ao notificar sobre novo documento: ' . $e->getMessage());
        }

        Session::flash('success', 'Documento criado com sucesso.');
        header('Location: /admin/general-documents');
        exit;
    }

    public function update(array $vars): void
    {
        Auth::requireRole('ADMIN', 'SUPER_ADMIN');

        if (!Csrf::validate($_POST['_token'] ?? '')) {
            Session::flash('error', 'Sessão expirada.');
            header('Location: /admin/general-documents');
            exit;
        }

        $id = (int) ($vars['id'] ?? 0);
        $doc = $this->docs->find($id);
        if (!$doc) {
            Session::flash('error', 'Documento não encontrado.');
            header('Location: /admin/general-documents');
            exit;
        }

        $categoryId = (int) ($_POST['category_id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $desc = trim($_POST['description'] ?? '');
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        if ($categoryId <= 0 || $title === '') {
            Session::flash('error', 'Categoria e título são obrigatórios.');
            header("Location: /admin/general-documents/{$id}/edit");
            exit;
        }

        $this->docs->update($id, [
            'category_id' => $categoryId,
            'title' => $title,
            'description' => $desc,
            'is_active' => $isActive,
        ]);

        Session::flash('success', 'Documento atualizado com sucesso.');
        header('Location: /admin/general-documents');
        exit;
    }

    public function delete(array $vars): void
    {
        Auth::requireRole('SUPER_ADMIN');

        if (!Csrf::validate($_POST['_token'] ?? '')) {
            Session::flash('error', 'Sessão expirada.');
            header('Location: /admin/general-documents');
            exit;
        }

        $id = (int) ($vars['id'] ?? 0);
        $doc = $this->docs->find($id);
        if ($doc) {
            // opcional: remover arquivo físico
            if (is_file($doc['file_path'])) {
                @unlink($doc['file_path']);
            }
            $this->docs->delete($id);
        }

        Session::flash('success', 'Documento removido.');
        header('Location: /admin/general-documents');
        exit;
    }

    public function toggle(array $vars): void
    {
        Auth::requireRole('ADMIN', 'SUPER_ADMIN');

        if (!Csrf::validate($_POST['_token'] ?? '')) {
            Session::flash('error', 'Sessão expirada.');
            header('Location: /admin/general-documents');
            exit;
        }

        $id = (int) ($vars['id'] ?? 0);
        $doc = $this->docs->find($id);

        if (!$doc) {
            Session::flash('error', 'Documento não encontrado.');
            header('Location: /admin/general-documents');
            exit;
        }

        // Toggle status
        $newStatus = ((int) $doc['is_active'] === 1) ? 0 : 1;

        // We only need to update the status, but the repo update method expects all fields.
        // So we keep original values for others.
        // NOTE: The update method signature is: update(int $id, array $data)
        // keys: category_id, title, description, is_active

        $this->docs->update($id, [
            'category_id' => $doc['category_id'],
            'title' => $doc['title'],
            'description' => $doc['description'],
            'is_active' => $newStatus,
        ]);

        $msg = $newStatus ? 'Documento ativado.' : 'Documento desativado.';
        Session::flash('success', $msg);
        header('Location: /admin/general-documents');
        exit;
    }
}
