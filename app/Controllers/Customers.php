<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Customers extends BaseController
{
    public function index()
    {
        $customers = [
            [
                'full_name' => 'Sample Customer 1',
                'email'     => '1sample@example.com',
                'phone'     => '09123456789',
            ],

            [
                'full_name' => 'Sample Customer 2',
                'email'     => '2sample@example.com',
                'phone'     => '09353533534',
            ],


            [
                'full_name' => 'Sample Customer 3',
                'email'     => '3sample@example.com',
                'phone'     => '09242422424',
            ],



            [
                'full_name' => 'Sample Customer 4',
                'email'     => '4sample@example.com',
                'phone'     => '09125242429',
            ],


            [
                'full_name' => 'Sample Customer 5',
                'email'     => '5sample@example.com',
                'phone'     => '09142424221',
            ],

        ];
        return view('customers/index', ['customers' => $customers]);
    }
}
