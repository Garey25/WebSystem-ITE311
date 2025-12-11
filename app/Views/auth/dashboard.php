<?= $this->extend('template') ?>
<?= $this->section('content') ?>

<?php $role = esc($role ?? 'student'); ?>
<div class="alert alert-success mb-4">
    <h5 class="mb-1">Welcome back, <?= esc($user_name ?? session('name')) ?>!</h5>
    <p class="mb-0">You are logged in as <strong><?= ucfirst($role) ?></strong></p>
</div>

<h1 class="h3 mb-4">Dashboard - <?= ucfirst($role) ?> Panel</h1>

<?php if ($role === 'admin'): ?>
  <!-- Admin Dashboard -->
  <div class="row g-4 mb-4">
    <div class="col-md-3">
      <div class="card h-100 text-center">
        <div class="card-body">
          <h2 class="text-primary"><?= esc($stats['total_users'] ?? 0) ?></h2>
          <p class="mb-0">Total Users</p>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card h-100 text-center">
        <div class="card-body">
          <h2 class="text-success"><?= esc($stats['total_courses'] ?? 0) ?></h2>
          <p class="mb-0">Total Courses</p>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card h-100 text-center">
        <div class="card-body">
          <h2 class="text-warning"><?= esc($stats['total_lessons'] ?? 0) ?></h2>
          <p class="mb-0">Total Lessons</p>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card h-100 text-center">
        <div class="card-body">
          <h2 class="text-info"><?= esc($stats['total_enrollments'] ?? 0) ?></h2>
          <p class="mb-0">Enrollments</p>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-4">
    <div class="col-md-8">
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">Recent Users</h5>
        </div>
        <div class="card-body">
          <?php if (!empty($recent_users)): ?>
            <div class="table-responsive">
              <table class="table table-dark table-striped">
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Joined</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($recent_users as $user): ?>
                    <tr>
                      <td><?= esc($user['name']) ?></td>
                      <td><?= esc($user['email']) ?></td>
                      <td><span class="badge bg-primary"><?= esc($user['role']) ?></span></td>
                      <td><?= date('M j, Y', strtotime($user['created_at'])) ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php else: ?>
            <p class="text-muted">No users found.</p>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card h-100">
        <div class="card-header">
          <h5 class="mb-0">Admin Actions</h5>
        </div>
        <div class="card-body d-grid gap-2">
          <a class="btn btn-primary" href="<?= site_url('admin/users') ?>">Manage Users</a>
          <a class="btn btn-outline-secondary" href="#">Manage Courses</a>
          <a class="btn btn-outline-secondary" href="#">System Settings</a>
          <a class="btn btn-outline-secondary" href="#">View Reports</a>
        </div>
      </div>
    </div>
  </div>

<?php elseif ($role === 'teacher'): ?>
  <!-- Teacher Dashboard -->
  <div class="row g-4 mb-4">
    <div class="col-md-3">
      <div class="card h-100 text-center">
        <div class="card-body">
          <h2 class="text-primary"><?= esc($stats['my_courses'] ?? 0) ?></h2>
          <p class="mb-0">My Courses</p>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card h-100 text-center">
        <div class="card-body">
          <h2 class="text-success"><?= esc($stats['total_lessons'] ?? 0) ?></h2>
          <p class="mb-0">Lessons Created</p>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card h-100 text-center">
        <div class="card-body">
          <h2 class="text-warning"><?= esc($stats['total_quizzes'] ?? 0) ?></h2>
          <p class="mb-0">Quizzes Created</p>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card h-100 text-center">
        <div class="card-body">
          <h2 class="text-info"><?= esc($stats['total_students'] ?? 0) ?></h2>
          <p class="mb-0">Total Students</p>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-4">
    <div class="col-md-8">
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">My Courses</h5>
        </div>
        <div class="card-body">
          <?php if (!empty($my_courses_list)): ?>
            <div class="list-group list-group-flush">
              <?php foreach ($my_courses_list as $course): ?>
                <div class="list-group-item bg-transparent border-secondary">
                  <div class="d-flex justify-content-between align-items-start">
                    <div>
                      <h6 class="mb-1"><?= esc($course['title']) ?></h6>
                      <p class="mb-1 text-muted"><?= esc($course['description']) ?></p>
                      <small class="text-muted">Created: <?= date('M j, Y', strtotime($course['created_at'])) ?></small>
                    </div>
                    <div>
                      <a href="#" class="btn btn-sm btn-outline-primary">Manage</a>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <p class="text-muted">No courses found. <a href="#" class="text-primary">Create your first course</a></p>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card h-100">
        <div class="card-header">
          <h5 class="mb-0">Teacher Tools</h5>
        </div>
        <div class="card-body d-grid gap-2">
          <a class="btn btn-primary" href="#">Create New Course</a>
          <a class="btn btn-outline-secondary" href="#">Create Quiz</a>
          <a class="btn btn-outline-secondary" href="#">Grade Submissions</a>
          <a class="btn btn-outline-secondary" href="#">View Analytics</a>
        </div>
      </div>
    </div>
  </div>

