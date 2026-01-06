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

        // Build Filter Query
        $where = [];
        $params = [];

        if (!empty($_GET['actor_type'])) {
            $where[] = "actor_type = :actor_type";
            $params[':actor_type'] = $_GET['actor_type'];
        }

        if (!empty($_GET['action'])) {
            $where[] = "action LIKE :action";
            $params[':action'] = '%' . $_GET['action'] . '%';
        }

        if (!empty($_GET['search'])) {
            $term = '%' . $_GET['search'] . '%';
            // Search in details JSON, actor_id, or IP
            $where[] = "(details LIKE :search OR ip_address LIKE :search OR actor_id LIKE :search OR target_id LIKE :search)";
            $params[':search'] = $term;
        }

        $whereSql = '';
        if (count($where) > 0) {
            $whereSql = 'WHERE ' . implode(' AND ', $where);
        }

        // Count Total
        $stmtCount = $pdo->prepare("SELECT COUNT(*) FROM audit_logs $whereSql");
        foreach ($params as $key => $val) {
            $stmtCount->bindValue($key, $val);
        }
        $stmtCount->execute();
        $total = (int)$stmtCount->fetchColumn();

        // Fetch Items
        $sql = "SELECT * FROM audit_logs $whereSql ORDER BY id DESC LIMIT :limit OFFSET :offset";
        $stmt = $pdo->prepare($sql);
        
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
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
                'pages' => max(1, (int)ceil($total / $perPage)),
            ],
            'csrfToken' => Csrf::token(),
        ];

        require __DIR__ . '/../../View/admin/layouts/base.php';
    }
}
