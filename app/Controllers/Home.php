<?php

namespace App\Controllers;

use App\Models\CourseModel;
use App\Models\EnrollmentModel;

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

        $data = [];

        // Load course data for students
        if ($role === 'student') {
            $data['enrolledCourses'] = $this->enrollmentModel->getUserEnrollments($user_id);
            $allCourses = $this->courseModel->getAllCourses();
            
            // Get enrolled course IDs
            $enrolledCourseIds = array_column($data['enrolledCourses'], 'course_id');
            
            // Filter out already enrolled courses
            $data['availableCourses'] = array_filter($allCourses, function($course) use ($enrolledCourseIds) {
                return !in_array($course['id'], $enrolledCourseIds);
            });
        } elseif ($role === 'admin' || $role === 'teacher') {
            // Provide course list for upload management
            $data['allCourses'] = $this->courseModel->getAllCourses();
        }

        return view('dashboard', $data);
    }
}