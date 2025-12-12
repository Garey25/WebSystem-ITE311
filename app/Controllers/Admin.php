<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\EnrollmentModel;
use App\Models\MaterialModel;
use App\Models\UserModel;
use App\Models\CourseModel;
use CodeIgniter\HTTP\ResponseInterface;

class Admin extends BaseController
{
    protected $userModel;
    protected $courseModel;
    protected $enrollmentModel;
    protected $materialModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->courseModel = new CourseModel();
        $this->enrollmentModel = new EnrollmentModel();
        $this->materialModel = new MaterialModel();
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

    public function search()
    {
        $this->checkAdmin();

        $q = trim((string) $this->request->getGet('q'));

        $limit = 25;

        $users = [];
        $courses = [];
        $enrollments = [];
        $materials = [];

        if ($q !== '') {
            $users = $this->userModel
                ->groupStart()
                    ->like('name', $q)
                    ->orLike('email', $q)
                    ->orLike('role', $q)
                ->groupEnd()
                ->orderBy('created_at', 'DESC')
                ->findAll($limit);

            $db = \Config\Database::connect();

            $courses = $db->table('courses c')
                ->select('c.*, u.name as teacher_name')
                ->join('users u', 'u.id = c.teacher_id', 'left')
                ->groupStart()
                    ->like('c.code', $q)
                    ->orLike('c.title', $q)
                    ->orLike('u.name', $q)
                ->groupEnd()
                ->orderBy('c.created_at', 'DESC')
                ->limit($limit)
                ->get()
                ->getResultArray();

            $enrollments = $db->table('enrollments e')
                ->select('e.*, u.name as student_name, u.email as student_email, c.title as course_title, c.code as course_code')
                ->join('users u', 'u.id = e.user_id')
                ->join('courses c', 'c.id = e.course_id')
                ->groupStart()
                    ->like('u.name', $q)
                    ->orLike('u.email', $q)
                    ->orLike('c.title', $q)
                    ->orLike('c.code', $q)
                    ->orLike('e.status', $q)
                ->groupEnd()
                ->orderBy('e.enrolled_at', 'DESC')
                ->limit($limit)
                ->get()
                ->getResultArray();

            $materials = $db->table('materials m')
                ->select('m.*, c.title as course_title, c.code as course_code')
                ->join('courses c', 'c.id = m.course_id')
                ->groupStart()
                    ->like('m.file_name', $q)
                    ->orLike('c.title', $q)
                    ->orLike('c.code', $q)
                ->groupEnd()
                ->orderBy('m.created_at', 'DESC')
                ->limit($limit)
                ->get()
                ->getResultArray();
        }

        return view('admin/search', [
            'title' => 'Admin Search',
            'q' => $q,
            'users' => $users,
            'courses' => $courses,
            'enrollments' => $enrollments,
            'materials' => $materials,
        ]);
    }

    public function courses()
    {
        $this->checkAdmin();

        $search = trim((string) $this->request->getGet('search'));

        $perPage = 10;

        if ($search !== '') {
            // When searching, return all matching results without pagination
            $courses = $this->courseModel
                ->groupStart()
                    ->like('code', $search)
                    ->orLike('title', $search)
                ->groupEnd()
                ->orderBy('created_at', 'DESC')
                ->findAll();
            $pager = null;
        } else {
            // Default listing with pagination
            $courses = $this->courseModel
                ->orderBy('created_at', 'DESC')
                ->paginate($perPage);
            $pager = $this->courseModel->pager;
        }

        foreach ($courses as &$course) {
            if (!isset($course['status']) || $course['status'] === '') {
                $course['status'] = 'inactive';
            }
        }
        unset($course);

        $teachers = $this->userModel
            ->where('role', 'teacher')
            ->where('status', 'active')
            ->orderBy('name', 'ASC')
            ->findAll();

        // Compute overall course statistics (not just current page)
        $allCourses = $this->courseModel->findAll();
        $totalCourses = count($allCourses);
        $activeCourses = 0;
        foreach ($allCourses as $c) {
            if (($c['status'] ?? '') === 'active') {
                $activeCourses++;
            }
        }

        $data = [
            'title' => 'Manage Courses',
            'courses' => $courses,
            'teachers' => $teachers,
            'totalCourses' => $totalCourses,
            'activeCourses' => $activeCourses,
            'search' => $search,
            'pager' => $pager ?? null,
        ];

        return view('admin/courses', $data);
    }

