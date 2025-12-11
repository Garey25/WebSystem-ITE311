<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\MaterialModel;

class Auth extends BaseController
{

    public function login()
    {
        // If already logged in, redirect to dashboard
        if (session('isLoggedIn')) {
            return redirect()->to(site_url('dashboard'));
        }

        return view('auth/login', ['title' => 'Login']);
    }

    public function attempt()
    {
        // Debug: Log the POST data
        log_message('info', 'Login attempt - POST data: ' . json_encode($this->request->getPost()));
        
        $rules = [
            'email' => 'required|valid_email',
            'password' => 'required'
        ];
        
        if (! $this->validate($rules)) {
            return view('auth/login', [
                'title' => 'Login',
                'validation' => $this->validator,
            ]);
        }

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $userModel = new UserModel();
        $user = $userModel->where('email', $email)->first();
        
        // Debug: Log user lookup result
        log_message('info', 'User lookup for email: ' . $email . ' - Found: ' . ($user ? 'YES' : 'NO'));
        if ($user) {
            log_message('info', 'User data: ' . json_encode($user));
        }
        
        if (! $user) {
            log_message('info', 'User not found for email: ' . $email);
            return redirect()->back()->withInput()->with('login_error', 'User not found.');
        }
        
        // Check if user account is active
        if (isset($user['status']) && $user['status'] === 'inactive') {
            log_message('info', 'Login attempt for inactive user: ' . $email);
            return redirect()->back()->withInput()->with('login_error', 'Your account has been deactivated. Please contact an administrator.');
        }
        
        if (! password_verify($password, $user['password'])) {
            return redirect()->back()->withInput()->with('login_error', 'Invalid password.');
        }

        // Successful login - regenerate session for security
        session()->regenerate();
        
        // Set comprehensive session data
        $sessionData = [
            'user_id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'] ?? 'student',
            'isLoggedIn' => true,
            'login_time' => time(),
        ];
        
        session()->set($sessionData);

        // Log successful login
        log_message('info', 'Login successful for user: ' . $user['email'] . ' with role: ' . ($user['role'] ?? 'student'));

        // Redirect to unified dashboard for all users
        return redirect()->to(site_url('dashboard'))->with('success', 'Welcome back, ' . $user['name'] . '! You are logged in as ' . ucfirst($user['role'] ?? 'student'));
    }

    private function requireLogin()
    {
        $isLoggedIn = session('isLoggedIn');
        log_message('info', 'requireLogin check - isLoggedIn: ' . ($isLoggedIn ? 'true' : 'false'));
        
        if (! $isLoggedIn) {
            session()->set('redirect_url', current_url());
            log_message('info', 'Login required, redirecting to login page');
            return false;
        }
        return true;
    }

    public function dashboard()
    {
        // Simple check - if not logged in, redirect to login
        if (!session('isLoggedIn')) {
            return redirect()->to(site_url('login'))->with('error', 'Please log in first.');
        }

        $role = session('role') ?? 'student';
        $userId = session('user_id');
        $userName = session('name');
        
        // Check if user account is still active
        if ($userId) {
            $userModel = new UserModel();
            $user = $userModel->find($userId);
            
            if ($user && isset($user['status']) && $user['status'] === 'inactive') {
                // Destroy session and redirect to login
                session()->destroy();
                return redirect()->to(site_url('login'))->with('error', 'Your account has been deactivated. Please contact an administrator.');
            }
        }
        
        $data = [
            'title' => 'Dashboard - ' . ucfirst($role),
            'role' => $role,
            'user_name' => $userName,
            'user_id' => $userId
        ];
        
        // Log dashboard access
        log_message('info', 'Dashboard accessed by user: ' . $userName . ' (ID: ' . $userId . ') with role: ' . $role);
        
        // Fetch role-specific data from database
        $db = \Config\Database::connect();
        
        // Helper to count rows safely
        $count = function(string $table, array $where = []) use ($db): int {
            try {
                if (method_exists($db, 'tableExists') && ! $db->tableExists($table)) {
                    return 0;
                }
                $query = $db->table($table);
                if (!empty($where)) {
                    $query->where($where);
                }
                return (int) $query->countAllResults();
            } catch (\Throwable $e) {
                return 0;
            }
        };

        // Role-specific data fetching
        if ($role === 'admin') {
            $data['stats'] = [
                'total_users' => $count('users'),
                'total_courses' => $count('courses'),
                'total_lessons' => $count('lessons'),
                'total_quizzes' => $count('quizzes'),
                'total_enrollments' => $count('enrollments'),
            ];
            
            // Recent activity for admin
            $data['recent_users'] = $db->table('users')
                ->select('id, name, email, role, created_at')
                ->orderBy('created_at', 'DESC')
                ->limit(5)
                ->get()
                ->getResultArray();
                
        } elseif ($role === 'teacher') {
            $data['stats'] = [
                'my_courses' => $count('courses'), // Will be enhanced with teacher-specific filtering
                'total_quizzes' => $count('quizzes'),
                'total_lessons' => $count('lessons'),
                'total_students' => $count('enrollments'),
            ];
            
            // Teacher-specific data
            $data['my_courses_list'] = $db->table('courses')
                ->select('id, title, description, created_at')
                ->orderBy('created_at', 'DESC')
                ->limit(5)
                ->get()
                ->getResultArray();
                
        } else { // student
            $data['stats'] = [
                'enrolled_courses' => $count('enrollments', ['user_id' => $userId]),
                'total_quizzes' => $count('quizzes'),
                'completed_quizzes' => $count('submissions', ['user_id' => $userId]),
            ];
            
            // Student-specific data
            $data['my_enrollments'] = $db->table('enrollments')
                ->select('enrollments.*, courses.title as course_title, courses.description, courses.id as course_id')
                ->join('courses', 'courses.id = enrollments.course_id')
                ->where('enrollments.user_id', $userId)
                ->orderBy('enrollments.enrolled_at', 'DESC')
                ->limit(5)
                ->get()
                ->getResultArray();
            
            // Fetch materials for each enrolled course
            $materialModel = new MaterialModel();
            foreach ($data['my_enrollments'] as &$enrollment) {
                try {
                    $enrollment['materials'] = $materialModel->getMaterialsByCourse($enrollment['course_id']);
                } catch (\Exception $e) {
                    // If materials table doesn't exist yet, set empty array
                    $enrollment['materials'] = [];
                }
            }
            unset($enrollment); // Unset reference
        }

        return view('auth/dashboard', $data);
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(site_url('login'))->with('success','You have been logged out.');
    }

    public function register()
    {
        $session = session();
        if ($session->get('isLoggedIn')) {
            return redirect()->to(base_url('dashboard'));
        }

        return view('auth/register');
    }

    public function store()
    {
        $rules = [
            'name' => 'required|min_length[2]|max_length[100]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'password_confirm' => 'required|matches[password]',
        ];

        if (! $this->validate($rules)) {
            return view('auth/register', [
                'title' => 'Register',
                'validation' => $this->validator,
            ]);
        }

        $userModel = new \App\Models\UserModel();

        $data = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'),
            'role' => 'student',
        ];

        try {
            if (! $userModel->insert($data)) {
                $errors = $userModel->errors();
                return redirect()->back()->withInput()->with('register_error', 'Registration failed: ' . implode(', ', $errors));
            }
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('register_error', 'Registration failed: ' . $e->getMessage());
        }

        return redirect()
            ->to(base_url('login'))
            ->with('register_success', 'Account created successfully. Please log in.');
    }
}