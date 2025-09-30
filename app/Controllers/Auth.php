<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{

    public function login()
    {
        if ($this->request->getMethod() === 'post') {
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
            
            if (! $user) {
                return redirect()->back()->withInput()->with('login_error', 'User not found.');
            }
            
            if (! password_verify($password, $user['password'])) {
                return redirect()->back()->withInput()->with('login_error', 'Invalid password.');
            }

            // Successful login - set role and regenerate session
            session()->regenerate();
            
            // Set session data
            $sessionData = [
                'user_id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role'] ?? 'student',
                'isLoggedIn' => true,
            ];
            
            session()->set($sessionData);

            // Debug: Log session data
            log_message('info', 'Login successful for user: ' . $user['email'] . ' with role: ' . ($user['role'] ?? 'student'));
            log_message('info', 'Session data: ' . json_encode(session()->get()));

            // Use base_url instead of site_url for redirect
            return redirect()->to(base_url('dashboard'))->with('success', 'Welcome, ' . $user['name'] . '!');
        }

        return view('auth/login', ['title' => 'Login']);
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
        if (! $this->requireLogin()) {
            return redirect()->to(base_url('login'))->with('error', 'Please log in first.');
        }

        $role = session('role') ?? 'student';
        $data = ['title' => 'Dashboard', 'role' => $role];
        
        // Debug: Log session data
        log_message('info', 'Dashboard accessed. Role: ' . $role . ', User: ' . session('name'));
        
        // Role-specific sample data (safe checks so it won't error if tables are missing)
        $db = \Config\Database::connect();
        
        // Helper to count rows safely
        $count = function(string $table) use ($db): int {
            try {
                if (method_exists($db, 'tableExists') && ! $db->tableExists($table)) {
                    return 0;
                }
                return (int) $db->table($table)->countAllResults();
            } catch (\Throwable $e) {
                return 0;
            }
        };

        if ($role === 'admin') {
            $data['stats'] = [
                'users' => $count('users'),
                'courses' => $count('courses'),
                'lessons' => $count('lessons'),
            ];
        } elseif ($role === 'teacher') {
            $data['stats'] = [
                'my_courses' => $count('courses'), // replace with real teacher filter later
                'quizzes' => $count('quizzes'),
            ];
        } else { // student
            $data['stats'] = [
                'enrolled' => $count('enrollments'),
                'quizzes' => $count('quizzes'),
            ];
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