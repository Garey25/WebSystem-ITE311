<?= $this->extend('template') ?>

<?= $this->section('content') ?>

<?= csrf_meta() ?>

<div id="course-notification-container" class="position-fixed top-0 start-50 translate-middle-x mt-3" style="z-index: 9999; min-width: 300px;"></div>

<!-- Add Course Modal -->
<div class="modal fade" id="addCourseModal" tabindex="-1" aria-labelledby="addCourseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="background-color: rgba(0, 48, 73, 0.95); border: 1px solid rgba(247, 127, 0, 0.3); color: #EAE2B7;">
            <div class="modal-header" style="border-bottom: 1px solid rgba(247, 127, 0, 0.2);">
                <h5 class="modal-title" id="addCourseModalLabel">Add New Course</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addCourseForm">
                <div class="modal-body">
                    <div id="addCourseAlert"></div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="addCourseCode" class="form-label">Course Code</label>
                            <input type="text" class="form-control" id="addCourseCode" name="code" placeholder="e.g., CS101">
                        </div>
                        <div class="col-md-8">
                            <label for="addCourseTitle" class="form-label">Course Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="addCourseTitle" name="title" required>
                        </div>
                        <div class="col-md-12">
                            <label for="addCourseDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="addCourseDescription" name="description" rows="3"></textarea>
                        </div>
                        <div class="col-md-4">
                            <label for="addSchoolYear" class="form-label">School Year</label>
                            <input type="text" class="form-control" id="addSchoolYear" name="school_year" placeholder="e.g., 2024-2025">
                        </div>
                        <div class="col-md-4">
                            <label for="addSemester" class="form-label">Semester</label>
                            <select class="form-select" id="addSemester" name="semester">
                                <option value="">Select Semester</option>
                                <option value="1st">1st Semester</option>
                                <option value="2nd">2nd Semester</option>
                                <option value="summer">Summer</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="addStatus" class="form-label">Status</label>
                            <select class="form-select" id="addStatus" name="status">
                                <option value="active">Active</option>
                                <option value="inactive" selected>Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="addStartDate" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="addStartDate" name="start_date">
                        </div>
                        <div class="col-md-6">
                            <label for="addEndDate" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="addEndDate" name="end_date">
                        </div>
                        <div class="col-md-6">
                            <label for="addTeacher" class="form-label">Teacher</label>
                            <select class="form-select" id="addTeacher" name="teacher_id">
                                <option value="">Select Teacher</option>
                                <?php if (!empty($teachers)): ?>
                                    <?php foreach ($teachers as $teacher): ?>
                                        <option value="<?= esc($teacher['id']) ?>"><?= esc($teacher['name']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="addScheduleDay" class="form-label">Day</label>
                            <select class="form-select" id="addScheduleDay">
                                <option value="">Select Day</option>
                                <option value="Mon">Mon</option>
                                <option value="Tue">Tue</option>
                                <option value="Wed">Wed</option>
                                <option value="Thu">Thu</option>
                                <option value="Fri">Fri</option>
                                <option value="Sat">Sat</option>
                                <option value="Sun">Sun</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="addScheduleTime" class="form-label">Time</label>
                            <input type="text" class="form-control" id="addScheduleTime" placeholder="e.g., 10:00 - 11:30 AM">
                            <input type="hidden" id="addSchedule" name="schedule">
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid rgba(247, 127, 0, 0.2);">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Course</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="dashboard-wrapper">
    <div class="card dashboard-hero shadow-sm border-0 mb-4">
        <div class="card-body d-lg-flex justify-content-between align-items-center gap-4">
            <div>
                <p class="text-uppercase text-muted fw-semibold mb-1">Course Management</p>
                <h1 class="fw-bold mb-2">Manage Courses</h1>
                <p class="text-muted mb-0">
                    View, search, and manage course offerings. Update schedules, teachers, and course status.
                </p>
            </div>
            <div class="mt-3 mt-lg-0 d-flex flex-column gap-2">
                <div class="d-flex gap-3">
                    <div class="summary-card text-center">
                        <p class="text-uppercase text-muted small mb-1">Total Courses</p>
                        <h2 class="fw-bold mb-0"><?= esc($totalCourses ?? 0) ?></h2>
                    </div>
                    <div class="summary-card text-center">
                        <p class="text-uppercase text-muted small mb-1">Active Courses</p>
                        <h2 class="fw-bold mb-0 text-success"><?= esc($activeCourses ?? 0) ?></h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card dashboard-card shadow-sm border-0 mb-4">
        <div class="card-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-2">
                <h5 class="mb-0">All Courses</h5>
                <button type="button" class="btn btn-primary btn-sm ms-md-3" data-bs-toggle="modal" data-bs-target="#addCourseModal">
                    Add Course
                </button>
            </div>
            <form class="d-flex gap-2" method="get" action="<?= site_url('admin/courses') ?>">
                <input
                    type="text"
                    class="form-control"
                    name="search"
                    placeholder="Search by title, course code, or teacher"
                    value="<?= esc($search ?? '') ?>"
                    style="min-width: 260px;"
                >
                <button type="submit" class="btn btn-primary">Search</button>
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="coursesTable">
                    <thead>
                        <tr>
                            <th>Course Code</th>
                            <th>Course Title</th>
                            <th>Description</th>
                            <th>School Year</th>
                            <th>Semester</th>
                            <th>Schedule</th>
                            <th>Teacher</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($courses)): ?>
                            <?php foreach ($courses as $course): ?>
                                <tr
                                    data-course-id="<?= esc($course['id']) ?>"
                                    data-course-code="<?= esc($course['code'] ?? '') ?>"
                                    data-course-title="<?= esc($course['title'] ?? '') ?>"
                                    data-course-description="<?= esc($course['description'] ?? '') ?>"
                                    data-course-school-year="<?= esc($course['school_year'] ?? '') ?>"
                                    data-course-semester="<?= esc($course['semester'] ?? '') ?>"
                                    data-course-start-date="<?= esc($course['start_date'] ?? '') ?>"
                                    data-course-end-date="<?= esc($course['end_date'] ?? '') ?>"
                                    data-course-schedule="<?= esc($course['schedule'] ?? '') ?>"
                                    data-course-teacher-id="<?= esc($course['teacher_id'] ?? '') ?>"
                                    data-course-status="<?= esc($course['status'] ?? 'inactive') ?>"
                                >
                                    <td><?= esc($course['code'] ?? '-') ?></td>
                                    <td><?= esc($course['title'] ?? '-') ?></td>
                                    <td class="text-truncate" style="max-width: 260px;">
                                        <?= esc($course['description'] ?? '-') ?>
                                    </td>
                                    <td><?= esc($course['school_year'] ?? '-') ?></td>
                                    <td><?= esc(ucfirst($course['semester'] ?? '-')) ?></td>
                                    <td><?= esc($course['schedule'] ?? '-') ?></td>
                                    <td>
                                        <?php
                                        $teacherName = '-';
                                        if (!empty($teachers)) {
                                            foreach ($teachers as $t) {
                                                if ((int) ($course['teacher_id'] ?? 0) === (int) $t['id']) {
                                                    $teacherName = $t['name'];
                                                    break;
                                                }
                                            }
                                        }
                                        ?>
                                        <?= esc($teacherName) ?>
                                    </td>
                                    <td>
                                        <select class="form-select form-select-sm course-status-select" data-course-id="<?= esc($course['id']) ?>">
                                            <option value="active" <?= ($course['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                                            <option value="inactive" <?= ($course['status'] ?? '') !== 'active' ? 'selected' : '' ?>>Inactive</option>
                                        </select>
                                    </td>
                                    <td class="text-center">
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-primary edit-course-btn"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editCourseModal"
                                        >
                                            Edit Details
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">No courses found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if (isset($pager) && $pager !== null): ?>
                <div class="d-flex justify-content-end mt-3">
                    <?= $pager->links() ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Edit Course Modal -->
<div class="modal fade" id="editCourseModal" tabindex="-1" aria-labelledby="editCourseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="background-color: rgba(0, 48, 73, 0.95); border: 1px solid rgba(247, 127, 0, 0.3); color: #EAE2B7;">
            <div class="modal-header" style="border-bottom: 1px solid rgba(247, 127, 0, 0.2);">
                <h5 class="modal-title" id="editCourseModalLabel">Edit Course Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editCourseForm">
                <input type="hidden" name="id" id="editCourseId">
                <div class="modal-body">
                    <div id="editCourseAlert"></div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="editCourseCode" class="form-label">Course Code</label>
                            <input type="text" class="form-control" id="editCourseCode" name="code" readonly>
                        </div>
                        <div class="col-md-4">
                            <label for="editSchoolYear" class="form-label">School Year</label>
                            <input type="text" class="form-control" id="editSchoolYear" name="school_year" placeholder="e.g., 2024-2025">
                        </div>
                        <div class="col-md-4">
                            <label for="editSemester" class="form-label">Semester</label>
                            <select class="form-select" id="editSemester" name="semester">
                                <option value="">Select Semester</option>
                                <option value="1st">1st Semester</option>
                                <option value="2nd">2nd Semester</option>
                                <option value="summer">Summer</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="editStartDate" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="editStartDate" name="start_date">
                        </div>
                        <div class="col-md-6">
                            <label for="editEndDate" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="editEndDate" name="end_date">
                        </div>
                        <div class="col-md-12">
                            <label for="editCourseTitle" class="form-label">Course Title</label>
                            <input type="text" class="form-control" id="editCourseTitle" name="title">
                        </div>
                        <div class="col-md-12">
                            <label for="editCourseDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="editCourseDescription" name="description" rows="3"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label for="editTeacher" class="form-label">Teacher</label>
                            <select class="form-select" id="editTeacher" name="teacher_id">
                                <option value="">Select Teacher</option>
                                <?php if (!empty($teachers)): ?>
                                    <?php foreach ($teachers as $teacher): ?>
                                        <option value="<?= esc($teacher['id']) ?>"><?= esc($teacher['name']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="editScheduleDay" class="form-label">Day</label>
                            <select class="form-select" id="editScheduleDay">
                                <option value="">Select Day</option>
                                <option value="Mon">Mon</option>
                                <option value="Tue">Tue</option>
                                <option value="Wed">Wed</option>
                                <option value="Thu">Thu</option>
                                <option value="Fri">Fri</option>
                                <option value="Sat">Sat</option>
                                <option value="Sun">Sun</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="editScheduleTime" class="form-label">Time</label>
                            <input type="text" class="form-control" id="editScheduleTime" placeholder="e.g., 10:00 - 11:30 AM">
                            <input type="hidden" id="editSchedule" name="schedule">
                        </div>
                        <div class="col-md-6">
                            <label for="editStatus" class="form-label">Status</label>
                            <select class="form-select" id="editStatus" name="status">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid rgba(247, 127, 0, 0.2);">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    function showCourseNotification(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const icon = type === 'success' ? 'bi-check-circle' : 'bi-exclamation-triangle';
        const notification = $('<div class="alert ' + alertClass + ' alert-dismissible fade show"><i class="bi ' + icon + '"></i> ' + message + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
        $('#course-notification-container').html(notification);
        setTimeout(function() {
            notification.fadeOut(function() { $(this).remove(); });
        }, 4000);
    }

    $('.edit-course-btn').on('click', function() {
        const row = $(this).closest('tr');
        $('#editCourseId').val(row.data('course-id'));
        $('#editCourseCode').val(row.data('course-code'));
        $('#editSchoolYear').val(row.data('course-school-year'));
        $('#editSemester').val(row.data('course-semester'));
        $('#editStartDate').val(row.data('course-start-date'));
        $('#editEndDate').val(row.data('course-end-date'));
        $('#editCourseTitle').val(row.data('course-title'));
        $('#editCourseDescription').val(row.data('course-description'));
        $('#editSchedule').val(row.data('course-schedule'));
        $('#editStatus').val(row.data('course-status'));
        $('#editTeacher').val(row.data('course-teacher-id'));
        $('#editCourseAlert').empty().removeClass('alert alert-success alert-danger').hide();
    });

    $('#editCourseForm').on('submit', function(e) {
        e.preventDefault();

        const form = $(this);
        const alertDiv = $('#editCourseAlert');
        const submitBtn = form.find('button[type="submit"]');

        alertDiv.empty().removeClass('alert alert-success alert-danger').hide();

        const startDate = $('#editStartDate').val();
        const endDate = $('#editEndDate').val();
        if (startDate && endDate && new Date(endDate) < new Date(startDate)) {
            alertDiv.addClass('alert alert-danger').html('<i class="bi bi-exclamation-triangle"></i> End date cannot be earlier than start date.').show();
            return;
        }

        const editDay = $('#editScheduleDay').val();
        const editTime = $('#editScheduleTime').val().trim();
        const combinedEditSchedule = editDay && editTime ? (editDay + ' ' + editTime) : editTime;
        $('#editSchedule').val(combinedEditSchedule);

        submitBtn.prop('disabled', true).text('Updating...');

        $.ajax({
            url: '<?= site_url('admin/courses/update') ?>',
            method: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alertDiv.addClass('alert alert-success').html('<i class="bi bi-check-circle"></i> ' + response.message).show();
                    showCourseNotification('success', response.message || 'Course updated successfully');
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    const message = response.message || 'Failed to update course.';
                    alertDiv.addClass('alert alert-danger').html('<i class="bi bi-exclamation-triangle"></i> ' + message).show();
                    showCourseNotification('danger', message);
                }
            },
            error: function() {
                const message = 'An error occurred while updating the course. Please try again.';
                alertDiv.addClass('alert alert-danger').html('<i class="bi bi-exclamation-triangle"></i> ' + message).show();
                showCourseNotification('danger', message);
            },
            complete: function() {
                submitBtn.prop('disabled', false).text('Update');
            }
        });
    });

    $(document).on('change', '.course-status-select', function() {
        const select = $(this);
        const courseId = select.data('course-id');
        const newStatus = select.val();

        if (!courseId) {
            return;
        }

        const csrfHeaderName = '<?= csrf_header() ?>';
        const csrfToken = $('meta[name="' + csrfHeaderName + '"]').attr('content');
        const csrfName = '<?= csrf_token() ?>';

        if (!csrfToken) {
            showCourseNotification('danger', 'Security token missing. Please refresh the page.');
            return;
        }

        select.prop('disabled', true);

        $.ajax({
            url: '<?= site_url('admin/courses/update-status') ?>',
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                [csrfHeaderName]: csrfToken
            },
            data: {
                id: courseId,
                status: newStatus,
                [csrfName]: csrfToken
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showCourseNotification('success', response.message || 'Course status updated successfully');
                } else {
                    showCourseNotification('danger', response.message || 'Failed to update course status');
                }
            },
            error: function() {
                showCourseNotification('danger', 'An error occurred while updating course status. Please try again.');
            },
            complete: function() {
                select.prop('disabled', false);
            }
        });
    });

    $('#addCourseForm').on('submit', function(e) {
        e.preventDefault();

        const form = $(this);
        const alertDiv = $('#addCourseAlert');
        const submitBtn = form.find('button[type="submit"]');

        alertDiv.empty().removeClass('alert alert-success alert-danger').hide();

        const startDate = $('#addStartDate').val();
        const endDate = $('#addEndDate').val();
        if (startDate && endDate && new Date(endDate) < new Date(startDate)) {
            alertDiv.addClass('alert alert-danger').html('<i class="bi bi-exclamation-triangle"></i> End date cannot be earlier than start date.').show();
            return;
        }

        const addDay = $('#addScheduleDay').val();
        const addTime = $('#addScheduleTime').val().trim();
        const combinedAddSchedule = addDay && addTime ? (addDay + ' ' + addTime) : addTime;
        $('#addSchedule').val(combinedAddSchedule);

        submitBtn.prop('disabled', true).text('Adding...');

        $.ajax({
            url: '<?= site_url('admin/courses/add') ?>',
            method: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alertDiv.addClass('alert alert-success').html('<i class="bi bi-check-circle"></i> ' + response.message).show();
                    showCourseNotification('success', response.message || 'Course added successfully');
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    const message = response.message || 'Failed to add course.';
                    alertDiv.addClass('alert alert-danger').html('<i class="bi bi-exclamation-triangle"></i> ' + message).show();
                    showCourseNotification('danger', message);
                }
            },
            error: function() {
                const message = 'An error occurred while adding the course. Please try again.';
                alertDiv.addClass('alert alert-danger').html('<i class="bi bi-exclamation-triangle"></i> ' + message).show();
                showCourseNotification('danger', message);
            },
            complete: function() {
                submitBtn.prop('disabled', false).text('Add Course');
            }
        });
    });
});
</script>

