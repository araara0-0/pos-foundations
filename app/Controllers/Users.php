<?php

namespace App\Controllers;

use App\Models\UserModel;

class Users extends BaseController
{
    public function index()
    {
        $userModel = new UserModel();
        $users = $userModel->findAll();
        return view('users/index', ['users' => $users]);
    }
}
