<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;

class Admin extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * Check if user is admin
     */
    private function checkAdmin()
    {
        if (session('role') !== 'admin') {
            if ($this->request->isAJAX()) {
                $this->response->setJSON(['success' => false, 'message' => 'Access denied. Admin only.'])->send();
                exit;
            }
            redirect()->to(site_url('dashboard'))->with('error', 'Access denied. Admin only.')->send();
            exit;
        }
    }

    /**
     * Check if user is protected admin
     */
    private function isProtectedAdmin($userId)
    {
        return $this->userModel->isProtectedAdmin($userId);
    }

    /**
     * Manage Users - List all users
     */
    public function users()
    {
        $this->checkAdmin();

        $users = $this->userModel->orderBy('created_at', 'DESC')->findAll();
        
        // Ensure all users have status field (default to 'active' if missing)
        foreach ($users as &$user) {
            if (!isset($user['status'])) {
                $user['status'] = 'active';
            }
            if (!isset($user['is_protected'])) {
                $user['is_protected'] = 0;
            }
        }
        unset($user);
        
        $protectedAdmin = $this->userModel->getProtectedAdmin();
        $protectedAdminId = $protectedAdmin ? $protectedAdmin['id'] : null;

        $data = [
            'title' => 'Manage Users',
            'users' => $users,
            'protectedAdminId' => $protectedAdminId,
        ];

        return view('admin/users', $data);
    }

    /**
     * Add new user
     */
    public function addUser()
    {
        $this->checkAdmin();

        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        $rules = [
            'name' => 'required|min_length[2]|max_length[100]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[8]|regex_match[/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/]',
            'role' => 'required|in_list[student,librarian,admin]',
        ];

        $messages = [
            'password' => [
                'min_length' => 'Password must be at least 8 characters long',
                'regex_match' => 'Password must contain at least one uppercase letter, one lowercase letter, and one number',
            ],
        ];

        if (!$this->validate($rules, $messages)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors(),
            ]);
        }

        $data = [
            'name' => esc($this->request->getPost('name')),
            'email' => esc($this->request->getPost('email')),
            'password' => $this->request->getPost('password'),
            'role' => esc($this->request->getPost('role')),
            'status' => 'active',
            'is_protected' => 0,
        ];

        try {
            if ($this->userModel->insert($data)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'User added successfully',
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to add user',
                    'errors' => $this->userModel->errors(),
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Update user role
     */
    public function updateRole()
    {
        $this->checkAdmin();

        // Allow both AJAX and regular POST requests
        $userId = $this->request->getPost('user_id');
        $newRole = $this->request->getPost('role');

        if (!$userId || !$newRole) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'User ID and role are required',
                ]);
            }
            return redirect()->back()->with('error', 'User ID and role are required');
        }

        // Check if user is protected admin
        if ($this->isProtectedAdmin($userId)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Cannot change role of protected admin account',
                ]);
            }
            return redirect()->back()->with('error', 'Cannot change role of protected admin account');
        }

        // Validate role
        if (!in_array($newRole, ['student', 'teacher', 'admin', 'librarian'])) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Invalid role',
                ]);
            }
            return redirect()->back()->with('error', 'Invalid role');
        }

        try {
            $data = ['role' => esc($newRole)];
            if ($this->userModel->update($userId, $data)) {
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'success' => true,
                        'message' => 'Role updated successfully',
                    ]);
                }
                return redirect()->back()->with('success', 'Role updated successfully');
            } else {
                $errors = $this->userModel->errors();
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Failed to update role',
                        'errors' => $errors,
                    ]);
                }
                return redirect()->back()->with('error', 'Failed to update role: ' . implode(', ', $errors));
            }
        } catch (\Exception $e) {
            log_message('error', 'Role update error: ' . $e->getMessage());
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage(),
                ]);
            }
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Toggle user status (activate/deactivate)
     */
    public function toggleStatus()
    {
        $this->checkAdmin();

        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        $userId = $this->request->getPost('user_id');

        if (!$userId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'User ID is required',
            ]);
        }

        // Check if user is protected admin
        if ($this->isProtectedAdmin($userId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Cannot deactivate protected admin account',
            ]);
        }

        try {
            $user = $this->userModel->find($userId);
            if (!$user) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'User not found',
                ]);
            }

            $newStatus = $user['status'] === 'active' ? 'inactive' : 'active';
            $data = ['status' => $newStatus];

            if ($this->userModel->update($userId, $data)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'User status updated successfully',
                    'new_status' => $newStatus,
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to update status',
                    'errors' => $this->userModel->errors(),
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Change password for protected admin
     */
    public function changePassword()
    {
        $this->checkAdmin();

        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        $userId = $this->request->getPost('user_id');
        $newPassword = $this->request->getPost('password');

        if (!$userId || !$newPassword) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'User ID and password are required',
            ]);
        }

        // Only allow password change for protected admin
        if (!$this->isProtectedAdmin($userId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Password change is only allowed for protected admin account',
            ]);
        }

        // Validate password strength
        if (strlen($newPassword) < 8 || !preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/', $newPassword)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Password must be at least 8 characters and contain uppercase, lowercase, and number',
            ]);
        }

        try {
            $data = ['password' => $newPassword];
            if ($this->userModel->update($userId, $data)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Password changed successfully',
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to change password',
                    'errors' => $this->userModel->errors(),
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ]);
        }
    }
}
