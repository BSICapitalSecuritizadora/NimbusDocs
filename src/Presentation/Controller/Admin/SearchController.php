<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Admin;

use App\Application\Service\GlobalSearchService;
use App\Support\Session;

/**
 * Controller for global search functionality
 */
class SearchController
{
    private array $config;
    private GlobalSearchService $searchService;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->searchService = new GlobalSearchService($config['pdo']);
    }

    /**
     * API endpoint for quick search (AJAX)
     */
    public function quickSearch(): void
    {
        $admin = Session::get('admin');
        if (!$admin) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        header('Content-Type: application/json; charset=utf-8');

        $query = trim($_GET['q'] ?? '');
        $results = $this->searchService->quickSearch($query, 8);

        echo json_encode([
            'success' => true,
            'results' => $results,
            'query' => $query,
        ]);
    }

    /**
     * API endpoint for full search (AJAX)
     */
    public function search(): void
    {
        $admin = Session::get('admin');
        if (!$admin) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        header('Content-Type: application/json; charset=utf-8');

        $query = trim($_GET['q'] ?? '');
        $results = $this->searchService->search($query, 15);

        echo json_encode([
            'success' => true,
            'results' => $results,
            'query' => $query,
        ]);
    }

    /**
     * Show full search results page
     */
    public function showResults(): void
    {
        $admin = Session::get('admin');
        if (!$admin) {
            header('Location: /admin/login');
            exit;
        }

        $query = trim($_GET['q'] ?? '');
        $results = $this->searchService->search($query, 50);
        $branding = $this->config['branding'] ?? [];

        require __DIR__ . '/../../View/admin/search/results.php';
    }
}
