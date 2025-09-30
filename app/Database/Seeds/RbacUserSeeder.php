<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RbacUserSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');
        $users = [
            ['name'=>'Admin One','email'=>'admin@example.com','password'=>password_hash('secret1234', PASSWORD_DEFAULT),'role'=>'admin','created_at'=>$now,'updated_at'=>$now],
            ['name'=>'Teacher One','email'=>'teacher@example.com','password'=>password_hash('secret1234', PASSWORD_DEFAULT),'role'=>'teacher','created_at'=>$now,'updated_at'=>$now],
            ['name'=>'Student One','email'=>'student@example.com','password'=>password_hash('secret1234', PASSWORD_DEFAULT),'role'=>'student','created_at'=>$now,'updated_at'=>$now],
        ];

        foreach ($users as $user) {
            // Check if user already exists
            $existing = $this->db->table('users')->where('email', $user['email'])->get()->getRow();
            if (!$existing) {
                $this->db->table('users')->insert($user);
            } else {
                // Update existing user with role if missing
                if (empty($existing->role)) {
                    $this->db->table('users')->where('email', $user['email'])->update(['role' => $user['role']]);
                }
            }
        }
    }
}
