<?php

namespace App\Controllers;

class Users extends BaseController
{
    public function index()
    {
        $users = [
            [
                'username'  => 'avery.admin',
                'full_name' => 'Avery Lim',
                'role'      => 'Administrator',
            ],
            [
                'username'  => 'bianca.cashier',
                'full_name' => 'Bianca Torres',
                'role'      => 'Cashier',
            ],
            [
                'username'  => 'carlo.stock',
                'full_name' => 'Carlo Navarro',
                'role'      => 'Inventory Clerk',
            ],
            [
                'username'  => 'dana.supervisor',
                'full_name' => 'Dana Flores',
                'role'      => 'Supervisor',
            ],
            [
                'username'  => 'ethan.manager',
                'full_name' => 'Ethan Ramos',
                'role'      => 'Store Manager',
            ],
        ];

        return view('users/index', ['users' => $users]);
    }
}
