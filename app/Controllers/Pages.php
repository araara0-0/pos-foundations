<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Pages extends BaseController
{
    public function index()
    {
        return view('pages/home');
    }


    public function about()
    {
        return view('pages/about');
    }

}
