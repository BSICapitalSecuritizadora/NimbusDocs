<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Api;

use App\Infrastructure\Auth\JwtService;
use App\Infrastructure\Persistence\MySqlPortalSubmissionRepository;
use PDO;

/**
 * API Submissions Controller
 */
class SubmissionsApiController
{
    private array $config;

    private PDO $pdo;

    private JwtService $jwt;

    private MySqlPortalSubmissionRepository $submissionRepo;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->pdo = $config['pdo'];
        $this->jwt = new JwtService($config['app']['secret'] ?? 'default-secret', 86400);
        $this->submissionRepo = new MySqlPortalSubmissionRepository($this->pdo);
    }

    /**
     * List submissions with filters and pagination
     */
    public function list(array $params): array
    {
        $user = $this->authenticateRequest();

        if (!$user) {
            http_response_code(401);

            return ['error' => 'Unauthorized', 'message' => 'Invalid or expired token.'];
        }

        $filters = [];

        if (!empty($_GET['status'])) {
            $filters['status'] = $_GET['status'];
        }

        if (!empty($_GET['user_id'])) {
            $filters['portal_user_id'] = (int) $_GET['user_id'];
        }

        if (!empty($_GET['search'])) {
            $filters['search'] = $_GET['search'];
        }

        if (!empty($_GET['date_from'])) {
            $filters['date_from'] = $_GET['date_from'];
        }

        if (!empty($_GET['date_to'])) {
            $filters['date_to'] = $_GET['date_to'];
        }

        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = min(100, max(1, (int) ($_GET['per_page'] ?? 20)));

        $result = $this->submissionRepo->paginateAll($filters, $page, $perPage);

        return [
            'success' => true,
            'data' => $result['items'],
            'meta' => [
                'page' => $result['page'],
                'per_page' => $result['perPage'],
                'total' => $result['total'],
                'total_pages' => $result['pages'],
            ],
        ];
    }

    /**
     * Get a single submission
     */
    public function show(array $params): array
    {
        $user = $this->authenticateRequest();

        if (!$user) {
            http_response_code(401);

            return ['error' => 'Unauthorized', 'message' => 'Invalid or expired token.'];
        }

        $id = (int) ($params['id'] ?? 0);
        $submission = $this->submissionRepo->findWithUserById($id);

        if (!$submission) {
            http_response_code(404);

            return ['error' => 'Not Found', 'message' => 'Submission not found.'];
        }

        return [
            'success' => true,
            'data' => $submission,
        ];
    }

    /**
     * Update submission status
     */
    public function updateStatus(?array $payload = null): array
    {
        $user = $this->authenticateRequest();

        if (!$user) {
            http_response_code(401);

            return ['error' => 'Unauthorized', 'message' => 'Invalid or expired token.'];
        }

        $id = (int) ($payload['id'] ?? $_GET['id'] ?? 0); // Check payload or get
        $input = $payload ?? json_decode(file_get_contents('php://input'), true) ?? [];
        $status = strtoupper(trim($input['status'] ?? ''));

        $validStatuses = ['PENDING', 'UNDER_REVIEW', 'APPROVED', 'NEEDS_CORRECTION', 'COMPLETED', 'REJECTED', 'CANCELLED'];

        if (!in_array($status, $validStatuses)) {
            http_response_code(400);

            return [
                'error' => 'Bad Request',
                'message' => 'Invalid status. Valid values: ' . implode(', ', $validStatuses),
            ];
        }

        $submission = $this->submissionRepo->findById($id);

        if (!$submission) {
            http_response_code(404);

            return ['error' => 'Not Found', 'message' => 'Submission not found.'];
        }

        $this->submissionRepo->updateStatus($id, $status, (int) $user['sub']);

        return [
            'success' => true,
            'message' => 'Status updated successfully.',
            'data' => [
                'id' => $id,
                'status' => $status,
            ],
        ];
    }

    /**
     * Create a new submission (for integrations)
     */
    public function create(?array $payload = null): array
    {
        $user = $this->authenticateRequest();

        if (!$user) {
            http_response_code(401);

            return ['error' => 'Unauthorized', 'message' => 'Invalid or expired token.'];
        }

        $input = $payload ?? json_decode(file_get_contents('php://input'), true) ?? [];

        // Validate required fields
        if (empty($input['portal_user_id']) || empty($input['title'])) {
            http_response_code(400);

            return ['error' => 'Bad Request', 'message' => "Fields 'portal_user_id' and 'title' are required."];
        }

        // Generate reference code
        $referenceCode = strtoupper(substr(md5(uniqid('', true)), 0, 8));

        $data = [
            'reference_code' => $referenceCode,
            'title' => $input['title'],
            'message' => $input['message'] ?? null,
            'status' => 'PENDING',
            'created_ip' => $_SERVER['REMOTE_ADDR'] ?? null,
            'created_user_agent' => 'API',
        ];

        $id = $this->submissionRepo->createForUser((int) $input['portal_user_id'], $data);

        http_response_code(201);

        return [
            'success' => true,
            'message' => 'Submission created successfully.',
            'data' => [
                'id' => $id,
                'reference_code' => $referenceCode,
            ],
        ];
    }

    /**
     * Authenticate the current request
     */
    private function authenticateRequest(): ?array
    {
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';

        if (preg_match('/^Bearer\s+(.+)$/i', $authHeader, $matches)) {
            $token = $matches[1];

            // Try JWT first
            $payload = $this->jwt->verify($token);
            if ($payload) {
                return $payload;
            }

            // Try API token
            $tokenHash = hash('sha256', $token);
            $sql = 'SELECT at.*, au.email, au.role 
                    FROM api_tokens at 
                    JOIN admin_users au ON au.id = at.admin_user_id 
                    WHERE at.token_hash = :hash 
                      AND at.revoked_at IS NULL';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['hash' => $tokenHash]);
            $apiToken = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($apiToken) {
                return [
                    'sub' => $apiToken['admin_user_id'],
                    'email' => $apiToken['email'],
                    'role' => $apiToken['role'],
                ];
            }
        }

        return null;
    }
}
