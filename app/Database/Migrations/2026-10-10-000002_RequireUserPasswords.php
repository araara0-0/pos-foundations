<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RequireUserPasswords extends Migration
{
    public function up()
    {
        foreach ($this->db->table('users')->select('id, password')->get()->getResultArray() as $user) {
            if ($user['password'] === null || $user['password'] === '') {
                $this->db->table('users')->where('id', $user['id'])->update([
                    'password' => password_hash(bin2hex(random_bytes(32)), PASSWORD_DEFAULT),
                ]);
            }
        }

        $this->forge->modifyColumn('users', [
            'password' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => false],
        ]);
    }

    public function down()
    {
    }
}
