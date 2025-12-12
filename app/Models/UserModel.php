<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'is_protected',
        'created_at',
        'updated_at',
    ];

    protected $validationRules = [
        'name' => 'required|min_length[2]|max_length[100]|regex_match[/^[\p{L} ]+$/u]',
        'email' => 'required|valid_email|is_unique[users.email,id,{id}]',
        'password' => 'permit_empty|min_length[6]',
        'role' => 'in_list[student,teacher,admin]',
        'status' => 'in_list[active,inactive]',
        'is_protected' => 'permit_empty|in_list[0,1]',
    ];

    protected $validationMessages = [
        'name' => [
            'required' => 'Name is required',
            'min_length' => 'Name must be at least 2 characters long',
            'max_length' => 'Name cannot exceed 100 characters',
            'regex_match' => 'Name may only contain letters and spaces.',
        ],
        'email' => [
            'required' => 'Email is required',
            'valid_email' => 'Please provide a valid email address',
            'is_unique' => 'This email is already registered',
        ],
        'password' => [
            'required' => 'Password is required',
            'min_length' => 'Password must be at least 6 characters long',
        ],
        'role' => [
            'in_list' => 'Role must be student, teacher, or admin',
        ],
    ];

    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password']) && !empty($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        } else {
            // Remove password from update if empty
            unset($data['data']['password']);
        }
        return $data;
    }
    
    /**
     * Check if user is protected admin
     */
    public function isProtectedAdmin($userId)
    {
        $user = $this->find($userId);
        return $user && isset($user['is_protected']) && $user['is_protected'] == 1;
    }
    
    /**
     * Get protected admin user
     */
    public function getProtectedAdmin()
    {
        return $this->where('is_protected', 1)->first();
    }
}


