<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPasswordsToUsers extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('password', 'users')) {
            $this->forge->addColumn('users', [
                'password' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            ]);
        }

        foreach ($this->db->table('users')->select('id, password')->get()->getResultArray() as $user) {
            if (! is_string($user['password']) || $user['password'] === '') {
                $this->db->table('users')->where('id', $user['id'])->update([
                    'password' => password_hash(bin2hex(random_bytes(32)), PASSWORD_DEFAULT),
                ]);
            }
        }
    }

    public function down()
    {
    }
}
