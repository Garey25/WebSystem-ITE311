<?php

namespace App\Models;

use CodeIgniter\Model;

class EnrollmentModel extends Model
{
    protected $table = 'enrollments';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['user_id', 'course_id', 'enrolled_at', 'status', 'processed_at', 'reject_reason'];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
    protected $createdField = 'enrolled_at';
    protected $updatedField = '';
    protected $deletedField = '';

    // Validation
    protected $validationRules = [
        'user_id' => 'required|integer',
        'course_id' => 'required|integer',
        'status' => 'permit_empty|in_list[pending,approved,rejected]'
    ];
    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    /**
     * Enroll a user in a course
     *
     * @param array $data
     * @return bool|int
     */
    public function enrollUser($data)
    {
        // Set the enrollment date to current timestamp and default status to pending
        $data['enrolled_at'] = date('Y-m-d H:i:s');
        $data['status'] = $data['status'] ?? 'pending';
        
        return $this->insert($data);
    }
    
    /**
     * Update enrollment status
     *
     * @param int $enrollmentId
     * @param string $status
     * @param string|null $rejectReason
     * @return bool
     */
    public function updateStatus($enrollmentId, $status, $rejectReason = null)
    {
        $payload = [
            'status' => $status,
            'processed_at' => date('Y-m-d H:i:s')
        ];

        if ($status === 'rejected') {
            $payload['reject_reason'] = $rejectReason;
        } else {
            $payload['reject_reason'] = null;
        }

        return $this->update($enrollmentId, [
            'status' => $payload['status'],
            'processed_at' => $payload['processed_at'],
            'reject_reason' => $payload['reject_reason'],
        ]);
    }
    
    /**
     * Get pending enrollments for teacher's courses
     *
     * @param int $teacherId
     * @return array
     */
    public function getPendingEnrollments($teacherId)
    {
        return $this->select('enrollments.*, users.name as student_name, users.email as student_email, courses.title as course_title')
                   ->join('users', 'users.id = enrollments.user_id')
                   ->join('courses', 'courses.id = enrollments.course_id')
                   ->groupStart()
                       ->where('courses.teacher_id', $teacherId)
                       ->orWhere('courses.teacher_id', null)
                   ->groupEnd()
                   ->where('enrollments.status', 'pending')
                   ->orderBy('enrollments.enrolled_at', 'ASC')
                   ->findAll();
    }

    /**
     * Get all courses a user is enrolled in
     *
     * @param int $user_id
     * @return array
     */
    public function getUserEnrollments($user_id)
    {
        return $this->select('enrollments.*, courses.title as course_title, courses.description as course_description')
                    ->join('courses', 'courses.id = enrollments.course_id')
                    ->where('enrollments.user_id', $user_id)
                    ->findAll();
    }

    /**
     * Get all students enrolled in a specific course
     *
     * @param int $course_id
     * @return array
     */
    public function getCourseEnrollments($course_id)
    {
        return $this->select('enrollments.*, users.name as student_name, users.email as student_email, users.id as student_id')
                    ->join('users', 'users.id = enrollments.user_id')
                    ->where('enrollments.course_id', $course_id)
                    ->findAll();
    }

    /**
     * Check if a user is already enrolled in a specific course
     *
     * @param int $user_id
     * @param int $course_id
     * @return bool
     */
    /**
     * Check if a user is already enrolled in a course
     * Only returns true if the enrollment is approved
     *
     * @param int $user_id
     * @param int $course_id
     * @return bool
     */
    public function isAlreadyEnrolled($user_id, $course_id)
    {
        $enrollment = $this->where('user_id', $user_id)
                          ->where('course_id', $course_id)
                          ->where('status', 'approved')
                          ->first();
        
        return $enrollment !== null;
    }
    
    /**
     * Check if a user has a pending enrollment request for a course
     *
     * @param int $user_id
     * @param int $course_id
     * @return bool
     */
    public function hasPendingEnrollment($user_id, $course_id)
    {
        $enrollment = $this->where('user_id', $user_id)
                          ->where('course_id', $course_id)
                          ->where('status', 'pending')
                          ->first();
        
        return $enrollment !== null;
    }
}
