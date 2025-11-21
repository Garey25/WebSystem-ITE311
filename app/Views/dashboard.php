<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<?php
    $role = session('role');
    $userEmail = esc(session('userEmail'));
    $enrolledCount = isset($enrolledCourses) ? count($enrolledCourses) : 0;
    $availableCount = isset($availableCourses) ? count($availableCourses) : 0;
    $manageableCount = isset($allCourses) ? count($allCourses) : 0;
    $materialsCount = 0;

    if (!empty($enrolledCourses)) {
        foreach ($enrolledCourses as $enrollment) {
            $materialsCount += isset($enrollment['materials']) ? count($enrollment['materials']) : 0;
        }
    }
?>

<div class="dashboard-wrapper">
    <div class="card dashboard-hero shadow-sm border-0 mb-4">
        <div class="card-body d-lg-flex justify-content-between align-items-center gap-4">
            <div>
                <p class="text-uppercase text-muted fw-semibold mb-1">Welcome back</p>
                <h1 class="fw-bold mb-2">Hi, <?= $userEmail ?> 👋</h1>
                <p class="text-muted mb-0">
                    Stay on top of your learning journey with a quick overview of your courses and recent activity.
                </p>
            </div>
            <div class="text-lg-end mt-3 mt-lg-0">
                <span class="badge rounded-pill bg-light text-dark px-3 py-2 text-uppercase">Role: <?= ucfirst($role ?? 'Guest') ?></span>
                <div class="mt-3">
                    <a href="<?= base_url('logout') ?>" class="btn btn-outline-light btn-lg px-4">Log out</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="stat-card p-4">
                <p class="text-muted text-uppercase small mb-1">Enrolled Courses</p>
                <h3 class="fw-bold mb-0"><?= $enrolledCount ?></h3>
                <small class="text-muted">Active learning paths</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card p-4">
                <p class="text-muted text-uppercase small mb-1">
                    <?= $role === 'admin' || $role === 'teacher' ? 'Courses Managed' : 'Available Courses' ?>
                </p>
                <h3 class="fw-bold mb-0">
                    <?= $role === 'admin' || $role === 'teacher' ? $manageableCount : $availableCount ?>
                </h3>
                <small class="text-muted">
                    <?= $role === 'admin' || $role === 'teacher' ? 'Total classes you oversee' : 'Open for enrollment' ?>
                </small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card p-4">
                <p class="text-muted text-uppercase small mb-1">Course Materials</p>
                <h3 class="fw-bold mb-0"><?= $materialsCount ?></h3>
                <small class="text-muted">Resources shared with you</small>
            </div>
        </div>
    </div>

    <div id="alert-container" class="mb-4"></div>

    <?php if ($role === 'student'): ?>
        <div class="card dashboard-card shadow-sm border-0 mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted text-uppercase small mb-1">Learning hub</p>
                    <h5 class="mb-0">My Enrolled Courses</h5>
                </div>
                <span class="badge bg-success bg-opacity-10 text-success px-3 py-2"><?= $enrolledCount ?> active</span>
            </div>
            <div class="card-body" id="enrolled-courses">
                <?php if (!empty($enrolledCourses)): ?>
                    <?php foreach ($enrolledCourses as $enrollment): ?>
                        <div class="course-block mb-3">
                            <div class="d-flex justify-content-between flex-wrap">
                                <div class="pe-3">
                                    <h6 class="fw-semibold mb-1"><?= esc($enrollment['course_title']) ?></h6>
                                    <p class="text-muted mb-2"><?= esc($enrollment['course_description']) ?></p>
                                    <small class="text-muted">Enrolled on <?= date('M d, Y', strtotime($enrollment['enrolled_at'])) ?></small>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-success-subtle text-success px-3 py-2">Enrolled</span>
                                </div>
                            </div>

                            <?php if (!empty($enrollment['materials'])): ?>
                                <div class="materials mt-3">
                                    <p class="text-muted text-uppercase small mb-2">Materials (<?= count($enrollment['materials']) ?>)</p>
                                    <div class="row g-2">
                                        <?php foreach ($enrollment['materials'] as $material): ?>
                                            <div class="col-md-6">
                                                <div class="material-tile">
                                                    <div>
                                                        <p class="mb-1 fw-semibold"><?= esc($material['file_name']) ?></p>
                                                        <small class="text-muted"><?= date('M j, Y', strtotime($material['created_at'])) ?></small>
                                                    </div>
                                                    <a href="<?= site_url('materials/download/' . $material['id']) ?>" class="btn btn-sm btn-outline-primary">
                                                        Download
                                                    </a>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="materials mt-3">
                                    <div class="material-tile justify-content-start">
                                        <p class="mb-0 text-muted">No materials uploaded yet. Check back soon.</p>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <h6>No enrollments yet</h6>
                        <p class="text-muted mb-0">Browse the available courses below and start learning.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="card dashboard-card shadow-sm border-0">
            <div class="card-header">
                <p class="text-muted text-uppercase small mb-1">Grow your skills</p>
                <h5 class="mb-0">Available Courses</h5>
            </div>
            <div class="card-body" id="available-courses">
                <?php if (!empty($availableCourses)): ?>
                    <?php foreach ($availableCourses as $course): ?>
                        <div class="course-row">
                            <div>
                                <h6 class="fw-semibold mb-1"><?= esc($course['title']) ?></h6>
                                <p class="text-muted mb-0"><?= esc($course['description']) ?></p>
                            </div>
                            <button class="btn btn-primary enroll-btn"
                                    data-course-id="<?= $course['id'] ?>"
                                    data-course-title="<?= esc($course['title']) ?>">
                                Enroll
                            </button>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <h6>No open courses right now</h6>
                        <p class="text-muted mb-0">Please check again later or contact your administrator.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php elseif ($role === 'admin' || $role === 'teacher'): ?>
        <div class="card dashboard-card shadow-sm border-0">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted text-uppercase small mb-1">Teaching workspace</p>
                    <h5 class="mb-0">Manage Course Materials</h5>
                </div>
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2"><?= $manageableCount ?> courses</span>
            </div>
            <div class="card-body">
                <?php if (!empty($allCourses)): ?>
                    <?php foreach ($allCourses as $course): ?>
                        <div class="course-row">
                            <div>
                                <h6 class="fw-semibold mb-1"><?= esc($course['title']) ?></h6>
                                <p class="text-muted mb-0"><?= esc($course['description']) ?></p>
                            </div>
                            <a class="btn btn-primary"
                               href="<?= site_url('admin/course/' . $course['id'] . '/upload') ?>">
                                Upload Materials
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <h6>No courses found</h6>
                        <p class="text-muted mb-0">Create a new course to start sharing materials.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('.enroll-btn').on('click', function(e) {
            e.preventDefault();

            const button = $(this);
            const courseId = button.data('course-id');
            const courseTitle = button.data('course-title');

            button.prop('disabled', true).text('Enrolling...');

            $.ajax({
                url: '<?= base_url('course/enroll') ?>',
                type: 'POST',
                data: {
                    course_id: courseId,
                    <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        showAlert('success', response.message);
                        button.replaceWith('<span class="badge bg-success-subtle text-success px-3 py-2">Enrolled</span>');
                        addToEnrolledCourses(courseId, courseTitle);
                    } else {
                        showAlert('danger', response.message);
                        button.prop('disabled', false).text('Enroll');
                    }
                },
                error: function() {
                    showAlert('danger', 'An error occurred. Please try again.');
                    button.prop('disabled', false).text('Enroll');
                }
            });
        });

        function showAlert(type, message) {
            const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
            $('#alert-container').html(alertHtml);

            setTimeout(function() {
                $('.alert').fadeOut();
            }, 5000);
        }

        function addToEnrolledCourses(courseId, courseTitle) {
            const enrolledContainer = $('#enrolled-courses');
            if (!enrolledContainer.length) {
                return;
            }

            if (enrolledContainer.find('.empty-state').length) {
                enrolledContainer.empty();
            }

            const currentDate = new Date().toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });

            const block = `
                <div class="course-block mb-3">
                    <div class="d-flex justify-content-between flex-wrap">
                        <div class="pe-3">
                            <h6 class="fw-semibold mb-1">${courseTitle}</h6>
                            <p class="text-muted mb-2">Course description</p>
                            <small class="text-muted">Enrolled on ${currentDate}</small>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-success-subtle text-success px-3 py-2">Enrolled</span>
                        </div>
                    </div>
                </div>
            `;

            enrolledContainer.prepend(block);
        }
    });
