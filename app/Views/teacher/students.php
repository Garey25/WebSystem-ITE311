<?= $this->extend('template') ?>

<?= $this->section('content') ?>

<div class="dashboard-wrapper">
    <div class="card dashboard-hero shadow-sm border-0 mb-4">
        <div class="card-body d-lg-flex justify-content-between align-items-center gap-4">
            <div>
                <p class="text-uppercase text-muted fw-semibold mb-1">Teacher Dashboard</p>
                <h1 class="fw-bold mb-2">Manage Students</h1>
                <p class="text-muted mb-0">
                    Monitor and manage students enrolled in your course.
                </p>
            </div>
            <div class="mt-3 mt-lg-0 text-lg-end">
                <p class="mb-1 text-muted text-uppercase small">Course</p>
                <?php if (!empty($courses)): ?>
                    <select id="courseSelect" class="form-select form-select-sm" style="min-width: 260px;">
                        <?php foreach ($courses as $course): ?>
                            <?php
                                $label = trim(($course['code'] ?? '') . ' - ' . ($course['title'] ?? ''));
                                if ($label === '-' || $label === '') {
                                    $label = $course['title'] ?? 'Course';
                                }
                            ?>
                            <option value="<?= esc($course['id']) ?>" <?= (int)($selectedCourseId ?? 0) === (int)$course['id'] ? 'selected' : '' ?>>
                                <?= esc($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                <?php else: ?>
                    <h5 class="mb-0">No courses available</h5>
                <?php endif; ?>
            </div>
        </div>
    </div>

<!-- Enroll Student Modal -->
<div class="modal fade" id="enrollStudentModal" tabindex="-1" aria-labelledby="enrollStudentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background-color: rgba(0, 48, 73, 0.95); border: 1px solid rgba(247, 127, 0, 0.3); color: #EAE2B7;">
            <div class="modal-header" style="border-bottom: 1px solid rgba(247, 127, 0, 0.2);">
                <h5 class="modal-title" id="enrollStudentModalLabel">Enroll Student</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="enrollStudentForm">
                <div class="modal-body">
                    <div id="enrollStudentAlert" class="mb-2"></div>
                    <div class="mb-3">
                        <label for="enrollStudentSelect" class="form-label">Select Student</label>
                        <select class="form-select" id="enrollStudentSelect" name="student_id" required>
                            <option value="">Choose a student</option>
                            <?php if (!empty($allStudents)): ?>
                                <?php foreach ($allStudents as $student): ?>
                                    <option value="<?= esc($student['id']) ?>">
                                        <?= esc($student['name']) ?> (<?= esc($student['email']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid rgba(247, 127, 0, 0.2);">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Enroll</button>
                </div>
            </form>
        </div>
    </div>
</div>

    <div class="card dashboard-card shadow-sm border-0 mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Search & Filters</h5>
            <?php if (!empty($allStudents) && !empty($courses)): ?>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#enrollStudentModal">
                    Enroll Student
                </button>
            <?php endif; ?>
        </div>
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Search</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="studentSearch" placeholder="Search by name, ID, or email">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Year Level</label>
                    <select class="form-select" id="filterYearLevel">
                        <option value="">All</option>
                        <option value="1">1st Year</option>
                        <option value="2">2nd Year</option>
                        <option value="3">3rd Year</option>
                        <option value="4">4th Year</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select class="form-select" id="filterStatus">
                        <option value="">All</option>
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                        <option value="Dropped">Dropped</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Program</label>
                    <select class="form-select" id="filterProgram">
                        <option value="">All</option>
                        <option value="BSCS">BSCS</option>
                        <option value="BSIT">BSIT</option>
                        <option value="BSIS">BSIS</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="card dashboard-card shadow-sm border-0">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Enrolled Students</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="studentsTable">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Program</th>
                            <th>Year Level</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($students)): ?>
                            <?php foreach ($students as $student): ?>
                                <tr
                                    data-student-id="<?= esc($student['student_id']) ?>"
                                    data-student-name="<?= esc($student['name']) ?>"
                                    data-student-email="<?= esc($student['email']) ?>"
                                    data-student-program="<?= esc($student['program']) ?>"
                                    data-student-year="<?= esc($student['year_level']) ?>"
                                    data-student-section="<?= esc($student['section'] ?? '') ?>"
                                    data-student-enrolled="<?= esc($student['enrolled_at'] ?? '') ?>"
                                    data-student-status="<?= esc($student['status']) ?>"
                                >
                                    <td><?= esc($student['student_id']) ?></td>
                                    <td><?= esc($student['name']) ?></td>
                                    <td><?= esc($student['email']) ?></td>
                                    <td><?= esc($student['program']) ?></td>
                                    <td><?= esc($student['year_level']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $student['status'] === 'Active' ? 'success' : ($student['status'] === 'Dropped' ? 'danger' : 'secondary') ?>">
                                            <?= esc($student['status']) ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary view-details-btn" data-bs-toggle="modal" data-bs-target="#studentDetailsModal">Details</button>
                                            <button type="button" class="btn btn-sm btn-outline-warning update-status-btn" data-bs-toggle="modal" data-bs-target="#updateStatusModal">Status</button>
                                            <button type="button" class="btn btn-sm btn-outline-danger remove-student-btn">Remove</button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">No students enrolled yet.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Student Details Modal -->
<div class="modal fade" id="studentDetailsModal" tabindex="-1" aria-labelledby="studentDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background-color: rgba(0, 48, 73, 0.95); border: 1px solid rgba(247, 127, 0, 0.3); color: #EAE2B7;">
            <div class="modal-header" style="border-bottom: 1px solid rgba(247, 127, 0, 0.2);">
                <h5 class="modal-title" id="studentDetailsModalLabel">Student Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">Student ID</dt>
                    <dd class="col-sm-8" id="detailStudentId"></dd>

                    <dt class="col-sm-4">Full Name</dt>
                    <dd class="col-sm-8" id="detailStudentName"></dd>

                    <dt class="col-sm-4">Email</dt>
                    <dd class="col-sm-8" id="detailStudentEmail"></dd>

                    <dt class="col-sm-4">Program / Major</dt>
                    <dd class="col-sm-8" id="detailStudentProgram"></dd>

                    <dt class="col-sm-4">Year Level</dt>
                    <dd class="col-sm-8" id="detailStudentYear"></dd>

                    <dt class="col-sm-4">Section</dt>
                    <dd class="col-sm-8" id="detailStudentSection"></dd>

                    <dt class="col-sm-4">Enrollment Date</dt>
                    <dd class="col-sm-8" id="detailStudentEnrolled"></dd>

                    <dt class="col-sm-4">Status</dt>
                    <dd class="col-sm-8" id="detailStudentStatus"></dd>
                </dl>
            </div>
            <div class="modal-footer" style="border-top: 1px solid rgba(247, 127, 0, 0.2);">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="updateStatusModal" tabindex="-1" aria-labelledby="updateStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background-color: rgba(0, 48, 73, 0.95); border: 1px solid rgba(247, 127, 0, 0.3); color: #EAE2B7;">
            <div class="modal-header" style="border-bottom: 1px solid rgba(247, 127, 0, 0.2);">
                <h5 class="modal-title" id="updateStatusModalLabel">Update Student Status</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="updateStatusForm">
                <input type="hidden" id="statusStudentId" name="student_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Current Status</label>
                        <input type="text" class="form-control" id="currentStatus" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="newStatus" class="form-label">New Status</label>
                        <select class="form-select" id="newStatus" name="new_status" required>
                            <option value="">Select Status</option>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                            <option value="Dropped">Dropped</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="statusRemarks" class="form-label">Remarks</label>
                        <textarea class="form-control" id="statusRemarks" name="remarks" rows="3" placeholder="Add remarks about this status change..."></textarea>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid rgba(247, 127, 0, 0.2);">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    function applyFilters() {
        const search = $('#studentSearch').val().toLowerCase();
        const year = $('#filterYearLevel').val();
        const status = $('#filterStatus').val();
        const program = $('#filterProgram').val();

        $('#studentsTable tbody tr').each(function() {
            const row = $(this);
            const name = (row.data('student-name') || '').toString().toLowerCase();
            const id = (row.data('student-id') || '').toString().toLowerCase();
            const email = (row.data('student-email') || '').toString().toLowerCase();
            const rowYear = (row.data('student-year') || '').toString();
            const rowStatus = (row.data('student-status') || '').toString();
            const rowProgram = (row.data('student-program') || '').toString();

            const matchesSearch = !search || name.includes(search) || id.includes(search) || email.includes(search);
            const matchesYear = !year || rowYear === year;
            const matchesStatus = !status || rowStatus === status;
            const matchesProgram = !program || rowProgram === program;

            row.toggle(matchesSearch && matchesYear && matchesStatus && matchesProgram);
        });
    }

    $('#studentSearch, #filterYearLevel, #filterStatus, #filterProgram').on('input change', applyFilters);

    $('.view-details-btn').on('click', function() {
        const row = $(this).closest('tr');
        $('#detailStudentId').text(row.data('student-id'));
        $('#detailStudentName').text(row.data('student-name'));
        $('#detailStudentEmail').text(row.data('student-email'));
        $('#detailStudentProgram').text(row.data('student-program'));
        $('#detailStudentYear').text(row.data('student-year'));
        $('#detailStudentSection').text(row.data('student-section') || '');
        $('#detailStudentEnrolled').text(row.data('student-enrolled') || '');
        $('#detailStudentStatus').text(row.data('student-status'));
    });

    $('.update-status-btn').on('click', function() {
        const row = $(this).closest('tr');
        $('#statusStudentId').val(row.data('student-id'));
        $('#currentStatus').val(row.data('student-status'));
        $('#newStatus').val('');
        $('#statusRemarks').val('');
    });

    $('#updateStatusForm').on('submit', function(e) {
        e.preventDefault();
        // Placeholder: Just close modal for now
        $('#updateStatusModal').modal('hide');
    });

    $('.remove-student-btn').on('click', function() {
        if (!confirm('Remove this student from the course?')) {
            return;
        }
        // Placeholder: Just hide the row for now
        $(this).closest('tr').fadeOut(function() { $(this).remove(); });
    });

    $('#enrollStudentForm').on('submit', function(e) {
        e.preventDefault();

        const alertDiv = $('#enrollStudentAlert');
        const studentId = $('#enrollStudentSelect').val();
        const courseId = $('#courseSelect').val();

        alertDiv.removeClass('alert alert-success alert-danger').empty();

        if (!studentId || !courseId) {
            alertDiv.addClass('alert alert-danger').text('Please select a student and course.');
            return;
        }

        $.ajax({
            url: '<?= site_url('teacher/students/enroll') ?>',
            method: 'POST',
            data: {
                student_id: studentId,
                course_id: courseId,
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alertDiv.addClass('alert alert-success').text(response.message || 'Student enrolled successfully.');
                    setTimeout(function() { location.reload(); }, 1200);
                } else {
                    alertDiv.addClass('alert alert-danger').text(response.message || 'Failed to enroll student.');
                }
            },
            error: function() {
                alertDiv.addClass('alert alert-danger').text('An error occurred. Please try again.');
            }
        });
    });

    $('#courseSelect').on('change', function() {
        const courseId = $(this).val();
        const baseUrl = '<?= site_url('teacher/students') ?>';
        window.location.href = baseUrl + (courseId ? ('?course_id=' + encodeURIComponent(courseId)) : '');
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

.btn-outline-warning {
    border-color: #FFC107;
    color: #FFC107;
}

.btn-outline-warning:hover {
    background-color: #FFC107;
    border-color: #FFC107;
    color: #003049;
}
</style>

<?= $this->endSection() ?>
