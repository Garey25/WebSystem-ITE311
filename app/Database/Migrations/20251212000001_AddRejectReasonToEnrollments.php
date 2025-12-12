<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRejectReasonToEnrollments extends Migration
{
    public function up()
    {
        $fields = [
            'reject_reason' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'processed_at',
            ],
        ];

        $this->forge->addColumn('enrollments', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('enrollments', 'reject_reason');
    }
}
