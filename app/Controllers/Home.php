<?php

namespace App\Controllers;

use App\Models\CourseModel;
use App\Models\EnrollmentModel;
use App\Models\MaterialModel;

class Home extends BaseController
{
    protected $courseModel;
    protected $enrollmentModel;

    public function __construct()
    {
        $this->courseModel = new CourseModel();
        $this->enrollmentModel = new EnrollmentModel();
    }

    public function index()
    {
        return view('index'); // Homepage
    }

    public function about()
    {
        return view('about'); // About page
    }

    public function contact() // Contact page
    {
        return view('contact');
    }

    public function dashboard()
    {
        $session = session();
        if (! $session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $user_id = $session->get('user_id');
        $role = $session->get('role');
        
        // Check if user account is still active
        if ($user_id) {
            $userModel = new \App\Models\UserModel();
            $user = $userModel->find($user_id);
            
            if ($user && isset($user['status']) && $user['status'] === 'inactive') {
                // Destroy session and redirect to login
                $session->destroy();
                return redirect()->to(base_url('login'))->with('error', 'Your account has been deactivated. Please contact an administrator.');
            }
        }

        $data = [];

        // Load course data for students
        if ($role === 'student') {
            $data['enrolledCourses'] = $this->enrollmentModel->getUserEnrollments($user_id);
            $data['approvedCourses'] = array_values(array_filter($data['enrolledCourses'], static function ($enrollment) {
                return ($enrollment['status'] ?? 'approved') === 'approved';
            }));
            $data['pendingCourses'] = array_values(array_filter($data['enrolledCourses'], static function ($enrollment) {
                return ($enrollment['status'] ?? '') === 'pending';
            }));
            $allCourses = $this->courseModel->getAllCourses();
            
            // Get enrolled course IDs
            $enrolledCourseIds = array_column($data['enrolledCourses'], 'course_id');
            
            // Filter out already enrolled courses
            $data['availableCourses'] = array_filter($allCourses, function($course) use ($enrolledCourseIds) {
                return !in_array($course['id'], $enrolledCourseIds);
            });
            
            // Fetch materials for each enrolled course
            $materialModel = new MaterialModel();
            foreach ($data['approvedCourses'] as &$enrollment) {
                try {
                    $enrollment['materials'] = $materialModel->getMaterialsByCourse($enrollment['course_id']);
                } catch (\Exception $e) {
                    // If materials table doesn't exist yet, set empty array
                    $enrollment['materials'] = [];
                }
            }
            unset($enrollment); // Unset reference
        } elseif ($role === 'admin' || $role === 'teacher') {
            // Provide course list for upload management
            $data['allCourses'] = $this->courseModel->getAllCourses();
        }

        return view('dashboard', $data);
    }
}