    public function updateCourse()
    {
        $this->checkAdmin();

        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        $id = (int) $this->request->getPost('id');
        $course = $this->courseModel->find($id);
        if (!$course) {
            return $this->response->setJSON(['success' => false, 'message' => 'Course not found']);
        }

        $startDate = $this->request->getPost('start_date');
        $endDate = $this->request->getPost('end_date');

        if ($startDate && $endDate) {
            try {
                $start = new \DateTime($startDate);
                $end = new \DateTime($endDate);
                if ($end < $start) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'End date cannot be earlier than start date.',
                    ]);
                }
            } catch (\Throwable $e) {
            }
        }

        $data = [
            'code' => $this->request->getPost('code'),
            'school_year' => $this->request->getPost('school_year'),
            'semester' => $this->request->getPost('semester'),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'teacher_id' => $this->request->getPost('teacher_id'),
            'schedule' => $this->request->getPost('schedule'),
            'status' => $this->request->getPost('status') === 'active' ? 'active' : 'inactive',
        ];

        if ($this->courseModel->update($id, $data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Course updated successfully',
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to update course',
            'errors' => $this->courseModel->errors(),
        ]);
    }

    public function updateCourseStatus()
    {
        $this->checkAdmin();

        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        $id = (int) $this->request->getPost('id');
        $status = $this->request->getPost('status') === 'active' ? 'active' : 'inactive';

        $course = $this->courseModel->find($id);
        if (!$course) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Course not found',
            ]);
        }

        if ($this->courseModel->update($id, ['status' => $status])) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Course status updated successfully',
                'new_status' => $status,
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to update course status',
            'errors' => $this->courseModel->errors(),
        ]);
    }

    public function addCourse()
    {
        $this->checkAdmin();

        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        $title = trim((string) $this->request->getPost('title'));
        if ($title === '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Course title is required.',
            ]);
        }

        $data = [
            'code' => $this->request->getPost('code') ?: null,
            'title' => $title,
            'description' => $this->request->getPost('description') ?: null,
            'school_year' => $this->request->getPost('school_year') ?: null,
            'semester' => $this->request->getPost('semester') ?: null,
            'start_date' => $this->request->getPost('start_date') ?: null,
            'end_date' => $this->request->getPost('end_date') ?: null,
            'schedule' => $this->request->getPost('schedule') ?: null,
            'teacher_id' => $this->request->getPost('teacher_id') ?: null,
            'status' => $this->request->getPost('status') === 'active' ? 'active' : 'inactive',
        ];

        // Basic date validation similar to updateCourse
        if (!empty($data['start_date']) && !empty($data['end_date'])) {
            try {
                $start = new \DateTime($data['start_date']);
                $end = new \DateTime($data['end_date']);
                if ($end < $start) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'End date cannot be earlier than start date.',
                    ]);
                }
            } catch (\Throwable $e) {
            }
        }

        try {
            if ($this->courseModel->insert($data)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Course added successfully.',
                ]);
            }

            $errors = $this->courseModel->errors();

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to add course.',
                'errors' => $errors,
            ]);
        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to add course: ' . $e->getMessage(),
            ]);
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
            'name' => 'required|min_length[2]|max_length[100]|regex_match[/^[\p{L} ]+$/u]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[8]|regex_match[/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/]',
            'role' => 'required|in_list[student,teacher,admin]',
        ];

        $messages = [
            'name' => [
                'regex_match' => 'Name may only contain letters and spaces.',
            ],
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

        $role = strtolower(trim((string) $this->request->getPost('role')));
        $data = [
            'name' => esc($this->request->getPost('name')),
            'email' => esc($this->request->getPost('email')),
            'password' => $this->request->getPost('password'),
            'role' => $role,
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
        $newRole = strtolower(trim((string) $this->request->getPost('role')));

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
        if (!in_array($newRole, ['student', 'teacher', 'admin'])) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Invalid role',
                ]);
            }
            return redirect()->back()->with('error', 'Invalid role');
        }

        try {
            $data = ['role' => $newRole];
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

            // Prevent deactivation or activation toggling for admin accounts
            if (($user['role'] ?? '') === 'admin') {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Admin account is protected and cannot be activated or deactivated.',
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
