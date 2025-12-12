<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStatusToEnrollments extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        $existing = array_map('strtolower', $db->getFieldNames('enrollments'));

        $fields = [];
        if (!in_array('status', $existing, true)) {
            $fields['status'] = [
                'type' => 'ENUM',
                'constraint' => ['pending', 'approved', 'rejected'],
                'default' => 'pending',
                'after' => 'enrolled_at'
            ];
        }
        if (!in_array('processed_at', $existing, true)) {
            $fields['processed_at'] = [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'status'
            ];
        }

        if (!empty($fields)) {
            $this->forge->addColumn('enrollments', $fields);
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();
        $existing = array_map('strtolower', $db->getFieldNames('enrollments'));

        if (in_array('status', $existing, true)) {
            $this->forge->dropColumn('enrollments', 'status');
        }
        if (in_array('processed_at', $existing, true)) {
            $this->forge->dropColumn('enrollments', 'processed_at');
        }
    }
}
