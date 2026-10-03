<?php

namespace App\Commands;

use App\Models\UserModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class SetUserPassword extends BaseCommand
{
    protected $group = 'Users';
    protected $name = 'users:set-password';
    protected $description = 'Set the password for a user or all users from standard input.';
    protected $usage = 'users:set-password <username|all>';

    public function run(array $params)
    {
        $target = $params[0] ?? null;
        if (! is_string($target) || $target === '') {
            CLI::error('Usage: php spark users:set-password <username|all>');
            return;
        }

        $model = new UserModel();
        $users = $target === 'all' ? $model->findAll() : [$model->where('username', $target)->first()];
        if ($users === [] || $users[0] === null) {
            CLI::error('No matching users found.');
            return;
        }

        CLI::write('Enter password, then press Enter:');
        $password = rtrim((string) fgets(STDIN), "\r\n");
        if (strlen($password) < 8 || strlen($password) > 72) {
            CLI::error('Password must be between 8 and 72 characters.');
            return;
        }

        foreach ($users as $user) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            if (! $model->update($user['id'], ['password' => $hash])) {
                CLI::error('Could not update ' . $user['username'] . '.');
                return;
            }

            $saved = $model->find($user['id']);
            if (! password_verify($password, $saved['password'] ?? '')) {
                CLI::error('Password verification failed for ' . $user['username'] . '.');
                return;
            }
        }

        CLI::write('Updated password for ' . count($users) . ' user(s).');
    }
}
