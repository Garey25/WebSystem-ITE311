<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStatusAndProtectedToUsers extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        
        // Add status field
        if (!$db->fieldExists('status', 'users')) {
            $this->forge->addColumn('users', [
                'status' => [
                    'type' => 'ENUM',
                    'constraint' => ['active', 'inactive'],
                    'default' => 'active',
                    'after' => 'role',
                ],
            ]);
        }
        
        // Add is_protected field
        if (!$db->fieldExists('is_protected', 'users')) {
            $this->forge->addColumn('users', [
                'is_protected' => [
                    'type' => 'TINYINT',
                    'constraint' => 1,
                    'default' => 0,
                    'after' => 'status',
                ],
            ]);
        }
        
        // Mark the first admin user as protected (if exists)
        $firstAdmin = $db->table('users')
            ->where('role', 'admin')
            ->orderBy('id', 'ASC')
            ->limit(1)
            ->get()
            ->getRow();
            
        if ($firstAdmin) {
            $db->table('users')
                ->where('id', $firstAdmin->id)
                ->update(['is_protected' => 1]);
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();
        
        if ($db->fieldExists('is_protected', 'users')) {
            $this->forge->dropColumn('users', 'is_protected');
        }
        
        if ($db->fieldExists('status', 'users')) {
            $this->forge->dropColumn('users', 'status');
        }
    }
}

