<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FixMissingRoles extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        
        // Fix teacher role
        $db->table('users')->where('email', 'teacher@example.com')->update(['role' => 'teacher']);
        
        // Set default role for any users with empty roles
        $db->table('users')->where('role', '')->orWhere('role IS NULL')->update(['role' => 'student']);
    }

    public function down()
    {
        // No need to reverse this
    }
}