<style>
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

.dashboard-card {
    background: rgba(0, 48, 73, 0.6);
    border: 1px solid rgba(247, 127, 0, 0.3);
    border-radius: 1.25rem;
    color: #EAE2B7;
}

.dashboard-card .card-header {
    border-bottom: 1px solid rgba(247, 127, 0, 0.2);
    background: transparent;
}

.summary-card {
    min-width: 140px;
    padding: 0.75rem 1rem;
    border-radius: 0.85rem;
    background-color: rgba(0, 0, 0, 0.2);
    border: 1px solid rgba(247, 127, 0, 0.25);
}

.table {
    color: #EAE2B7;
}

.table thead th {
    border-bottom: 2px solid rgba(247, 127, 0, 0.3);
    color: #FCBF49;
    font-weight: 600;
}

.table tbody tr {
    border-bottom: 1px solid rgba(247, 127, 0, 0.1);
}

.table tbody tr:hover {
    background-color: rgba(247, 127, 0, 0.1);
}

.form-select, .form-control {
    background-color: rgba(234, 226, 183, 0.1);
    border: 1px solid rgba(247, 127, 0, 0.3);
    color: #EAE2B7;
}

.form-select:focus, .form-control:focus {
    background-color: rgba(234, 226, 183, 0.15);
    border-color: #F77F00;
    box-shadow: 0 0 0 0.2rem rgba(247, 127, 0, 0.25);
    color: #EAE2B7;
}

.modal-content {
    border-radius: 1rem;
}

.btn-primary {
    background-color: #F77F00;
    border-color: #F77F00;
    color: #003049;
}

.btn-primary:hover {
    background-color: #FCBF49;
    border-color: #FCBF49;
    color: #003049;
}

.btn-outline-primary {
    border-color: #F77F00;
    color: #F77F00;
}

.btn-outline-primary:hover {
    background-color: #F77F00;
    border-color: #F77F00;
    color: #003049;
}
</style>

<?= $this->endSection() ?>
