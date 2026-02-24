<?php

declare(strict_types=1);

namespace Tests\Integration\Admin;

use PHPUnit\Framework\TestCase;
use App\Support\Auth;
use App\Support\Session;

class AuthenticationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $_SESSION = [];
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
        parent::tearDown();
    }

    public function testIsAdminWithoutSession(): void
    {
        $this->assertFalse(Auth::isAdmin());
    }

    public function testIsAdminWithSession(): void
    {
        Session::set('admin', [
            'id' => 1,
            'email' => 'admin@test.com',
            'name' => 'Test Admin',
        ]);
        
        $this->assertTrue(Auth::isAdmin());
    }

    public function testGetAdminWithSession(): void
    {
        $adminData = [
            'id' => 1,
            'email' => 'admin@test.com',
            'name' => 'Test Admin',
        ];
        
        Session::set('admin', $adminData);
        
        $admin = Auth::getAdmin();
        
        $this->assertIsArray($admin);
        $this->assertEquals(1, $admin['id']);
        $this->assertEquals('admin@test.com', $admin['email']);
    }

    public function testGetAdminWithoutSession(): void
    {
        $this->assertNull(Auth::getAdmin());
    }

    public function testAdminLogin(): void
    {
        $adminData = [
            'id' => 5,
            'email' => 'test@example.com',
            'name' => 'John Doe',
            'role' => 'ADMIN',
        ];
        
        Auth::loginAdmin($adminData);
        
        $this->assertTrue(Auth::isAdmin());
        $this->assertEquals($adminData, Auth::getAdmin());
    }

    public function testAdminLogout(): void
    {
        Session::set('admin', [
            'id' => 1,
            'email' => 'admin@test.com',
        ]);
        
        $this->assertTrue(Auth::isAdmin());
        
        Auth::logoutAdmin();
        
        $this->assertFalse(Auth::isAdmin());
        $this->assertNull(Auth::getAdmin());
    }

    public function testIsPortalUserWithoutSession(): void
    {
        $this->assertFalse(Auth::isPortalUser());
    }

    public function testIsPortalUserWithSession(): void
    {
        Session::set('portal_user', [
            'id' => 10,
            'email' => 'user@test.com',
        ]);
        
        $this->assertTrue(Auth::isPortalUser());
    }

    public function testGetPortalUserWithSession(): void
    {
        $userData = [
            'id' => 10,
            'email' => 'portal@test.com',
            'name' => 'Portal User',
        ];
        
        Session::set('portal_user', $userData);
        
        $user = Auth::getPortalUser();
        
        $this->assertIsArray($user);
        $this->assertEquals(10, $user['id']);
        $this->assertEquals('portal@test.com', $user['email']);
    }

    public function testPortalUserLogin(): void
    {
        $userData = [
            'id' => 15,
            'email' => 'portal@example.com',
            'name' => 'Jane Smith',
        ];
        
        Auth::loginPortalUser($userData);
        
        $this->assertTrue(Auth::isPortalUser());
        $this->assertEquals($userData, Auth::getPortalUser());
    }

    public function testPortalUserLogout(): void
    {
        Session::set('portal_user', [
            'id' => 10,
            'email' => 'user@test.com',
        ]);
        
        $this->assertTrue(Auth::isPortalUser());
        
        Auth::logoutPortalUser();
        
        $this->assertFalse(Auth::isPortalUser());
        $this->assertNull(Auth::getPortalUser());
    }

    public function testAdminAndPortalUserSeparation(): void
    {
        $adminData = ['id' => 1, 'email' => 'admin@test.com'];
        $portalData = ['id' => 10, 'email' => 'portal@test.com'];
        
        Auth::loginAdmin($adminData);
        Auth::loginPortalUser($portalData);
        
        $this->assertTrue(Auth::isAdmin());
        $this->assertTrue(Auth::isPortalUser());
        $this->assertEquals($adminData, Auth::getAdmin());
        $this->assertEquals($portalData, Auth::getPortalUser());
        
        Auth::logoutAdmin();
        
        $this->assertFalse(Auth::isAdmin());
        $this->assertTrue(Auth::isPortalUser(), 'Portal user should still be logged in');
    }

    public function testSessionPersistence(): void
    {
        $adminData = ['id' => 1, 'name' => 'Admin'];
        
        Auth::loginAdmin($adminData);
        
        // Simulate new request with same session
        $admin = Auth::getAdmin();
        
        $this->assertEquals($adminData, $admin);
    }

    public function testMultipleLogoutCalls(): void
    {
        Auth::loginAdmin(['id' => 1]);
        
        Auth::logoutAdmin();
        Auth::logoutAdmin(); // Should not throw error
        
        $this->assertFalse(Auth::isAdmin());
    }

    public function testLoginOverwrite(): void
    {
        Auth::loginAdmin(['id' => 1, 'name' => 'First Admin']);
        Auth::loginAdmin(['id' => 2, 'name' => 'Second Admin']);
        
        $admin = Auth::getAdmin();
        
        $this->assertEquals(2, $admin['id']);
        $this->assertEquals('Second Admin', $admin['name']);
    }
}
