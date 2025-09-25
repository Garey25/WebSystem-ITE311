<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RbacUserSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');
        $rows = [
            ['name'=>'Admin One','email'=>'admin@example.com','password'=>password_hash('secret1234', PASSWORD_DEFAULT),'role'=>'admin','created_at'=>$now,'updated_at'=>$now],
            ['name'=>'Teacher One','email'=>'teacher@example.com','password'=>password_hash('secret1234', PASSWORD_DEFAULT),'role'=>'teacher','created_at'=>$now,'updated_at'=>$now],
            ['name'=>'Student One','email'=>'student@example.com','password'=>password_hash('secret1234', PASSWORD_DEFAULT),'role'=>'student','created_at'=>$now,'updated_at'=>$now],
        ];
        $this->db->table('users')->insertBatch($rows);
    }
}
