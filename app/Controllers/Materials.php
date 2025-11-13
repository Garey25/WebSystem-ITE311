<?php

namespace App\Controllers;

use App\Models\MaterialModel;
use App\Models\CourseModel;
use App\Models\EnrollmentModel;

class Materials extends BaseController
{
    protected $materialModel;
    protected $courseModel;
    protected $enrollmentModel;

    public function __construct()
    {
        $this->materialModel = new MaterialModel();
        $this->courseModel = new CourseModel();
        $this->enrollmentModel = new EnrollmentModel();
    }

    public function upload($course_id)
    {
        if (! $this->request->is('post')) {
            $course = $this->courseModel->find($course_id);
            if (! $course) {
                return redirect()->back()->with('error', 'Course not found');
            }
            return view('materials/upload', ['course' => $course]);
        }

        // POST: handle upload with explicit checks to avoid redirecting to wrong referer
        $file = $this->request->getFile('material');
        if ($file === null || ! $file->isValid()) {
            return redirect()->to(site_url('admin/course/' . $course_id . '/upload'))
                ->with('error', 'Please choose a valid file to upload.');
        }

        // Enforce size (<= 10MB) and extensions
        $allowedExt = ['pdf','ppt','pptx','doc','docx','zip','rar','txt'];
        $ext = strtolower($file->getExtension() ?? '');
        if (! in_array($ext, $allowedExt, true)) {
            return redirect()->to(site_url('admin/course/' . $course_id . '/upload'))
                ->with('error', 'Invalid file type. Allowed: ' . implode(', ', $allowedExt));
        }
        if ($file->getSize() > 10 * 1024 * 1024) { // 10MB
            return redirect()->to(site_url('admin/course/' . $course_id . '/upload'))
                ->with('error', 'File too large. Max size is 10MB.');
        }

        $originalName = $file->getClientName();
        $targetDir = WRITEPATH . 'uploads/materials/' . $course_id;
        if (! is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $newName = $file->getRandomName();
        $file->move($targetDir, $newName);

        $this->materialModel->insertMaterial([
            'course_id' => (int) $course_id,
            'file_name' => $originalName,
            'file_path' => 'uploads/materials/' . $course_id . '/' . $newName,
        ]);

        return redirect()->to(site_url('admin/course/' . $course_id . '/upload'))
            ->with('success', 'Material uploaded successfully');
    }

    public function delete($material_id)
    {
        $material = $this->materialModel->find($material_id);
        if (! $material) {
            return redirect()->back()->with('error', 'Material not found');
        }
        $fullPath = WRITEPATH . $material['file_path'];
        if (is_file($fullPath)) {
            @unlink($fullPath);
        }
        $this->materialModel->delete($material_id);
        return redirect()->back()->with('success', 'Material deleted');
    }

    public function download($material_id)
    {
        if (! session()->get('isLoggedIn')) {
            return redirect()->to(site_url('login'));
        }
        $material = $this->materialModel->find($material_id);
        if (! $material) {
            return redirect()->back()->with('error', 'Material not found');
        }
        $userId = (int) session('user_id');
        $enrolled = $this->enrollmentModel->where('user_id', $userId)
            ->where('course_id', (int) $material['course_id'])->first();
        if (! $enrolled && session('role') !== 'admin' && session('role') !== 'teacher') {
            return redirect()->back()->with('error', 'Unauthorized');
        }
        $fullPath = WRITEPATH . $material['file_path'];
        if (! is_file($fullPath)) {
            return redirect()->back()->with('error', 'File missing');
        }
        return $this->response->download($fullPath, null)->setFileName($material['file_name']);
    }
}


