<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAvatarToUsers extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('avatar', 'users')) {
            $this->forge->addColumn('users', [
            'avatar' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'role',
            ],
            ]);
        }
    }

    public function down()
    {
        // The SQL import may have supplied this column before the migration ran.
        // There is no reliable way to tell who created it, so keep the data.
    }
}
