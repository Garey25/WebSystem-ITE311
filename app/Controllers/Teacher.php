<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CourseModel;
use App\Models\EnrollmentModel;
use App\Models\MaterialModel;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;

class Teacher extends BaseController
{
    protected $courseModel;
    protected $enrollmentModel;
    protected $materialModel;
    protected $userModel;

    public function __construct()
    {
        $this->courseModel = new CourseModel();
        $this->enrollmentModel = new EnrollmentModel();
        $this->materialModel = new MaterialModel();
        $this->userModel = new UserModel();
        
        // Ensure user is logged in and is a teacher
        if (session()->get('role') !== 'teacher') {
            throw new \CodeIgniter\Exceptions\PageNotFoundException();
        }
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

    /**
     * Display pending enrollment requests for teacher's courses
     *
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function enrollments()
    {
        $teacherId = session()->get('user_id');
        log_message('info', 'Teacher::enrollments accessed. teacherId=' . (string) $teacherId . ', role=' . (string) session()->get('role'));
        $pendingEnrollments = $this->enrollmentModel->getPendingEnrollments($teacherId);
        log_message('info', 'Teacher::enrollments pendingEnrollments count=' . count($pendingEnrollments));
        
        return view('teacher/enrollments', [
            'title' => 'Enrollment Requests',
            'enrollments' => $pendingEnrollments
        ]);
    }

    public function search()
    {
        $teacherId = (int) session()->get('user_id');
        $q = trim((string) $this->request->getGet('q'));

        $limit = 25;

        $courses = [];
        $enrollments = [];
        $materials = [];

        $db = \Config\Database::connect();

        // Teacher-owned courses are the boundary for what we search.
        $teacherCourseIds = $db->table('courses')
            ->select('id')
            ->where('teacher_id', $teacherId)
            ->get()
            ->getResultArray();
        $teacherCourseIds = array_values(array_filter(array_map(static function ($row) {
            return (int) ($row['id'] ?? 0);
        }, $teacherCourseIds)));

        if ($q !== '' && !empty($teacherCourseIds)) {
            $courses = $db->table('courses c')
                ->select('c.*')
                ->where('c.teacher_id', $teacherId)
                ->groupStart()
                    ->like('c.code', $q)
                    ->orLike('c.title', $q)
                    ->orLike('c.description', $q)
                ->groupEnd()
                ->orderBy('c.created_at', 'DESC')
                ->limit($limit)
                ->get()
                ->getResultArray();

            $enrollments = $db->table('enrollments e')
                ->select('e.*, u.name as student_name, u.email as student_email, c.title as course_title, c.code as course_code')
                ->join('users u', 'u.id = e.user_id')
                ->join('courses c', 'c.id = e.course_id')
                ->whereIn('e.course_id', $teacherCourseIds)
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
                ->whereIn('m.course_id', $teacherCourseIds)
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

        return view('teacher/search', [
            'title' => 'Teacher Search',
            'q' => $q,
            'courses' => $courses,
            'enrollments' => $enrollments,
            'materials' => $materials,
        ]);
    }
    
    /**
     * Handle enrollment status update (approve/reject)
     *
     * @param int $enrollmentId
     * @param string $status
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function updateEnrollmentStatus($enrollmentId, $status)
    {
        if (!in_array($status, ['approved', 'rejected'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid status.'
            ])->setStatusCode(400);
        }

        $rejectReason = null;
        if ($status === 'rejected') {
            $rejectReason = trim((string) $this->request->getPost('reject_reason'));
            if ($rejectReason === '') {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Reject reason is required.'
                ])->setStatusCode(400);
            }
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
        if (!$course) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Course not found.'
            ])->setStatusCode(404);
        }

        if ($course['teacher_id'] != session()->get('user_id')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You are not authorized to update this enrollment.'
            ])->setStatusCode(403);
        }

        // Update the enrollment status
        if ($this->enrollmentModel->updateStatus($enrollmentId, $status, $rejectReason)) {
            $statusText = $status === 'approved' ? 'approved' : 'rejected';
            
            // Notify the student
            $notificationModel = new \App\Models\NotificationModel();
            $notificationModel->insert([
                'user_id' => $enrollment['user_id'],
                'message' => $status === 'rejected'
                    ? ('Your enrollment in ' . $course['title'] . ' has been rejected. Reason: ' . $rejectReason)
                    : ('Your enrollment in ' . $course['title'] . ' has been ' . $statusText . '.'),
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
    
    /**
     * Enroll a student in a course (for teachers)
     */
    public function enrollStudent()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
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
            'status' => 'approved' // Direct enrollment by teacher is auto-approved
        ];

        if ($this->enrollmentModel->enrollUser($enrollmentData)) {
            // Notify the student
            $notificationModel = new \App\Models\NotificationModel();
            $notificationModel->insert([
                'user_id' => $studentId,
                'message' => 'You have been enrolled in ' . $course['title'] . ' by your teacher.',
                'is_read' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            
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
