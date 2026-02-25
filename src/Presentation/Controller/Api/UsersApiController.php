<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Api;

use App\Infrastructure\Auth\JwtService;
use App\Infrastructure\Persistence\MySqlPortalUserRepository;
use PDO;

/**
 * API Users Controller
 */
class UsersApiController
{
    private array $config;

    private PDO $pdo;

    private JwtService $jwt;

    private MySqlPortalUserRepository $userRepo;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->pdo = $config['pdo'];
        $this->jwt = new JwtService($config['app']['secret'] ?? 'default-secret', 86400);
        $this->userRepo = new MySqlPortalUserRepository($this->pdo);
    }

    /**
     * List portal users with pagination
     */
    public function list(array $params): array
    {
        $user = $this->authenticateRequest();

        if (!$user) {
            http_response_code(401);

            return ['error' => 'Unauthorized', 'message' => 'Invalid or expired token.'];
        }

        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = min(100, max(1, (int) ($_GET['per_page'] ?? 20)));

        $result = $this->userRepo->paginate($page, $perPage);

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
     * Get a single user
     */
    public function show(array $params): array
    {
        $user = $this->authenticateRequest();

        if (!$user) {
            http_response_code(401);

            return ['error' => 'Unauthorized', 'message' => 'Invalid or expired token.'];
        }

        $id = (int) ($params['id'] ?? 0);
        $portalUser = $this->userRepo->findById($id);

        if (!$portalUser) {
            http_response_code(404);

            return ['error' => 'Not Found', 'message' => 'User not found.'];
        }

        return [
            'success' => true,
            'data' => $portalUser,
        ];
    }

    /**
     * Create a new portal user
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
        if (empty($input['full_name'])) {
            http_response_code(400);

            return ['error' => 'Bad Request', 'message' => 'Field \'full_name\' is required.'];
        }

        $data = [
            'full_name' => $input['full_name'],
            'email' => $input['email'] ?? null,
            'document_number' => $input['document_number'] ?? null,
            'phone_number' => $input['phone_number'] ?? null,
            'external_id' => $input['external_id'] ?? null,
            'notes' => $input['notes'] ?? null,
            'status' => 'INVITED',
        ];

        $id = $this->userRepo->create($data);

        http_response_code(201);

        return [
            'success' => true,
            'message' => 'User created successfully.',
            'data' => [
                'id' => $id,
                'full_name' => $data['full_name'],
            ],
        ];
    }

    /**
     * Update a portal user
     */
    public function update(?array $payload = null): array
    {
        $user = $this->authenticateRequest();

        if (!$user) {
            http_response_code(401);

            return ['error' => 'Unauthorized', 'message' => 'Invalid or expired token.'];
        }

        $id = (int) ($payload['id'] ?? $_GET['id'] ?? 0);
        $input = $payload ?? json_decode(file_get_contents('php://input'), true) ?? [];

        $portalUser = $this->userRepo->findById($id);

        if (!$portalUser) {
            http_response_code(404);

            return ['error' => 'Not Found', 'message' => 'User not found.'];
        }

        $data = [];
        $allowedFields = ['full_name', 'email', 'document_number', 'phone_number', 'external_id', 'notes', 'status'];

        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $input)) {
                $data[$field] = $input[$field];
            }
        }

        if (!empty($data)) {
            $this->userRepo->update($id, $data);
        }

        return [
            'success' => true,
            'message' => 'User updated successfully.',
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
