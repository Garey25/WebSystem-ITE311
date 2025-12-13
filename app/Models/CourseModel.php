<?php

namespace App\Models;

use CodeIgniter\Model;

class CourseModel extends Model
{
    protected $table = 'courses';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'code',
        'title',
        'description',
        'school_year',
        'semester',
        'start_date',
        'end_date',
        'schedule',
        'teacher_id',
        'status',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = '';

    // Validation
    protected $validationRules = [
        'code' => 'permit_empty|max_length[20]|regex_match[/^[A-Za-z0-9]+$/]',
        'title' => 'required|min_length[3]|max_length[200]|regex_match[/^[\p{L}\d ]+$/u]',
        'description' => 'permit_empty',
        'school_year' => 'permit_empty|regex_match[/^\d{4}-\d{4}$/]',
        'semester' => 'permit_empty|in_list[1st,2nd,summer]',
        'status' => 'permit_empty|in_list[active,inactive]'
    ];
    protected $validationMessages = [
        'code' => [
            'regex_match' => 'Course code may only contain letters and numbers.',
            'max_length' => 'Course code cannot exceed 20 characters.',
        ],
        'title' => [
            'regex_match' => 'Course title may only contain letters, numbers, and spaces.',
        ],
        'school_year' => [
            'regex_match' => 'School year must be in the format YYYY-YYYY (e.g., 2024-2025).',
        ],
        'semester' => [
            'in_list' => 'Semester must be 1st, 2nd, or summer.',
        ],
        'status' => [
            'in_list' => 'Status must be active or inactive.',
        ],
    ];
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
     * Get all available courses
     *
     * @return array
     */
    public function getAllCourses()
    {
        return $this->findAll();
    }

    /**
     * Get course by ID
     *
     * @param int $id
     * @return array|null
     */
    public function getCourseById($id)
    {
        return $this->find($id);
    }

    /**
     * Search courses by title or description
     *
     * @param string $searchTerm
     * @return array
     */
    public function searchCourses($searchTerm)
    {
        return $this->like('title', $searchTerm)
                    ->orLike('description', $searchTerm)
                    ->findAll();
    }
}
