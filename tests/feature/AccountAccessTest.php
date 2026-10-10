<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use Config\Database;

/** @internal */
final class AccountAccessTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    public function testGuestIsRedirectedFromCustomerForm(): void
    {
        $this->withSession([])->get('/customers/new')->assertRedirectTo('/login');
    }

    public function testGuestIsRedirectedFromProductForm(): void
    {
        $this->withSession([])->get('/products/new')->assertRedirectTo('/login');
    }

    public function testGuestIsRedirectedFromSaleForm(): void
    {
        $this->withSession([])->get('/sales/new')->assertRedirectTo('/login');
    }

    public function testGuestIsRedirectedFromSalesHistory(): void
    {
        $this->withSession([])->get('/sales')->assertRedirectTo('/login');
    }

    public function testGuestIsRedirectedFromUserForm(): void
    {
        $this->withSession([])->get('/users/new')->assertRedirectTo('/login');
    }

    public function testLoggedInUserCanOpenCustomerForm(): void
    {
        $response = $this->withSession(['user_id' => $this->createStaff(), 'username' => 'feature.staff'])->get('/customers/new');
        $response->assertOK();
        $response->assertSee('New Customer');
    }

    public function testLoggedInUserCanOpenProductForm(): void
    {
        $response = $this->withSession(['user_id' => $this->createStaff(), 'username' => 'feature.staff'])->get('/products/new');
        $response->assertOK();
        $response->assertSee('New Product');
        $response->assertSeeElement('input[name=image]');
    }

    public function testLoggedInUserCanOpenUserForm(): void
    {
        $response = $this->withSession(['user_id' => $this->createStaff(), 'username' => 'feature.staff'])->get('/users/new');
        $response->assertOK();
        $response->assertSee('New User');
        $response->assertSeeElement('input[name=avatar]');
    }

    public function testDeletedStaffSessionCannotOpenManagementPages(): void
    {
        $staffId = $this->createStaff();
        Database::connect('tests')->table('users')->where('id', $staffId)->delete();

        $this->withSession(['user_id' => $staffId, 'username' => 'feature.staff'])
            ->get('/products/new')
            ->assertRedirectTo('/login');

        $this->assertNull(session()->get('user_id'));
        $this->assertNull(session()->get('username'));
    }

    private function createStaff(): int
    {
        $db = Database::connect('tests');
        $table = $db->getPrefix() . 'users';
        $db->query("CREATE TABLE IF NOT EXISTS {$table} (id INTEGER PRIMARY KEY AUTOINCREMENT, username VARCHAR(50) NOT NULL, full_name VARCHAR(100) NOT NULL, password VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL)");
        $db->table('users')->insert([
            'username' => 'feature.' . bin2hex(random_bytes(8)),
            'full_name' => 'Feature Test Staff',
            'password' => password_hash('test-password', PASSWORD_DEFAULT),
            'created_at' => '2026-10-10 10:00:00',
        ]);

        return (int) $db->insertID();
    }
}