<?php else: ?>
  <!-- Student Dashboard -->
  <div class="row g-4 mb-4">
    <div class="col-md-4">
      <div class="card h-100 text-center">
        <div class="card-body">
          <h2 class="text-primary"><?= esc($stats['enrolled_courses'] ?? 0) ?></h2>
          <p class="mb-0">Enrolled Courses</p>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card h-100 text-center">
        <div class="card-body">
          <h2 class="text-success"><?= esc($stats['completed_quizzes'] ?? 0) ?></h2>
          <p class="mb-0">Completed Quizzes</p>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card h-100 text-center">
        <div class="card-body">
          <h2 class="text-warning"><?= esc($stats['total_quizzes'] ?? 0) ?></h2>
          <p class="mb-0">Available Quizzes</p>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-4">
    <div class="col-md-8">
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">My Enrollments</h5>
        </div>
        <div class="card-body">
          <?php if (!empty($my_enrollments)): ?>
            <div class="list-group list-group-flush">
              <?php foreach ($my_enrollments as $enrollment): ?>
                <div class="list-group-item bg-transparent border-secondary mb-3">
                  <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="flex-grow-1">
                      <h6 class="mb-1"><?= esc($enrollment['course_title']) ?></h6>
                      <p class="mb-1 text-muted"><?= esc($enrollment['description']) ?></p>
                      <small class="text-muted">Enrolled: <?= date('M j, Y', strtotime($enrollment['enrolled_at'])) ?></small>
                    </div>
                  </div>
                  
                  <!-- Course Materials Section -->
                  <?php if (!empty($enrollment['materials'])): ?>
                    <div class="mt-3 pt-3 border-top border-secondary">
                      <h6 class="mb-2 text-info">
                        <i class="bi bi-file-earmark"></i> Course Materials (<?= count($enrollment['materials']) ?>)
                      </h6>
                      <div class="list-group list-group-flush">
                        <?php foreach ($enrollment['materials'] as $material): ?>
                          <div class="list-group-item bg-transparent border-secondary px-0 py-2">
                            <div class="d-flex justify-content-between align-items-center">
                              <div>
                                <i class="bi bi-file-earmark-text me-2"></i>
                                <span><?= esc($material['file_name']) ?></span>
                                <small class="text-muted ms-2">
                                  (<?= date('M j, Y', strtotime($material['created_at'])) ?>)
                                </small>
                              </div>
                              <a href="<?= site_url('materials/download/' . $material['id']) ?>" 
                                 class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-download"></i> Download
                              </a>
                            </div>
                          </div>
                        <?php endforeach; ?>
                      </div>
                    </div>
                  <?php else: ?>
                    <div class="mt-2 pt-2 border-top border-secondary">
                      <small class="text-muted">
                        <i class="bi bi-info-circle"></i> No materials available for this course yet.
                      </small>
                    </div>
                  <?php endif; ?>
                </div>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <p class="text-muted">You haven't enrolled in any courses yet. <a href="#" class="text-primary">Browse available courses</a></p>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card h-100">
        <div class="card-header">
          <h5 class="mb-0">Student Actions</h5>
        </div>
        <div class="card-body d-grid gap-2">
          <a class="btn btn-primary" href="#">Browse Courses</a>
          <a class="btn btn-outline-secondary" href="#">View Grades</a>
          <a class="btn btn-outline-secondary" href="#">Upcoming Deadlines</a>
          <a class="btn btn-outline-secondary" href="#">My Progress</a>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>

<?= $this->endSection() ?>
