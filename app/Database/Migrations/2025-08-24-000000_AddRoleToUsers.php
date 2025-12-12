<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRoleToUsers extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        if (! $db->fieldExists('role', 'users')) {
            $this->forge->addColumn('users', [
                'role' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 20,
                    'default'    => 'student',
                    'after'      => 'password',
                ],
            ]);
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();
        if ($db->fieldExists('role', 'users')) {
            $this->forge->dropColumn('users', 'role');
        }
    }
}
