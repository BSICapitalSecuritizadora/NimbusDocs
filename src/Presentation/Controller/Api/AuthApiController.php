<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Api;

use App\Infrastructure\Auth\JwtService;
use App\Infrastructure\Persistence\MySqlAdminUserRepository;
use PDO;

/**
 * API Authentication Controller
 */
class AuthApiController
{
    private array $config;
    private PDO $pdo;
    private JwtService $jwt;
    private MySqlAdminUserRepository $userRepo;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->pdo = $config['pdo'];
        $this->jwt = new JwtService($config['app']['secret'] ?? 'default-secret', 86400);
        $this->userRepo = new MySqlAdminUserRepository($this->pdo);
    }

    /**
     * Login and get JWT token
     */
    public function login(array $params): array
    {
        // Get JSON payload
        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        
        $email = trim($input['email'] ?? '');
        $password = $input['password'] ?? '';

        if (empty($email) || empty($password)) {
            http_response_code(400);
            return [
                'error' => 'Bad Request',
                'message' => 'Email and password are required.',
            ];
        }

        // Find user
        $user = $this->userRepo->findActiveByEmail($email);

        if (!$user) {
            http_response_code(401);
            return [
                'error' => 'Unauthorized',
                'message' => 'Invalid credentials.',
            ];
        }

        // Check auth mode
        if (!in_array($user['auth_mode'], ['LOCAL_ONLY', 'LOCAL_AND_MS'], true)) {
            http_response_code(401);
            return [
                'error' => 'Unauthorized',
                'message' => 'This account uses Microsoft authentication only.',
            ];
        }

        // Verify password
        if (empty($user['password_hash']) || !password_verify($password, $user['password_hash'])) {
            http_response_code(401);
            return [
                'error' => 'Unauthorized',
                'message' => 'Invalid credentials.',
            ];
        }

        // Generate JWT
        $token = $this->jwt->generate([
            'sub' => $user['id'],
            'email' => $user['email'],
            'name' => $user['name'] ?? $user['full_name'] ?? '',
            'role' => $user['role'],
        ]);

        // Update last login
        $this->userRepo->updateLastLogin((int) $user['id'], 'API');

        return [
            'success' => true,
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => 86400,
            'user' => [
                'id' => $user['id'],
                'name' => $user['name'] ?? $user['full_name'] ?? '',
                'email' => $user['email'],
                'role' => $user['role'],
            ],
        ];
    }

    /**
     * Create a long-lived API token
     */
    public function createToken(array $params): array
    {
        $user = $this->authenticateRequest();
        
        if (!$user) {
            http_response_code(401);
            return ['error' => 'Unauthorized', 'message' => 'Invalid or expired token.'];
        }

        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $name = trim($input['name'] ?? 'API Token');

        // Generate token
        $token = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $token);
        $last4 = substr($token, -4);

        // Store in database
        $sql = "INSERT INTO api_tokens (admin_user_id, name, token_hash, last_4) VALUES (:uid, :name, :hash, :last4)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'uid' => $user['sub'],
            'name' => $name,
            'hash' => $tokenHash,
            'last4' => $last4,
        ]);

        return [
            'success' => true,
            'token' => $token,
            'name' => $name,
            'message' => 'Store this token securely. It cannot be retrieved again.',
        ];
    }

    /**
     * Revoke an API token
     */
    public function revokeToken(array $params): array
    {
        $user = $this->authenticateRequest();
        
        if (!$user) {
            http_response_code(401);
            return ['error' => 'Unauthorized', 'message' => 'Invalid or expired token.'];
        }

        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $tokenId = (int) ($input['token_id'] ?? 0);

        if ($tokenId <= 0) {
            http_response_code(400);
            return ['error' => 'Bad Request', 'message' => 'Token ID is required.'];
        }

        $sql = "UPDATE api_tokens SET revoked_at = NOW() WHERE id = :id AND admin_user_id = :uid";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $tokenId, 'uid' => $user['sub']]);

        return [
            'success' => $stmt->rowCount() > 0,
            'message' => $stmt->rowCount() > 0 ? 'Token revoked.' : 'Token not found.',
        ];
    }

    /**
     * Authenticate the current request and return user data
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
            $sql = "SELECT at.*, au.email, au.role 
                    FROM api_tokens at 
                    JOIN admin_users au ON au.id = at.admin_user_id 
                    WHERE at.token_hash = :hash 
                      AND at.revoked_at IS NULL 
                      AND (at.expires_at IS NULL OR at.expires_at > NOW())";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['hash' => $tokenHash]);
            $apiToken = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($apiToken) {
                // Update last used
                $this->pdo->prepare("UPDATE api_tokens SET last_used_at = NOW() WHERE id = :id")
                    ->execute(['id' => $apiToken['id']]);
                
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
