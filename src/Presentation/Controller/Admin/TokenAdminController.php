<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Admin;

use App\Infrastructure\Persistence\MySqlPortalAccessTokenRepository;
use App\Support\Session;
use App\Support\Csrf;

final class TokenAdminController
{
    private MySqlPortalAccessTokenRepository $tokenRepo;

    public function __construct(private array $config)
    {
        $this->tokenRepo = new MySqlPortalAccessTokenRepository($config['pdo']);
    }

    private function requireAdmin(): array
    {
        $admin = Session::get('admin');
        if (!$admin) {
            header('Location: /admin/login');
            exit;
        }
        return $admin;
    }

    private function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }

    public function index(array $vars = []): void
    {
        $admin = $this->requireAdmin();

        $page    = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = 25;

        $filters = [
            'status' => $_GET['status'] ?? '',
            'search' => $_GET['search'] ?? '',
        ];

        $result = $this->tokenRepo->paginate($page, $perPage, $filters);
        $total  = $result['total'];
        $items  = $result['items'];

        $totalPages = (int)max(1, ceil($total / $perPage));

        $pageTitle   = 'Tokens de acesso do portal';
        $contentView = __DIR__ . '/../../View/admin/tokens/index.php';

        $viewData = [
            'admin'      => $admin,
            'items'      => $items,
            'page'       => $page,
            'perPage'    => $perPage,
            'total'      => $total,
            'totalPages' => $totalPages,
            'filters'    => $filters,
            'csrfToken'  => Csrf::token(),
        ];

        require __DIR__ . '/../../View/admin/layouts/base.php';
    }

    public function show(array $vars = []): void
    {
        $admin = $this->requireAdmin();

        $id    = (int)($vars['id'] ?? 0);
        $token = $this->tokenRepo->findById($id);

        if (!$token) {
            http_response_code(404);
            echo 'Token não encontrado.';
            return;
        }

        $pageTitle   = 'Detalhes do token';
        $contentView = __DIR__ . '/../../View/admin/tokens/show.php';

        $viewData = [
            'admin' => $admin,
            'token' => $token,
        ];

        require __DIR__ . '/../../View/admin/layouts/base.php';
    }

    public function revoke(array $vars = []): void
    {
        $this->requireAdmin();

        $id    = (int)($vars['id'] ?? 0);
        $post  = $_POST;
        $token = $post['_token'] ?? '';

        if (!Csrf::validate($token)) {
            Session::flash('error', 'Sessão expirada. Tente novamente.');
            $this->redirect('/admin/tokens');
        }

        $record = $this->tokenRepo->findById($id);
        if (!$record) {
            Session::flash('error', 'Token não encontrado.');
            $this->redirect('/admin/tokens');
        }

        $this->tokenRepo->revoke($id);

        // opcional: escrever audit_log aqui, se quiser usar $this->config['audit']

        Session::flash('success', 'Token revogado/invalidado com sucesso.');
        $this->redirect('/admin/tokens');
    }
}
