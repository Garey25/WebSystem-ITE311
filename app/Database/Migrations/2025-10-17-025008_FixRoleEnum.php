<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FixRoleEnum extends Migration
{
    public function up()
    {
        // Fix the role column to include 'teacher' in the ENUM
        $this->forge->modifyColumn('users', [
            'role' => [
                'type' => 'ENUM',
                'constraint' => ['student', 'admin', 'teacher'],
                'default' => 'student',
            ],
        ]);
    }

    public function down()
    {
        //
    }
}
