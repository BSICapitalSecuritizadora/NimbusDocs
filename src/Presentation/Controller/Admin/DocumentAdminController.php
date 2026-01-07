<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Admin;

use App\Infrastructure\Persistence\MySqlPortalDocumentRepository;
use App\Infrastructure\Persistence\MySqlPortalUserRepository;
use App\Support\FileUpload;
use App\Support\Auth;
use App\Support\Session;
use App\Support\Csrf;

final class DocumentAdminController
{
    private MySqlPortalDocumentRepository $documents;
    private MySqlPortalUserRepository $users;

    public function __construct(private array $config)
    {
        $pdo = $config['pdo'];
        $this->documents = new MySqlPortalDocumentRepository($pdo);
        $this->users     = new MySqlPortalUserRepository($pdo);
    }

    public function index(): void
    {
        Auth::requireRole('ADMIN', 'SUPER_ADMIN');

        $pageTitle   = 'Documentos do Portal';
        $contentView = __DIR__ . '/../../View/admin/documents/index.php';

        $allDocuments = $this->documents->getAll(); // você cria esse método se quiser

        $viewData = [
            'documents' => $allDocuments,
            'csrfToken' => Csrf::token(),
        ];

        require __DIR__ . '/../../View/admin/layouts/base.php';
    }

    public function show(array $vars): void
    {
        Auth::requireRole('ADMIN', 'SUPER_ADMIN');

        $id = (int)($vars['id'] ?? 0);
        $document = $this->documents->find($id);

        if (!$document) {
            Session::flash('error', 'Documento não encontrado.');
            header('Location: /admin/documents');
            exit;
        }

        // Get user info
        $user = $this->users->findById((int)$document['portal_user_id']);

        $pageTitle   = 'Detalhes do Documento';
        $contentView = __DIR__ . '/../../View/admin/documents/show.php';

        $viewData = [
            'document'  => $document,
            'user'      => $user,
            'csrfToken' => Csrf::token(),
        ];

        require __DIR__ . '/../../View/admin/layouts/base.php';
    }

    public function createForm(): void
    {
        Auth::requireRole('ADMIN', 'SUPER_ADMIN');

        $pageTitle   = 'Enviar documento';
        $contentView = __DIR__ . '/../../View/admin/documents/create.php';

        $viewData = [
            'users'     => $this->users->all(),
            'csrfToken' => Csrf::token(),
        ];

        require __DIR__ . '/../../View/admin/layouts/base.php';
    }

    public function create(): void
    {
        $admin = Auth::requireRole('ADMIN', 'SUPER_ADMIN');

        if (!Csrf::validate($_POST['_token'] ?? '')) {
            Session::flash('error', 'Sessão expirada.');
            header('Location: /admin/documents');
            exit;
        }

        $portalUserId = (int)$_POST['portal_user_id'];
        $title        = trim($_POST['title'] ?? '');
        $description  = trim($_POST['description'] ?? '');

        // upload seguro (garante diretório base)
        $uploadBase = dirname(__DIR__, 4) . '/storage/documents/' . $portalUserId . '/';
        if (!is_dir($uploadBase) && !mkdir($uploadBase, 0775, true) && !is_dir($uploadBase)) {
            Session::flash('error', 'Falha ao preparar diretório de documentos.');
            header('Location: /admin/documents');
            exit;
        }
        $storedFile = FileUpload::store($_FILES['file'], $uploadBase);

        $id = $this->documents->create([
            'portal_user_id'      => $portalUserId,
            'title'               => $title,
            'description'         => $description,
            'file_path'           => $storedFile['path'],
            'file_original_name'  => $storedFile['original_name'],
            'file_size'           => $storedFile['size'],
            'file_mime'           => $storedFile['mime_type'],
            'created_by_admin'    => $admin['id'],
        ]);

        // notificar usuário (se ativado)
        $portalUser = $this->users->findById($portalUserId);
        $submissionDummy = ['id' => $id, 'title' => $title];

        $notif = $this->config['notifications_service'] ?? null;
        if ($notif) {
            $notif->portalSubmissionResponseUploaded($portalUser, $submissionDummy);
        }

        Session::flash('success', 'Documento enviado com sucesso.');
        header('Location: /admin/documents');
        exit;
    }

    public function delete(array $vars): void
    {
        $admin = Auth::requireRole('ADMIN', 'SUPER_ADMIN');

        if (!Csrf::validate($_POST['_token'] ?? '')) {
            Session::flash('error', 'Sessão expirada.');
            header('Location: /admin/documents');
            exit;
        }

        $id = (int)($vars['id'] ?? 0);
        $doc = $this->documents->find($id);
        if ($doc) {
            // Remove arquivo do disco (best-effort)
            $path = $doc['file_path'] ?? null;
            if ($path && is_file($path)) {
                @unlink($path);
            }
            $this->documents->delete($id);
        }

        Session::flash('success', 'Documento excluído.');
        header('Location: /admin/documents');
        exit;
    }
}
