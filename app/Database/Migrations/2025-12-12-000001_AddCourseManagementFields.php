<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCourseManagementFields extends Migration
{
    public function up()
    {
        $fields = [
            'code' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'school_year' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'semester' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'start_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'end_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'schedule' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'teacher_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
                'default' => 'inactive',
            ],
        ];

        $this->forge->addColumn('courses', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('courses', [
            'code',
            'school_year',
            'semester',
            'start_date',
            'end_date',
            'schedule',
            'teacher_id',
            'status',
        ]);
    }
}
