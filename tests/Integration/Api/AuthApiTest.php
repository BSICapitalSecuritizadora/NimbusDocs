<?php

declare(strict_types=1);

namespace Tests\Integration\Api;

use App\Presentation\Controller\Api\AuthApiController;

class AuthApiTest extends ApiTestCase
{
    private AuthApiController $controller;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->controller = new AuthApiController($this->getApiConfig());

        // Setup a test user
        $this->pdo->exec("INSERT INTO admin_users (id, name, email, password_hash, role, auth_mode, is_active) VALUES (999, 'API Tester', 'api@test.com', '" . password_hash('Pass123!', PASSWORD_DEFAULT) . "', 'ADMIN', 'LOCAL_ONLY', 1)");
    }

    public function testLoginWithValidCredentials(): void
    {
        $payload = [
            'email' => 'api@test.com',
            'password' => 'Pass123!'
        ];

        $response = $this->controller->login($payload);

        $this->assertIsArray($response);
        $this->assertArrayHasKey('success', $response);
        $this->assertTrue($response['success']);
        
        $this->assertArrayHasKey('token', $response);
        $this->assertNotEmpty($response['token']);
        
        $this->assertArrayHasKey('user', $response);
        $this->assertEquals('api@test.com', $response['user']['email']);
    }

    public function testLoginWithInvalidCredentials(): void
    {
        $payload = [
            'email' => 'api@test.com',
            'password' => 'WrongPass!'
        ];

        $response = $this->controller->login($payload);

        $this->assertIsArray($response);
        $this->assertArrayHasKey('error', $response);
        $this->assertEquals('Unauthorized', $response['error']);
        $this->assertEquals(401, http_response_code());
    }

    public function testLoginWithMissingPayload(): void
    {
        $response = $this->controller->login([]); // Empty payload

        $this->assertIsArray($response);
        $this->assertArrayHasKey('error', $response);
        $this->assertEquals('Bad Request', $response['error']);
        $this->assertEquals(400, http_response_code());
    }

    public function testCreateTokenRequiresAuthentication(): void
    {
        $response = $this->controller->createToken(['name' => 'My API Key']);
        
        $this->assertIsArray($response);
        $this->assertArrayHasKey('error', $response);
        $this->assertEquals('Unauthorized', $response['error']);
    }

    public function testCreateTokenWithAuthentication(): void
    {
        $this->authenticateAs(999, 'api@test.com');
        
        $response = $this->controller->createToken(['name' => 'My API Key']);
        
        $this->assertIsArray($response);
        $this->assertArrayHasKey('success', $response);
        $this->assertTrue($response['success']);
        $this->assertArrayHasKey('token', $response);
        $this->assertNotEmpty($response['token']);
    }
}
