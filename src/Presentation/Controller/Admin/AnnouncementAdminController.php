<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Admin;

use App\Infrastructure\Persistence\MySqlPortalAnnouncementRepository;
use App\Support\Auth;
use App\Support\Session;
use App\Support\Csrf;

final class AnnouncementAdminController
{
    private MySqlPortalAnnouncementRepository $repo;

    public function __construct(private array $config)
    {
        $this->repo = new MySqlPortalAnnouncementRepository($config['pdo']);
    }

    public function index(): void
    {
        $admin = Auth::requireRole('ADMIN', 'SUPER_ADMIN');

        $announcements = $this->repo->listAll();

        $pageTitle   = 'Comunicados do Portal';
        $contentView = __DIR__ . '/../../View/admin/announcements/index.php';

        $viewData = [
            'admin'         => $admin,
            'announcements' => $announcements,
            'csrfToken'     => Csrf::token(),
            'success'       => Session::getFlash('success'),
            'error'         => Session::getFlash('error'),
        ];

        require __DIR__ . '/../../View/admin/layouts/base.php';
    }

    public function createForm(): void
    {
        $admin = Auth::requireRole('ADMIN', 'SUPER_ADMIN');

        $pageTitle   = 'Novo comunicado';
        $contentView = __DIR__ . '/../../View/admin/announcements/form.php';

        $viewData = [
            'admin'     => $admin,
            'csrfToken' => Csrf::token(),
            'mode'      => 'create',
            'data'      => [
                'title'     => '',
                'body'      => '',
                'level'     => 'info',
                'starts_at' => '',
                'ends_at'   => '',
                'is_active' => 1,
            ],
        ];

        require __DIR__ . '/../../View/admin/layouts/base.php';
    }

    public function editForm(array $vars): void
    {
        $admin = Auth::requireRole('ADMIN', 'SUPER_ADMIN');
        $id    = (int)($vars['id'] ?? 0);

        $announcement = $this->repo->find($id);
        if (!$announcement) {
            Session::flash('error', 'Comunicado não encontrado.');
            header('Location: /admin/announcements');
            exit;
        }

        $pageTitle   = 'Editar comunicado';
        $contentView = __DIR__ . '/../../View/admin/announcements/form.php';

        $viewData = [
            'admin'     => $admin,
            'csrfToken' => Csrf::token(),
            'mode'      => 'edit',
            'data'      => $announcement,
        ];

        require __DIR__ . '/../../View/admin/layouts/base.php';
    }

    public function store(): void
    {
        $admin = Auth::requireRole('ADMIN', 'SUPER_ADMIN');

        if (!Csrf::validate($_POST['_token'] ?? '')) {
            Session::flash('error', 'Sessão expirada.');
            header('Location: /admin/announcements');
            exit;
        }

        $title   = trim($_POST['title'] ?? '');
        $body    = trim($_POST['body'] ?? '');
        $level   = $_POST['level'] ?? 'info';
        $active  = isset($_POST['is_active']) ? 1 : 0;

        $starts  = trim($_POST['starts_at'] ?? '');
        $ends    = trim($_POST['ends_at'] ?? '');

        $startsAt = $starts !== '' ? $starts . ' 00:00:00' : null;
        $endsAt   = $ends   !== '' ? $ends   . ' 23:59:59' : null;

        if ($title === '' || $body === '') {
            Session::flash('error', 'Título e mensagem são obrigatórios.');
            header('Location: /admin/announcements/new');
            exit;
        }

        $this->repo->create([
            'title'            => $title,
            'body'             => $body,
            'level'            => $level,
            'starts_at'        => $startsAt,
            'ends_at'          => $endsAt,
            'is_active'        => $active,
            'created_by_admin' => $admin['id'],
        ]);

        Session::flash('success', 'Comunicado criado com sucesso.');
        header('Location: /admin/announcements');
        exit;
    }

    public function update(array $vars): void
    {
        $admin = Auth::requireRole('ADMIN', 'SUPER_ADMIN');
        $id    = (int)($vars['id'] ?? 0);

        if (!Csrf::validate($_POST['_token'] ?? '')) {
            Session::flash('error', 'Sessão expirada.');
            header('Location: /admin/announcements');
            exit;
        }

        $announcement = $this->repo->find($id);
        if (!$announcement) {
            Session::flash('error', 'Comunicado não encontrado.');
            header('Location: /admin/announcements');
            exit;
        }

        $title   = trim($_POST['title'] ?? '');
        $body    = trim($_POST['body'] ?? '');
        $level   = $_POST['level'] ?? 'info';
        $active  = isset($_POST['is_active']) ? 1 : 0;

        $starts  = trim($_POST['starts_at'] ?? '');
        $ends    = trim($_POST['ends_at'] ?? '');

        $startsAt = $starts !== '' ? $starts . ' 00:00:00' : null;
        $endsAt   = $ends   !== '' ? $ends   . ' 23:59:59' : null;

        if ($title === '' || $body === '') {
            Session::flash('error', 'Título e mensagem são obrigatórios.');
            header("Location: /admin/announcements/{$id}/edit");
            exit;
        }

        $this->repo->update($id, [
            'title'     => $title,
            'body'      => $body,
            'level'     => $level,
            'starts_at' => $startsAt,
            'ends_at'   => $endsAt,
            'is_active' => $active,
        ]);

        Session::flash('success', 'Comunicado atualizado com sucesso.');
        header('Location: /admin/announcements');
        exit;
    }

    public function delete(array $vars): void
    {
        $admin = Auth::requireRole('SUPER_ADMIN'); // só super admin apaga
        $id    = (int)($vars['id'] ?? 0);

        if (!Csrf::validate($_POST['_token'] ?? '')) {
            Session::flash('error', 'Sessão expirada.');
            header('Location: /admin/announcements');
            exit;
        }

        $this->repo->delete($id);

        Session::flash('success', 'Comunicado removido.');
        header('Location: /admin/announcements');
        exit;
    }
}
