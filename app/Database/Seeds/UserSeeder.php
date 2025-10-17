<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');
        $data = [
            [
                'name'     => 'Admin One',
                'email'    => 'admin@example.com',
                'password' => password_hash('secret1234', PASSWORD_DEFAULT),
                'role'     => 'admin',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name'     => 'Teacher One',
                'email'    => 'teacher@example.com',
                'password' => password_hash('secret1234', PASSWORD_DEFAULT),
                'role'     => 'teacher',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name'     => 'Student One',
                'email'    => 'student@example.com',
                'password' => password_hash('secret1234', PASSWORD_DEFAULT),
                'role'     => 'student',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        foreach ($data as $user) {
            // Check if user already exists
            $existing = $this->db->table('users')->where('email', $user['email'])->get()->getRow();
            if (!$existing) {
                $this->db->table('users')->insert($user);
            } else {
                // Update existing user with correct role and password
                $this->db->table('users')->where('email', $user['email'])->update([
                    'name' => $user['name'],
                    'password' => $user['password'],
                    'role' => $user['role'],
                    'updated_at' => $now,
                ]);
            }
        }
    }
}
