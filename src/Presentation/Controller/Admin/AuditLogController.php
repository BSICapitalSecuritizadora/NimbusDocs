<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Admin;

use App\Support\Csrf;
use App\Support\Session;
use PDO;

final class AuditLogController
{
    public function __construct(private array $config) {}

    private function requireAdmin(): array
    {
        $admin = Session::get('admin');
        if (!$admin) {
            http_response_code(403);
            echo '403 - Não autorizado.';
            exit;
        }

        return $admin;
    }

    public function index(array $vars = []): void
    {
        $this->requireAdmin();

        $pdo = $this->config['pdo'];
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = 20;
        $offset = max(0, ($page - 1) * $perPage);

        $total = (int)$pdo->query('SELECT COUNT(*) FROM audit_logs')->fetchColumn();

        // Ordena de forma compatível com esquemas antigos/novos
        // Usamos id DESC como fallback neutro
        $stmt = $pdo->prepare('SELECT * FROM audit_logs ORDER BY id DESC LIMIT :limit OFFSET :offset');
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $pageTitle = 'Auditoria do Sistema';
        $contentView = __DIR__ . '/../../View/admin/audit_logs/index.php';
        $viewData = [
            'pagination' => [
                'items' => $items,
                'total' => $total,
                'page' => $page,
                'perPage' => $perPage,
                'pages' => (int)ceil($total / $perPage),
            ],
            'csrfToken' => Csrf::token(),
        ];

        require __DIR__ . '/../../View/admin/layouts/base.php';
    }
}
