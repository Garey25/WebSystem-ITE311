<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{

    public function login()
    {
        if ($this->request->getMethod() === 'post') {
            $rules = ['email'=>'required|valid_email', 'password'=>'required'];
            if (! $this->validate($rules)) {
                return view('auth/login', [
                    'title'       => 'Login',
                    'validation'  => $this->validator,
                ]);
            }

            $email = $this->request->getPost('email');
            $pass  = $this->request->getPost('password');

            $user = (new UserModel())->where('email', $email)->first();
            if (! $user || ! password_verify($pass, $user['password'])) {
                return redirect()->back()->withInput()->with('login_error','Invalid email or password.');
            }

            // Successful login
            session()->regenerate();
            session()->set([
                'user_id'    => $user['id'],
                'name'       => $user['name'],
                'email'      => $user['email'],
                'role'       => $user['role'] ?? 'student',
                'isLoggedIn' => true,
            ]);

            // Debug: Log the session data
            log_message('info', 'Login successful for user: ' . $user['email'] . ' with role: ' . ($user['role'] ?? 'student'));
            log_message('info', 'Session data: ' . json_encode(session()->get()));

            return redirect()->to(site_url('dashboard'))->with('success','Welcome, '.$user['name'].'!');
        }

        return view('auth/login', ['title' => 'Login']);
    }

    private function requireLogin()
    {
        if (! session('isLoggedIn')) {
            session()->set('redirect_url', current_url());
            return false;
        }
        return true;
    }

    public function dashboard()
    {
        // Debug: Log session status
        log_message('info', 'Dashboard accessed. Session isLoggedIn: ' . (session('isLoggedIn') ? 'true' : 'false'));
        log_message('info', 'Session role: ' . (session('role') ?? 'null'));
        
        if (! $this->requireLogin()) {
            log_message('info', 'Login required, redirecting to login page');
            return redirect()->to(site_url('login'))->with('error','Please log in first.');
        }

        $role = session('role') ?? 'student';
        log_message('info', 'Dashboard loading for role: ' . $role);

        // Role-specific sample data (safe checks so it won't error if tables are missing)
        $data = ['title' => 'Dashboard', 'role' => $role];
        
        // Initialize stats with default values
        $data['stats'] = [
            'users' => 0,
            'courses' => 0,
            'lessons' => 0,
            'my_courses' => 0,
            'quizzes' => 0,
            'enrolled' => 0,
        ];

        try {
            $db = \Config\Database::connect();
            
            // Helpers to count rows safely
            $count = function(string $table) use ($db): int {
                try {
                    if (method_exists($db, 'tableExists') && ! $db->tableExists($table)) return 0;
                    return (int) $db->table($table)->countAllResults();
                } catch (\Throwable $e) {
                    log_message('error', 'Database error counting ' . $table . ': ' . $e->getMessage());
                    return 0;
                }
            };

            if ($role === 'admin') {
                $data['stats'] = [
                    'users'    => $count('users'),
                    'courses'  => $count('courses'),
                    'lessons'  => $count('lessons'),
                ];
            } elseif ($role === 'teacher') {
                $data['stats'] = [
                    'my_courses' => $count('courses'), // replace with real teacher filter later
                    'quizzes'    => $count('quizzes'),
                ];
            } else { // student
                $data['stats'] = [
                    'enrolled' => $count('enrollments'),
                    'quizzes'  => $count('quizzes'),
                ];
            }
        } catch (\Throwable $e) {
            log_message('error', 'Dashboard database error: ' . $e->getMessage());
            // Keep default stats if database fails
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
        $name = trim((string) $this->request->getPost('name'));
        $email = trim((string) $this->request->getPost('email'));
        $password = (string) $this->request->getPost('password');
        $passwordConfirm = (string) $this->request->getPost('password_confirm');

        if ($name === '' || $email === '' || $password === '' || $passwordConfirm === '') {
            return redirect()->back()->withInput()->with('register_error', 'All fields are required.');
        }

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()->withInput()->with('register_error', 'Invalid email address.');
        }

        if ($password !== $passwordConfirm) {
            return redirect()->back()->withInput()->with('register_error', 'Passwords do not match.');
        }

        $userModel = new \App\Models\UserModel();

        // Check for existing email
        if ($userModel->where('email', $email)->first()) {
            return redirect()->back()->withInput()->with('register_error', 'Email is already registered.');
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $userId = $userModel->insert([
            'name' => $name,
            'email' => $email,
            'role' => 'student',
            'password' => $passwordHash,
        ], true);

        if (! $userId) {
            return redirect()->back()->withInput()->with('register_error', 'Registration failed.');
        }

        // Redirect to login with success message
        return redirect()
            ->to(base_url('login'))
            ->with('register_success', 'Account created successfully. Please log in.');
    }
}