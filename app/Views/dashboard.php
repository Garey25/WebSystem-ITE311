<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<?php
    $role = session('role');
    $userEmail = esc(session('userEmail'));
    $approvedCourses = $approvedCourses ?? [];
    $pendingCourses = $pendingCourses ?? [];
    $approvedCount = count($approvedCourses);
    $pendingCount = count($pendingCourses);
    $enrolledCount = $approvedCount + $pendingCount;
    $availableCount = isset($availableCourses) ? count($availableCourses) : 0;
    $manageableCount = isset($allCourses) ? count($allCourses) : 0;
    $materialsCount = 0;

    if (!empty($approvedCourses)) {
        foreach ($approvedCourses as $enrollment) {
            $materialsCount += isset($enrollment['materials']) ? count($enrollment['materials']) : 0;
        }
    }
?>

<div class="dashboard-wrapper">
    <div class="card dashboard-hero shadow-sm border-0 mb-4">
        <div class="card-body d-lg-flex justify-content-between align-items-center gap-4">
            <div>
                <p class="text-uppercase text-muted fw-semibold mb-1">Welcome back</p>
                <h1 class="fw-bold mb-2">Hi, <?= $userEmail ?> </h1>
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
                <h3 class="fw-bold mb-0" id="stat-enrolled-count"><?= $enrolledCount ?></h3>
                <small class="text-muted">Active learning paths</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card p-4">
                <p class="text-muted text-uppercase small mb-1">
                    <?= $role === 'admin' || $role === 'teacher' ? 'Courses Managed' : 'Available Courses' ?>
                </p>
                <h3 class="fw-bold mb-0" id="stat-available-count"><?= $role === 'admin' || $role === 'teacher' ? $manageableCount : $availableCount ?></h3>
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

    <div class="row mb-3">
        <div class="col-lg-6">
            <div class="input-group">
                <input type="text" id="dashboardGlobalSearch" class="form-control"
                    placeholder="Search in your dashboard..."
                    style="background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: #fff;">
                <span class="input-group-text" style="background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: #fff;">
                    <i class="bi bi-search"></i>
                </span>
            </div>
            <small class="text-muted">Tip: search courses, materials, and items shown on your dashboard.</small>
        </div>
    </div>

    <div id="alert-container" class="mb-4"></div>

    <?php if ($role === 'student'): ?>
        <div class="card dashboard-card shadow-sm border-0 mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted text-uppercase small mb-1">Enrollment</p>
                    <h5 class="mb-0">Pending Courses</h5>
                </div>
                <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2" id="pending-count-badge"><?= $pendingCount ?> pending</span>
            </div>
            <div class="card-body">
                <div id="pending-courses">
                    <?php if (!empty($pendingCourses)): ?>
                        <?php foreach ($pendingCourses as $enrollment): ?>
                            <div class="course-block mb-3 dashboard-course-item dashboard-search-target">
                                <div class="d-flex justify-content-between flex-wrap">
                                    <div class="pe-3">
                                        <h6 class="fw-semibold mb-1"><?= esc($enrollment['course_title']) ?></h6>
                                        <p class="text-muted mb-2"><?= esc($enrollment['course_description']) ?></p>
                                        <small class="text-muted">Requested on <?= date('M d, Y', strtotime($enrollment['enrolled_at'])) ?></small>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-warning-subtle text-warning px-3 py-2">Pending</span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <h6>No pending requests</h6>
                            <p class="text-muted mb-0">Your enrollment requests will appear here while waiting for approval.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="card dashboard-card shadow-sm border-0 mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted text-uppercase small mb-1">Learning hub</p>
                    <h5 class="mb-0">Approved Courses</h5>
                </div>
                <span class="badge bg-success bg-opacity-10 text-success px-3 py-2" id="approved-count-badge"><?= $approvedCount ?> active</span>
            </div>
            <div class="card-body">
                <div id="approved-courses">
                <?php if (!empty($approvedCourses)): ?>
                    <?php foreach ($approvedCourses as $enrollment): ?>
                        <div class="course-block mb-3 dashboard-course-item dashboard-search-target">
                            <div class="d-flex justify-content-between flex-wrap">
                                <div class="pe-3">
                                    <h6 class="fw-semibold mb-1"><?= esc($enrollment['course_title']) ?></h6>
                                    <p class="text-muted mb-2"><?= esc($enrollment['course_description']) ?></p>
                                    <small class="text-muted">Enrolled on <?= date('M d, Y', strtotime($enrollment['enrolled_at'])) ?></small>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-success-subtle text-success px-3 py-2">Approved</span>
                                </div>
                            </div>

                            <?php if (!empty($enrollment['materials'])): ?>
                                <div class="materials mt-3">
                                    <p class="text-muted text-uppercase small mb-2">Materials (<?= count($enrollment['materials']) ?>)</p>
                                    <div class="row g-2">
                                        <?php foreach ($enrollment['materials'] as $material): ?>
                                            <div class="col-md-6">
                                                <div class="material-tile dashboard-search-target">
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
                        <h6>No approved courses yet</h6>
                        <p class="text-muted mb-0">Once approved by your teacher, your courses will appear here.</p>
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
                        <div class="course-row dashboard-search-target">
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
        <?php if ($role === 'teacher'): ?>
            <div class="card dashboard-card shadow-sm border-0 mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted text-uppercase small mb-1">Manage course</p>
                        <h5 class="mb-0">Enrollment Requests</h5>
                    </div>
                    <a class="btn btn-primary" href="<?= site_url('teacher/enrollments') ?>">
                        View Requests
                    </a>
                </div>
                <div class="card-body">
                    <div class="course-row dashboard-search-target">
                        <div>
                            <h6 class="fw-semibold mb-1">Student Enrollment Requests</h6>
                            <p class="text-muted mb-0">Approve or reject pending enrollments for your courses.</p>
                        </div>
                        <a class="btn btn-outline-primary" href="<?= site_url('teacher/enrollments') ?>">
                            Manage
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
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
                        <div class="course-row dashboard-search-target">
                            <div>
                                <h6 class="fw-semibold mb-1"><?= esc($course['title']) ?></h6>
                                <p class="text-muted mb-0"><?= esc($course['description']) ?></p>
                            </div>
                            <div class="d-flex gap-2">
                                <?php if ($role === 'teacher'): ?>
                                    <a class="btn btn-outline-primary" href="<?= site_url('teacher/students?course_id=' . $course['id']) ?>">
                                        View Students
                                    </a>
                                <?php endif; ?>
                                <a class="btn btn-primary"
                                   href="<?= site_url('admin/course/' . $course['id'] . '/upload') ?>">
                                    Upload Materials
                                </a>
                            </div>
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
        $(document).on('click', '.enroll-btn', function(e) {
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
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                },
                dataType: 'text',
                success: function(rawResponse) {
                    const response = parseJsonResponse(rawResponse);

                    if (response && response.success) {
                        showAlert('success', response.message);
                        button.closest('.course-row').remove();
                        addToPendingCourses(courseId, courseTitle);
                        incrementCountersAfterEnroll();
                        ensureAvailableEmptyState();
                        return;
                    }

                    if (response && response.message) {
                        showAlert('danger', response.message);
                    } else {
                        showAlert('danger', 'An error occurred. Please try again.');
                    }
                    button.prop('disabled', false).text('Enroll');
                },
                error: function(xhr) {
                    const response = parseJsonResponse(xhr.responseText);

                    if (response && response.success) {
                        showAlert('success', response.message);
                        button.closest('.course-row').remove();
                        addToPendingCourses(courseId, courseTitle);
                        incrementCountersAfterEnroll();
                        ensureAvailableEmptyState();
                        return;
                    }

                    if (response && response.message) {
                        showAlert('danger', response.message);
                    } else {
                        showAlert('danger', 'An error occurred. Please try again.');
                    }
                    button.prop('disabled', false).text('Enroll');
                }
            });
        });

        function parseJsonResponse(raw) {
            if (!raw) {
                return null;
            }

            if (typeof raw === 'object') {
                return raw;
            }

            if (typeof raw !== 'string') {
                return null;
            }

            const start = raw.indexOf('{');
            const end = raw.lastIndexOf('}');
            if (start === -1 || end === -1 || end <= start) {
                return null;
            }

            const candidate = raw.slice(start, end + 1);
            try {
                return JSON.parse(candidate);
            } catch (e) {
                return null;
            }
        }

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

        function addToPendingCourses(courseId, courseTitle) {
            const pendingContainer = $('#pending-courses');
            if (!pendingContainer.length) {
                return;
            }

            if (pendingContainer.find('.empty-state').length) {
                pendingContainer.empty();
            }

            const currentDate = new Date().toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });

            const block = `
                <div class="course-block mb-3 dashboard-course-item dashboard-search-target">
                    <div class="d-flex justify-content-between flex-wrap">
                        <div class="pe-3">
                            <h6 class="fw-semibold mb-1">${courseTitle}</h6>
                            <p class="text-muted mb-2">Course description</p>
                            <small class="text-muted">Requested on ${currentDate}</small>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-warning-subtle text-warning px-3 py-2">Pending</span>
                        </div>
                    </div>
                </div>
            `;

            pendingContainer.prepend(block);
        }

        function incrementCountersAfterEnroll() {
            const pendingBadge = $('#pending-count-badge');
            const enrolledCount = $('#stat-enrolled-count');
            const availableCount = $('#stat-available-count');

            const pendingValue = parseInt((pendingBadge.text().match(/\d+/) || ['0'])[0], 10) + 1;
            pendingBadge.text(pendingValue + ' pending');

            const enrolledValue = parseInt((enrolledCount.text().match(/\d+/) || ['0'])[0], 10) + 1;
            enrolledCount.text(enrolledValue);

            const availableValueRaw = (availableCount.text().match(/\d+/) || ['0'])[0];
            const availableValue = Math.max(0, parseInt(availableValueRaw, 10) - 1);
            availableCount.text(availableValue);
        }

        function ensureAvailableEmptyState() {
            const availableContainer = $('#available-courses');
            if (!availableContainer.length) {
                return;
            }
            if (availableContainer.find('.course-row').length === 0 && availableContainer.find('.empty-state').length === 0) {
                availableContainer.html(`
                    <div class="empty-state">
                        <h6>No open courses right now</h6>
                        <p class="text-muted mb-0">Please check again later or contact your administrator.</p>
                    </div>
                `);
            }
        }

        // Global dashboard search (all roles)
        $('#dashboardGlobalSearch').on('keyup', function() {
            const searchTerm = ($(this).val() || '').toLowerCase().trim();

            if (!searchTerm) {
                $('.dashboard-search-target').show();
                return;
            }

            $('.dashboard-search-target').each(function() {
                const text = $(this).text().toLowerCase();
                $(this).toggle(text.indexOf(searchTerm) > -1);
            });
        });
    });
