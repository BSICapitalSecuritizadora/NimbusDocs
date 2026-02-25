<?php

declare(strict_types=1);

namespace Tests\E2E;

class SubmissionE2ETest extends E2ETestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Setup a test user
        $this->pdo->exec("INSERT INTO portal_users (id, full_name, email, document_number, status) VALUES (888, 'Portal E2E Test', 'portal@test.com', '12345678909', 'ACTIVE')");
        // For Portal, login uses access_codes so let's mock an access code for this user
        $code = \App\Support\Encrypter::hash('E2E123');
        $expires = '2099-12-31 23:59:59';
        $this->pdo->exec("INSERT INTO portal_access_tokens (portal_user_id, code, expires_at, status) VALUES (888, '{$code}', '{$expires}', 'PENDING')");
    }

    public function testPortalLoginAndCreateSubmission(): void
    {
        $client = static::createPantherClient();
        
        // Go to Portal Login
        $crawler = $client->request('GET', '/portal/login');
        
        $client->waitFor('form', 5);
        
        // Verify we are on the portal page
        $this->assertStringContainsStringIgnoringCase('Portal do Cliente', $crawler->filter('h1')->text());
        
        // Fill the portal login form
        $form = $crawler->selectButton('Acessar Portal')->form([
            'access_code' => 'E2E123'
        ]);
        
        $client->submit($form);
        
        // Wait for portal dashboard (portal uses .portal-main as main container)
        $client->waitFor('.portal-main', 10);
        
        // Verify we're logged in (title contains "Minhas informações" or app name)
        $this->assertStringContainsStringIgnoringCase('NimbusDocs', $client->getTitle());
        
        // Navigate to New Submission page
        $crawler = $client->request('GET', '/portal/submissions/new');
        
        // Wait for the submission creation form/stepper
        $client->waitFor('form', 10);
        $this->assertStringContainsStringIgnoringCase('Nova', $client->getTitle());
    }
}

