<?php

declare(strict_types=1);

namespace Tests\E2E;

class AuthenticationE2ETest extends E2ETestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Setup a test user
        $this->pdo->exec("INSERT INTO admin_users (id, name, email, password_hash, role, auth_mode, is_active) VALUES (999, 'E2E Admin Tester', 'e2e@test.com', '" . password_hash('Pass123!', PASSWORD_DEFAULT) . "', 'ADMIN', 'LOCAL_ONLY', 1)");
    }

    public function testAdminLoginFlow(): void
    {
        $client = static::createPantherClient();

        // Go to Admin Login
        $crawler = $client->request('GET', '/admin/login');

        $client->waitFor('form', 5);

        // Verify we are on the login page
        $this->assertStringContainsStringIgnoringCase('NimbusDocs', $crawler->filter('h1')->text());
        $this->assertStringContainsStringIgnoringCase('Painel Administrativo', $crawler->filter('p.nd-login-subtitle')->text());

        // Fill the form and submit
        $form = $crawler->selectButton('ACESSAR PLATAFORMA')->form([
            'email' => 'e2e@test.com',
            'password' => 'Pass123!',
        ]);
        $client->submit($form);

        // Wait for redirect to dashboard
        $client->waitFor('.nd-main', 10);

        // Verify successful login
        $this->assertStringContainsStringIgnoringCase('Dashboard', $client->getTitle());
        $this->assertStringContainsStringIgnoringCase('E2E Admin Tester', $client->getPageSource());

        // Test Logout
        $client->clickLink('Sair');

        // Wait to return to login
        $client->waitFor('form');

        $this->assertStringContainsString('Entrar com Microsoft', $client->getPageSource());
    }
}