</script>

<style>
:root {
    --dashboard-bg: #003049;
    --card-bg: rgba(0, 48, 73, 0.6);
    --card-border: rgba(247, 127, 0, 0.3);
    --text-muted: #EAE2B7;
    --accent: #F77F00;
    --accent-light: #FCBF49;
    --danger: #D62828;
}

.dashboard-wrapper {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.dashboard-hero {
    background: linear-gradient(120deg, #003049, rgba(0, 48, 73, 0.8));
    color: #EAE2B7;
    border-radius: 1.25rem;
    border: 1px solid rgba(247, 127, 0, 0.3);
}

.dashboard-hero .btn-outline-light {
    border-color: rgba(255,255,255,0.3);
    color: #fff;
}

.stat-card {
    background: var(--card-bg);
    border-radius: 1rem;
    border: 1px solid var(--card-border);
    color: #EAE2B7;
}

.dashboard-card {
    background: var(--card-bg);
    border: 1px solid var(--card-border);
    border-radius: 1.25rem;
    color: #EAE2B7;
}

.dashboard-card .card-header {
    border-bottom: 1px solid rgba(247, 127, 0, 0.2);
    background: transparent;
}

.course-block,
.course-row,
.material-tile {
    background: rgba(234, 226, 183, 0.05);
    border: 1px solid rgba(247, 127, 0, 0.2);
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
    border: 1px dashed rgba(247, 127, 0, 0.3);
    border-radius: 1rem;
    color: #EAE2B7;
}

.text-muted {
    color: var(--text-muted) !important;
}

.badge.bg-success-subtle {
    background-color: rgba(252, 191, 73, 0.2) !important;
    color: #FCBF49 !important;
}

.badge.bg-primary.bg-opacity-10 {
    background-color: rgba(247, 127, 0, 0.2) !important;
    color: #F77F00 !important;
}

.badge.bg-success {
    background-color: #FCBF49 !important;
    color: #003049 !important;
}

.badge.bg-success.bg-opacity-10 {
    background-color: rgba(252, 191, 73, 0.2) !important;
    color: #FCBF49 !important;
}

#enrolledSearchInput::placeholder {
    color: rgba(234, 226, 183, 0.5);
}

#enrolledSearchInput:focus {
    background-color: rgba(234, 226, 183, 0.1) !important;
    border-color: #F77F00 !important;
    color: #EAE2B7 !important;
    box-shadow: 0 0 0 0.2rem rgba(247, 127, 0, 0.25);
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