<?= $this->extend('template') ?>

<?= $this->section('content') ?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h1 class="fw-bold mb-1">Teacher Search</h1>
        <p class="text-muted mb-0">Search your courses, enrollments, and materials.</p>
    </div>

    <form class="d-flex gap-2" method="get" action="<?= site_url('teacher/search') ?>" style="min-width: 320px;">
        <input
            type="text"
            class="form-control"
            name="q"
            placeholder="Search students, courses, materials..."
            value="<?= esc($q ?? '') ?>"
        >
        <button type="submit" class="btn btn-primary">Search</button>
    </form>
</div>

<?php if (trim((string) ($q ?? '')) === ''): ?>
    <div class="alert alert-info">Enter a keyword to search.</div>
<?php endif; ?>

<div class="row g-4">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header">
                <h5 class="mb-0">Courses</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Code</th>
                                <th>Title</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($courses)): ?>
                                <?php foreach ($courses as $course): ?>
                                    <tr>
                                        <td><?= esc($course['id'] ?? '') ?></td>
                                        <td><?= esc($course['code'] ?? '') ?></td>
                                        <td><?= esc($course['title'] ?? '') ?></td>
                                        <td><?= esc($course['status'] ?? '') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">No courses found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header">
                <h5 class="mb-0">Enrollments / Students</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Student</th>
                                <th>Email</th>
                                <th>Course</th>
                                <th>Status</th>
                                <th>Enrolled At</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($enrollments)): ?>
                                <?php foreach ($enrollments as $e): ?>
                                    <tr>
                                        <td><?= esc($e['id'] ?? '') ?></td>
                                        <td><?= esc($e['student_name'] ?? '') ?></td>
                                        <td><?= esc($e['student_email'] ?? '') ?></td>
                                        <td><?= esc(($e['course_code'] ?? '') . ' - ' . ($e['course_title'] ?? '')) ?></td>
                                        <td><?= esc($e['status'] ?? '') ?></td>
                                        <td><?= esc($e['enrolled_at'] ?? '') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No enrollments found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header">
                <h5 class="mb-0">Materials</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>File Name</th>
                                <th>Course</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($materials)): ?>
                                <?php foreach ($materials as $m): ?>
                                    <tr>
                                        <td><?= esc($m['id'] ?? '') ?></td>
                                        <td><?= esc($m['file_name'] ?? '') ?></td>
                                        <td><?= esc(($m['course_code'] ?? '') . ' - ' . ($m['course_title'] ?? '')) ?></td>
                                        <td><?= esc($m['created_at'] ?? '') ?></td>
                                        <td>
                                            <?php if (!empty($m['course_id'])): ?>
                                                <a class="btn btn-sm btn-outline-primary" href="<?= site_url('admin/course/' . (int) $m['course_id'] . '/upload') ?>">Open</a>
                                            <?php endif; ?>
                                            <?php if (!empty($m['id'])): ?>
                                                <a class="btn btn-sm btn-outline-secondary" href="<?= site_url('materials/download/' . (int) $m['id']) ?>">Download</a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">No materials found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
