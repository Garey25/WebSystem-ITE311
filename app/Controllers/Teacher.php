<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CourseModel;
use App\Models\EnrollmentModel;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;

class Teacher extends BaseController
{
    protected $courseModel;
    protected $enrollmentModel;
    protected $userModel;

    public function __construct()
    {
        $this->courseModel = new CourseModel();
        $this->enrollmentModel = new EnrollmentModel();
        $this->userModel = new UserModel();
    }

    public function index()
    {
        return redirect()->to(site_url('teacher/students'));
    }

    public function students()
    {
        // Available courses (can be filtered by teacher later)
        $courses = $this->courseModel
            ->orderBy('title', 'ASC')
            ->findAll();

        $selectedCourseId = (int) ($this->request->getGet('course_id') ?? 0);
        $selectedCourse = null;

        if (!empty($courses)) {
            // If no course selected or invalid, default to first
            if ($selectedCourseId === 0) {
                $selectedCourse = $courses[0];
                $selectedCourseId = (int) $selectedCourse['id'];
            } else {
                foreach ($courses as $c) {
                    if ((int) $c['id'] === $selectedCourseId) {
                        $selectedCourse = $c;
                        break;
                    }
                }
                if ($selectedCourse === null) {
                    $selectedCourse = $courses[0];
                    $selectedCourseId = (int) $selectedCourse['id'];
                }
            }
        }

        // Load enrolled students for selected course (basic info)
        $students = [];
        if ($selectedCourseId > 0) {
            $enrollments = $this->enrollmentModel->getCourseEnrollments($selectedCourseId);
            foreach ($enrollments as $enrollment) {
                $students[] = [
                    'student_id' => $enrollment['student_id'],
                    'name' => $enrollment['student_name'] ?? '',
                    'email' => $enrollment['student_email'] ?? '',
                    'program' => '-',
                    'year_level' => '-',
                    'section' => '',
                    'enrolled_at' => $enrollment['enrolled_at'] ?? '',
                    'status' => 'Active',
                ];
            }
        }

        $courseTitle = $selectedCourse
            ? trim(($selectedCourse['code'] ?? '') . ' - ' . ($selectedCourse['title'] ?? ''))
            : 'No courses available';

        // All active students (for enrollment dropdown)
        $allStudents = $this->userModel
            ->where('role', 'student')
            ->where('status', 'active')
            ->orderBy('name', 'ASC')
            ->findAll();

        $data = [
            'title' => 'Manage Students',
            'courseTitle' => $courseTitle,
            'students' => $students,
            'courses' => $courses,
            'selectedCourseId' => $selectedCourseId,
            'allStudents' => $allStudents,
        ];

        return view('teacher/students', $data);
    }

    public function enrollStudent()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        if (!session()->get('isLoggedIn') || session('role') !== 'teacher') {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied.']);
        }

        $studentId = (int) $this->request->getPost('student_id');
        $courseId = (int) $this->request->getPost('course_id');

        if (!$studentId || !$courseId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Student and course are required.',
            ]);
        }

        $student = $this->userModel->find($studentId);
        if (!$student || ($student['role'] ?? '') !== 'student') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Selected user is not a valid student.',
            ]);
        }

        $course = $this->courseModel->getCourseById($courseId);
        if (!$course) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Course not found.',
            ]);
        }

        if ($this->enrollmentModel->isAlreadyEnrolled($studentId, $courseId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Student is already enrolled in this course.',
            ]);
        }

        $enrollmentData = [
            'user_id' => $studentId,
            'course_id' => $courseId,
        ];

        if ($this->enrollmentModel->enrollUser($enrollmentData)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Student enrolled successfully.',
                'student' => [
                    'student_id' => $studentId,
                    'name' => $student['name'],
                    'email' => $student['email'],
                ],
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to enroll student. Please try again.',
        ]);
    }
}
