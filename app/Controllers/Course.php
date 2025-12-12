<?php

namespace App\Controllers;

use App\Models\CourseModel;
use App\Models\EnrollmentModel;
use App\Models\NotificationModel;

class Course extends BaseController
{
    protected $courseModel;
    protected $enrollmentModel;

    public function __construct()
    {
        $this->courseModel = new CourseModel();
        $this->enrollmentModel = new EnrollmentModel();
    }

    /**
     * Display all courses listing page
     *
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function index()
    {
        $courses = $this->courseModel->getAllCourses();
        return view('courses/index', ['courses' => $courses]);
    }

    /**
     * Handle course enrollment via AJAX
     *
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function enroll()
    {
        // Debug logging
        log_message('info', 'Course enrollment attempt - POST data: ' . json_encode($this->request->getPost()));
        
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            log_message('info', 'Enrollment failed - user not logged in');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You must be logged in to enroll in courses.'
            ]);
        }

        // Get the course_id from POST request
        $course_id = $this->request->getPost('course_id');
        
        if (!$course_id) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Course ID is required.'
            ]);
        }

        // Validate course_id is numeric
        if (!is_numeric($course_id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid course ID.'
            ]);
        }

        $user_id = session()->get('user_id');

        // Check if course exists
        $course = $this->courseModel->getCourseById($course_id);
        if (!$course) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Course not found.'
            ]);
        }

        // Ensure course has an assigned teacher
        if (empty($course['teacher_id'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'This course has no assigned teacher yet. Please contact the administrator.'
            ]);
        }

        // Check if user is already enrolled
        if ($this->enrollmentModel->isAlreadyEnrolled($user_id, $course_id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You are already enrolled in this course.'
            ]);
        }
        
        // Check if user already has a pending enrollment request
        if ($this->enrollmentModel->hasPendingEnrollment($user_id, $course_id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You already have a pending enrollment request for this course. Please wait for the teacher to review it.'
            ]);
        }

        // Enroll the user with pending status
        $enrollmentData = [
            'user_id' => $user_id,
            'course_id' => $course_id,
            'status' => 'pending'
        ];

        if ($enrollmentId = $this->enrollmentModel->enrollUser($enrollmentData)) {
            log_message('info', 'Enrollment request created for user ' . $user_id . ' in course ' . $course_id);
            
            // Create notification for student
            $notificationModel = new NotificationModel();
            $notificationModel->insert([
                'user_id' => $user_id,
                'message' => 'Your enrollment request for ' . $course['title'] . ' has been sent to the teacher.',
                'is_read' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            
            // Create notification for teacher
            $notificationModel->insert([
                'user_id' => $course['teacher_id'],
                'message' => 'New enrollment request from ' . session()->get('name') . ' for ' . $course['title'],
                'is_read' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'link' => site_url('teacher/enrollments')
            ]);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Enrollment request sent! The teacher will review your request shortly.'
            ]);
        } else {
            log_message('error', 'Enrollment failed for user ' . $user_id . ' in course ' . $course_id);
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to enroll in course. Please try again.'
            ]);
        }
    }

    /**
     * Search courses
     * Handles both AJAX and regular requests
     *
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    /**
     * Handle enrollment status update (approve/reject)
     *
     * @param int $enrollmentId
     * @param string $status
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function updateEnrollmentStatus($enrollmentId, $status)
    {
        // Only allow teachers to update enrollment status
        if (session()->get('role') !== 'teacher') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized access.'
            ])->setStatusCode(403);
        }

        $enrollment = $this->enrollmentModel->find($enrollmentId);
        if (!$enrollment) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Enrollment not found.'
            ])->setStatusCode(404);
        }

        // Verify the teacher owns the course
        $course = $this->courseModel->getCourseById($enrollment['course_id']);
        if ($course['teacher_id'] != session()->get('user_id')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You are not authorized to update this enrollment.'
            ])->setStatusCode(403);
        }

        // Update the enrollment status
        if ($this->enrollmentModel->updateStatus($enrollmentId, $status)) {
            $statusText = $status === 'approved' ? 'approved' : 'rejected';
            
            // Notify the student
            $notificationModel = new NotificationModel();
            $notificationModel->insert([
                'user_id' => $enrollment['user_id'],
                'message' => 'Your enrollment in ' . $course['title'] . ' has been ' . $statusText . '.',
                'is_read' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Enrollment ' . $statusText . ' successfully.'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to update enrollment status.'
        ]);
    }

    public function search()
    {
        $searchTerm = $this->request->getGet('search') ?? $this->request->getPost('search_term') ?? '';
        
        if (empty($searchTerm)) {
            $courses = $this->courseModel->getAllCourses();
        } else {
            $courses = $this->courseModel->searchCourses($searchTerm);
        }

        // Return JSON for AJAX requests
        if ($this->request->isAJAX()) {
            // Format courses for JSON response
            $formattedCourses = [];
            foreach ($courses as $course) {
                $formattedCourses[] = [
                    'id' => $course['id'],
                    'name' => $course['title'],
                    'description' => $course['description'] ?? ''
                ];
            }
            return $this->response->setJSON($formattedCourses);
        }

        // Return view for regular requests
        return view('courses/index', [
            'courses' => $courses,
            'searchTerm' => $searchTerm
        ]);
    }
}
