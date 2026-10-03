<?php

namespace App\Commands;

use App\Models\UserModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class ResetUserPassword extends BaseCommand
{
    protected $group = 'Users';
    protected $name = 'users:reset-password';
    protected $description = 'Generate a new password for an existing user and show it once.';
    protected $usage = 'users:reset-password <username>';

    public function run(array $params)
    {
        $username = $params[0] ?? null;
        if (! is_string($username) || $username === '') {
            CLI::error('Usage: php spark users:reset-password <username>');
            return;
        }

        $model = new UserModel();
        $user = $model->where('username', $username)->first();
        if ($user === null) {
            CLI::error('User not found.');
            return;
        }

        $password = rtrim(strtr(base64_encode(random_bytes(18)), '+/', '-_'), '=');
        $model->update($user['id'], ['password' => password_hash($password, PASSWORD_DEFAULT)]);
        CLI::write('New password for ' . $username . ': ' . $password);
        CLI::write('Store it securely; it will not be shown again.');
    }
}
