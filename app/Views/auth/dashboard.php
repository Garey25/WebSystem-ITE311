<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php $role = esc($role ?? 'student'); ?>
<div class="alert alert-success mb-4">Welcome, <?= esc(session('name')) ?>!</div>

<h1 class="h4 mb-3">Dashboard</h1>

<?php if ($role === 'admin'): ?>
  <div class="row g-4">
    <div class="col-md-4">
      <div class="card h-100">
        <div class="card-header">System Overview</div>
        <div class="card-body">
          <p class="mb-1"><strong>Total Users:</strong> <?= esc($stats['users'] ?? 0) ?></p>
          <p class="mb-1"><strong>Total Courses:</strong> <?= esc($stats['courses'] ?? 0) ?></p>
          <p class="mb-0"><strong>Total Lessons:</strong> <?= esc($stats['lessons'] ?? 0) ?></p>
        </div>
      </div>
    </div>
    <div class="col-md-8">
      <div class="card h-100">
        <div class="card-header">Admin Actions</div>
        <div class="card-body d-grid gap-2">
          <a class="btn btn-primary" href="#">Manage Users</a>
          <a class="btn btn-outline-secondary" href="#">Manage Courses</a>
          <a class="btn btn-outline-secondary" href="#">Site Settings</a>
        </div>
      </div>
    </div>
  </div>

<?php elseif ($role === 'teacher'): ?>
  <div class="row g-4">
    <div class="col-md-4">
      <div class="card h-100">
        <div class="card-header">My Teaching</div>
        <div class="card-body">
          <p class="mb-1"><strong>My Courses:</strong> <?= esc($stats['my_courses'] ?? 0) ?></p>
          <p class="mb-0"><strong>Quizzes:</strong> <?= esc($stats['quizzes'] ?? 0) ?></p>
        </div>
      </div>
    </div>
    <div class="col-md-8">
      <div class="card h-100">
        <div class="card-header">Teacher Shortcuts</div>
        <div class="card-body d-grid gap-2">
          <a class="btn btn-primary" href="#">Create Course</a>
          <a class="btn btn-outline-secondary" href="#">Create Quiz</a>
          <a class="btn btn-outline-secondary" href="#">Grade Submissions</a>
        </div>
      </div>
    </div>
  </div>

<?php else: ?>
  <div class="row g-4">
    <div class="col-md-4">
      <div class="card h-100">
        <div class="card-header">My Learning</div>
        <div class="card-body">
          <p class="mb-1"><strong>Enrolled Courses:</strong> <?= esc($stats['enrolled'] ?? 0) ?></p>
          <p class="mb-0"><strong>Quizzes Assigned:</strong> <?= esc($stats['quizzes'] ?? 0) ?></p>
        </div>
      </div>
    </div>
    <div class="col-md-8">
      <div class="card h-100">
        <div class="card-header">Student Shortcuts</div>
        <div class="card-body d-grid gap-2">
          <a class="btn btn-primary" href="#">Browse Courses</a>
          <a class="btn btn-outline-secondary" href="#">View Grades</a>
          <a class="btn btn-outline-secondary" href="#">Upcoming Deadlines</a>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>

<?= $this->endSection() ?>