</script>

<style>
:root {
    --dashboard-bg: #111315;
    --card-bg: #1b1f24;
    --card-border: rgba(255, 255, 255, 0.08);
    --text-muted: #9da7b5;
    --accent: #1db954;
}

.dashboard-wrapper {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.dashboard-hero {
    background: linear-gradient(120deg, #1f2937, #111827);
    color: #fff;
    border-radius: 1.25rem;
}

.dashboard-hero .btn-outline-light {
    border-color: rgba(255,255,255,0.3);
    color: #fff;
}

.stat-card {
    background: var(--card-bg);
    border-radius: 1rem;
    border: 1px solid var(--card-border);
    color: #fff;
}

.dashboard-card {
    background: var(--card-bg);
    border: 1px solid var(--card-border);
    border-radius: 1.25rem;
    color: #fff;
}

.dashboard-card .card-header {
    border-bottom: 1px solid rgba(255,255,255,0.06);
    background: transparent;
}

.course-block,
.course-row,
.material-tile {
    background: rgba(255,255,255,0.02);
    border: 1px solid rgba(255,255,255,0.04);
    border-radius: 1rem;
    padding: 1.25rem;
}

.course-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
}

.course-row:last-child {
    margin-bottom: 0;
}

.material-tile {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
}

.empty-state {
    text-align: center;
    padding: 2rem 1rem;
    border: 1px dashed rgba(255,255,255,0.2);
    border-radius: 1rem;
    color: #fff;
}

.text-muted {
    color: var(--text-muted) !important;
}

.badge.bg-success-subtle {
    background-color: rgba(74, 222, 128, 0.15) !important;
    color: #4ade80 !important;
}

.badge.bg-primary.bg-opacity-10 {
    background-color: rgba(59, 130, 246, 0.15) !important;
    color: #93c5fd !important;
}

.badge.bg-success {
    background-color: #22c55e !important;
}

@media (max-width: 767px) {
    .course-row {
        flex-direction: column;
        align-items: flex-start;
    }

    .stat-card {
        text-align: center;
    }
}
</style>
<?= $this->endSection() ?>