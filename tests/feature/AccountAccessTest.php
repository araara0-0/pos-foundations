<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;

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

    public function testGuestIsRedirectedFromUserForm(): void
    {
        $this->withSession([])->get('/users/new')->assertRedirectTo('/login');
    }

    public function testLoggedInUserCanOpenCustomerForm(): void
    {
        $response = $this->withSession(['user_id' => 1, 'username' => 'avery.admin'])->get('/customers/new');
        $response->assertOK();
        $response->assertSee('New Customer');
    }

    public function testLoggedInUserCanOpenProductForm(): void
    {
        $response = $this->withSession(['user_id' => 1, 'username' => 'avery.admin'])->get('/products/new');
        $response->assertOK();
        $response->assertSee('New Product');
        $response->assertSeeElement('input[name=image]');
    }

    public function testLoggedInUserCanOpenUserForm(): void
    {
        $response = $this->withSession(['user_id' => 1, 'username' => 'avery.admin'])->get('/users/new');
        $response->assertOK();
        $response->assertSee('New User');
        $response->assertSeeElement('input[name=avatar]');
    }
}
