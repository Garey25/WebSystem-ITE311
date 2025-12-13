<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CourseModel;
use App\Models\EnrollmentModel;
use App\Models\MaterialModel;

class Student extends BaseController
{
    protected $courseModel;
    protected $enrollmentModel;
    protected $materialModel;

    public function __construct()
    {
        $this->courseModel = new CourseModel();
        $this->enrollmentModel = new EnrollmentModel();
        $this->materialModel = new MaterialModel();

        if (session()->get('role') !== 'student') {
            throw new \CodeIgniter\Exceptions\PageNotFoundException();
        }
    }

    public function enrollments()
    {
        $userId = (int) session()->get('user_id');
        $enrollments = $this->enrollmentModel->getUserEnrollments($userId);

        return view('student/enrollments', [
            'title' => 'My Enrollments',
            'enrollments' => $enrollments,
        ]);
    }

    public function grades()
    {
        return view('student/grades', [
            'title' => 'My Grades',
        ]);
    }

    public function progress()
    {
        return view('student/progress', [
            'title' => 'My Progress',
        ]);
    }

    public function search()
    {
        $userId = (int) session()->get('user_id');
        $q = trim((string) $this->request->getGet('q'));

        $limit = 25;

        $courses = [];
        $enrollments = [];
        $materials = [];

        $db = \Config\Database::connect();

        $courseIds = $db->table('enrollments')
            ->select('course_id')
            ->where('user_id', $userId)
            ->get()
            ->getResultArray();
        $courseIds = array_values(array_filter(array_map(static function ($row) {
            return (int) ($row['course_id'] ?? 0);
        }, $courseIds)));

        if ($q !== '' && !empty($courseIds)) {
            $courses = $db->table('courses c')
                ->select('c.*')
                ->whereIn('c.id', $courseIds)
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
                ->select('e.*, c.title as course_title, c.code as course_code')
                ->join('courses c', 'c.id = e.course_id')
                ->where('e.user_id', $userId)
                ->groupStart()
                    ->like('c.title', $q)
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
                ->whereIn('m.course_id', $courseIds)
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

        return view('student/search', [
            'title' => 'Student Search',
            'q' => $q,
            'courses' => $courses,
            'enrollments' => $enrollments,
            'materials' => $materials,
        ]);
    }
}
