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
    protected $allowedFields = ['title', 'description'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = '';

    // Validation
    protected $validationRules = [
        'title' => 'required|min_length[3]|max_length[200]',
        'description' => 'permit_empty'
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
