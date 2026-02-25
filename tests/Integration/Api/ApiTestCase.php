<?php

declare(strict_types=1);

namespace Tests\Integration\Api;

use App\Infrastructure\Auth\JwtService;
use Tests\TestCase;

abstract class ApiTestCase extends TestCase
{
    protected JwtService $jwt;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure standard testing environment for API
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        $_SERVER['HTTP_USER_AGENT'] = 'PHPUnit API Test';

        $this->jwt = new JwtService($_ENV['APP_SECRET'] ?? 'default-secret', 86400);
    }

    protected function tearDown(): void
    {
        unset($_SERVER['HTTP_AUTHORIZATION']);
        parent::tearDown();
    }

    /**
     * Helper to prepare the environment as an authenticated request
     */
    protected function authenticateAs(int $userId, string $email, string $role = 'ADMIN'): string
    {
        $token = $this->jwt->generate([
            'sub' => $userId,
            'email' => $email,
            'name' => 'API User',
            'role' => $role,
        ]);

        $_SERVER['HTTP_AUTHORIZATION'] = "Bearer {$token}";

        return $token;
    }

    /**
     * Get the standardized configuration array injected into API controllers
     */
    protected function getApiConfig(): array
    {
        return [
            'pdo' => $this->pdo,
            'app' => [
                'secret' => $_ENV['APP_SECRET'] ?? 'default-secret',
            ],
            // Add other dependencies as needed by controllers
        ];
    }
}
