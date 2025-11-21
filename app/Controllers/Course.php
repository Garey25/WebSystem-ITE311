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

        // Check if user is already enrolled
        if ($this->enrollmentModel->isAlreadyEnrolled($user_id, $course_id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You are already enrolled in this course.'
            ]);
        }

        // Enroll the user
        $enrollmentData = [
            'user_id' => $user_id,
            'course_id' => $course_id
        ];

        if ($this->enrollmentModel->enrollUser($enrollmentData)) {
            log_message('info', 'Enrollment successful for user ' . $user_id . ' in course ' . $course_id);
            
            // Create notification
            $notificationModel = new NotificationModel();
            $notificationModel->insert([
                'user_id' => $user_id,
                'message' => 'You have been enrolled in ' . $course['title'],
                'is_read' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Successfully enrolled in ' . $course['title'] . '!'
            ]);
        } else {
            log_message('error', 'Enrollment failed for user ' . $user_id . ' in course ' . $course_id);
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to enroll in course. Please try again.'
            ]);
        }
    }
}
