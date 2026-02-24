<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;
use App\Infrastructure\Persistence\MySqlAdminUserRepository;
use App\Infrastructure\Persistence\MySqlPortalUserRepository;
use App\Infrastructure\Persistence\MySqlPortalAccessTokenRepository;
use App\Presentation\Controller\Admin\Auth\LoginController;
use App\Presentation\Controller\Portal\Auth\PortalLoginController;

class AuthenticationTest extends TestCase
{
    private array $config;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Mock config for controllers
        $this->config = [
            'pdo' => $this->pdo,
            'recaptcha_secret' => 'dummy',
            'branding' => []
        ];

        // Seed an admin user
        $adminRepo = new MySqlAdminUserRepository($this->pdo);
        $adminRepo->create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password_hash' => password_hash('Admin123!', PASSWORD_ARGON2ID),
            'role' => 'ADMIN',
            'is_active' => 1
        ]);

        // Seed a portal user
        $portalRepo = new MySqlPortalUserRepository($this->pdo);
        $portalUserId = $portalRepo->create([
            'full_name' => 'Portal Test',
            'email' => 'portal@test.com',
            'status' => 'ACTIVE'
        ]);
        
        // Create an access token for the portal user
        $tokenRepo = new MySqlPortalAccessTokenRepository($this->pdo);
        $tokenRepo->create([
            'portal_user_id' => $portalUserId,
            'code'           => 'VALIDCODE',
            'expires_at'     => date('Y-m-d H:i:s', time() + 3600)
        ]);

        // Mock Session
        $this->setSession([
            '_csrf_token' => 'test-csrf-token',
            '_csrf_token_ts' => time()
        ]);
    }

    public function testAdminLoginWithValidCredentials(): void
    {
        // Mock POST request
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            '_token' => 'test-csrf-token',
            'email' => 'admin@test.com',
            'password' => 'Admin123!'
        ];

        $controller = new LoginController($this->config);
        
        // Catch redirect exception or output
        try {
            $controller->handleLogin();
        } catch (\Throwable $e) {
            echo "ADMIN_LOGIN_VALID_ERROR: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n";
        }

        // Assert session has admin
        $this->assertArrayHasKey('admin', $_SESSION, 'Admin should be logged in');
        $this->assertEquals('admin@test.com', $_SESSION['admin']['email'], 'Logged in user should be admin@test.com');
    }

    public function testAdminLoginWithInvalidCredentials(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            '_token' => 'test-csrf-token',
            'email' => 'admin@test.com',
            'password' => 'WrongPassword!'
        ];

        $controller = new LoginController($this->config);
        
        try {
            $controller->handleLogin();
        } catch (\Throwable $e) {
            echo "ADMIN_LOGIN_INVALID_ERROR: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n";
        }

        $this->assertArrayNotHasKey('admin', $_SESSION, 'Admin should NOT be logged in with wrong password');
        $this->assertArrayHasKey('error', $_SESSION['_flash'] ?? [], 'Should have an error flash message');
    }

    public function testPortalLoginWithValidCredentials(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            '_token' => 'test-csrf-token',
            'access_code' => 'VALIDCODE'
        ];

        $controller = new PortalLoginController($this->config);
        
        try {
            $controller->handleLogin();
        } catch (\Throwable $e) {
            echo "PORTAL_LOGIN_VALID_ERROR: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n";
        }

        $this->assertArrayHasKey('portal_user', $_SESSION, 'Portal user should be logged in');
        $this->assertEquals('portal@test.com', $_SESSION['portal_user']['email'], 'Logged in user should be portal@test.com');
    }

    public function testPortalLoginWithInvalidCredentials(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            '_token' => 'test-csrf-token',
            'access_code' => 'INVALIDCODE'
        ];

        $controller = new PortalLoginController($this->config);
        
        try {
            $controller->handleLogin();
        } catch (\Throwable $e) {
            echo "PORTAL_LOGIN_INVALID_ERROR: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n";
        }

        $this->assertArrayNotHasKey('portal_user', $_SESSION, 'Portal user should NOT be logged in');
    }
}
