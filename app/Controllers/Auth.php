<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    public function login()
    {
        if (session()->get('user_id') !== null) {
            return redirect()->to('/customers');
        }

        return view('auth/login');
    }

    public function attempt()
    {
        $username = trim((string) $this->request->getPost('username'));
        $password = (string) $this->request->getPost('password');
        $user = (new UserModel())->where('username', $username)->first();

        if ($user === null || ! is_string($user['password'] ?? null) || ! password_verify($password, $user['password'])) {
            return redirect()->to('/login')->with('username', $username)->with('error', 'Invalid username or password.');
        }

        $session = session();
        $session->regenerate(true);
        $session->set([
            'user_id' => (int) $user['id'],
            'username' => $user['username'],
        ]);

        return redirect()->to('/customers');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}
