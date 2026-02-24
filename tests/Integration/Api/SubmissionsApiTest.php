<?php

declare(strict_types=1);

namespace Tests\Integration\Api;

use App\Presentation\Controller\Api\SubmissionsApiController;

class SubmissionsApiTest extends ApiTestCase
{
    private SubmissionsApiController $controller;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->controller = new SubmissionsApiController($this->getApiConfig());

        // Setup test data
        $this->pdo->exec("INSERT INTO portal_users (id, full_name, email, document_number) VALUES (888, 'Portal Test', 'portal@test.com', '12345678909')");
        $this->pdo->exec("INSERT INTO admin_users (id, name, email, password_hash, role, auth_mode, is_active) VALUES (999, 'API Tester', 'api@test.com', 'hash', 'ADMIN', 'LOCAL_ONLY', 1)");
    }

    public function testListRequiresAuthentication(): void
    {
        $response = $this->controller->list([]);
        
        $this->assertIsArray($response);
        $this->assertArrayHasKey('error', $response);
        $this->assertEquals('Unauthorized', $response['error']);
        $this->assertEquals(401, http_response_code());
    }

    public function testCreateSubmissionWithAuthentication(): void
    {
        $this->authenticateAs(999, 'api@test.com');
        
        $payload = [
            'portal_user_id' => 888,
            'title' => 'API Submission Test',
            'message' => 'This was sent via automated integration test'
        ];

        $response = $this->controller->create($payload);
        
        $this->assertIsArray($response);
        $this->assertArrayHasKey('success', $response);
        $this->assertTrue($response['success']);
        
        $this->assertArrayHasKey('data', $response);
        $this->assertArrayHasKey('id', $response['data']);
        $this->assertArrayHasKey('reference_code', $response['data']);
        
        $this->assertEquals(201, http_response_code());
    }

    public function testCreateSubmissionMissingFields(): void
    {
        $this->authenticateAs(999, 'api@test.com');
        
        // Missing title
        $payload = [
            'portal_user_id' => 888,
        ];

        $response = $this->controller->create($payload);
        
        $this->assertIsArray($response);
        $this->assertArrayHasKey('error', $response);
        $this->assertEquals('Bad Request', $response['error']);
        $this->assertEquals(400, http_response_code());
    }

    public function testUpdateStatusWithAuthentication(): void
    {
        $this->authenticateAs(999, 'api@test.com');
        
        // Setup a submission directly in DB to update
        $this->pdo->exec("INSERT INTO portal_submissions (id, portal_user_id, reference_code, title, status) VALUES (555, 888, 'REF123', 'Update Me', 'PENDING')");
        
        $payload = [
            'id' => 555,
            'status' => 'COMPLETED'
        ];
        
        // Note: For actual GET parms mock, we inject to payload as modified in the controller
        $response = $this->controller->updateStatus($payload);
        
        $this->assertIsArray($response);
        $this->assertArrayHasKey('success', $response);
        $this->assertTrue($response['success']);
        $this->assertEquals('COMPLETED', $response['data']['status']);
    }
}
