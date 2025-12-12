<?= $this->extend('template') ?>

<?= $this->section('content') ?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><?= $title ?? 'Enrollment Requests' ?></h4>
                </div>
                <div class="card-body">
                    <?php if (empty($enrollments)): ?>
                        <div class="alert alert-info mb-0">
                            No pending enrollment requests.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Student</th>
                                        <th>Email</th>
                                        <th>Course</th>
                                        <th>Requested On</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($enrollments as $enrollment): ?>
                                        <tr id="enrollment-<?= $enrollment['id'] ?>">
                                            <td><?= esc($enrollment['student_name']) ?></td>
                                            <td><?= esc($enrollment['student_email']) ?></td>
                                            <td><?= esc($enrollment['course_title']) ?></td>
                                            <td><?= date('M j, Y g:i A', strtotime($enrollment['enrolled_at'])) ?></td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button type="button" 
                                                            class="btn btn-success btn-sm approve-btn" 
                                                            data-enrollment-id="<?= $enrollment['id'] ?>"
                                                            data-course-title="<?= esc($enrollment['course_title']) ?>">
                                                        <i class="bi bi-check-lg"></i> Approve
                                                    </button>
                                                    <button type="button" 
                                                            class="btn btn-danger btn-sm reject-btn"
                                                            data-enrollment-id="<?= $enrollment['id'] ?>"
                                                            data-course-title="<?= esc($enrollment['course_title']) ?>">
                                                        <i class="bi bi-x-lg"></i> Reject
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel">Confirm Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="confirmModalBody">
                <!-- Content will be inserted here by JavaScript -->
            </div>
            <div class="modal-body pt-0" id="rejectReasonContainer" style="display:none;">
                <label for="rejectReason" class="form-label">Reason for rejection</label>
                <textarea class="form-control" id="rejectReason" rows="3" placeholder="Enter reason..." required></textarea>
                <div class="invalid-feedback">Reject reason is required.</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmActionBtn">Confirm</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    let currentEnrollmentId = null;
    let currentAction = '';
    let currentCourseTitle = '';
    const rejectContainer = $('#rejectReasonContainer');
    const rejectTextarea = $('#rejectReason');
    const confirmModalEl = document.getElementById('confirmModal');
    const confirmModal = confirmModalEl ? new bootstrap.Modal(confirmModalEl) : null;
    
    // Handle approve button click
    $('.approve-btn').on('click', function() {
        currentEnrollmentId = $(this).data('enrollment-id');
        currentCourseTitle = $(this).data('course-title');
        currentAction = 'approved';

        rejectTextarea.val('').removeClass('is-invalid');
        rejectContainer.hide();
        
        $('#confirmModalBody').html(`
            <p>Are you sure you want to approve the enrollment request for <strong>${$(this).closest('tr').find('td:first').text()}</strong> 
            in <strong>${currentCourseTitle}</strong>?</p>
        `);
        
        $('#confirmActionBtn')
            .removeClass('btn-danger')
            .addClass('btn-success')
            .html('<i class="bi bi-check-lg"></i> Approve');

        if (confirmModal) {
            confirmModal.show();
        }
    });
    
    // Handle reject button click
    $('.reject-btn').on('click', function() {
        currentEnrollmentId = $(this).data('enrollment-id');
        currentCourseTitle = $(this).data('course-title');
        currentAction = 'rejected';

        rejectTextarea.val('').removeClass('is-invalid');
        rejectContainer.show();
        
        $('#confirmModalBody').html(`
            <p>Are you sure you want to reject the enrollment request for <strong>${$(this).closest('tr').find('td:first').text()}</strong> 
            in <strong>${currentCourseTitle}</strong>?</p>
        `);
        
        $('#confirmActionBtn')
            .removeClass('btn-success')
            .addClass('btn-danger')
            .html('<i class="bi bi-x-lg"></i> Reject');

        if (confirmModal) {
            confirmModal.show();
        }
    });
    
    // Handle confirm action
    $('#confirmActionBtn').on('click', function() {
        if (!currentEnrollmentId || !currentAction) return;

        let rejectReason = '';
        if (currentAction === 'rejected') {
            rejectReason = (rejectTextarea.val() || '').trim();
            if (rejectReason === '') {
                rejectTextarea.addClass('is-invalid');
                rejectTextarea.focus();
                return;
            }
            rejectTextarea.removeClass('is-invalid');
        }
        
        const $btn = $(this);
        $btn.prop('disabled', true).html(`
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            Processing...
        `);
        
        $.ajax({
            url: `<?= site_url('teacher/enrollments/update/') ?>${currentEnrollmentId}/${currentAction}`,
            method: 'POST',
            data: {
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>',
                reject_reason: rejectReason
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Remove the row from the table
                    $(`#enrollment-${currentEnrollmentId}`).fadeOut(400, function() {
                        $(this).remove();
                        // If no more enrollments, show message
                        if ($('tbody tr').length === 0) {
                            $('tbody').html(`
                                <tr>
                                    <td colspan="5" class="text-center">
                                        <div class="alert alert-info mb-0">No pending enrollment requests.</div>
                                    </td>
                                </tr>
                            `);
                        }
                    });
                    
                    // Show success message
                    showToast('success', response.message);
                } else {
                    showToast('danger', response.message || 'An error occurred. Please try again.');
                }

                if (confirmModal) {
                    confirmModal.hide();
                }
            },
            error: function() {
                showToast('danger', 'An error occurred. Please try again.');

                if (confirmModal) {
                    confirmModal.hide();
                }
            },
            complete: function() {
                $btn.prop('disabled', false).html('Confirm');
            }
        });
    });
    
    // Toast notification function
    function showToast(type, message) {
        const toast = `
            <div class="toast align-items-center text-white bg-${type} border-0 position-fixed bottom-0 end-0 m-3" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;

        const $toast = $(toast).appendTo('body');
        const toastEl = $toast.get(0);
        const toastObj = toastEl ? new bootstrap.Toast(toastEl, { autohide: true, delay: 5000 }) : null;
        if (toastObj) {
            toastObj.show();
        }

        if (toastEl) {
            toastEl.addEventListener('hidden.bs.toast', function() {
                $toast.remove();
            });
        }
    }
});
</script>
<?= $this->endSection() ?>
