<?= $this->extend('template') ?>

<?= $this->section('content') ?>

<?= csrf_meta() ?>

<div id="notification-container" class="position-fixed top-0 start-50 translate-middle-x mt-3" style="z-index: 9999; min-width: 300px;"></div>

<div class="dashboard-wrapper">
    <div class="card dashboard-hero shadow-sm border-0 mb-4">
        <div class="card-body d-lg-flex justify-content-between align-items-center gap-4">
            <div>
                <p class="text-uppercase text-muted fw-semibold mb-1">User Management</p>
                <h1 class="fw-bold mb-2">Manage Users 👥</h1>
                <p class="text-muted mb-0">
                    Add, edit, and manage user accounts. Control user roles and account status.
                </p>
            </div>
            <div class="mt-3 mt-lg-0">
                <button type="button" class="btn btn-primary btn-lg px-4" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i class="bi bi-person-plus"></i> Add User
                </button>
            </div>
        </div>
    </div>

    <div class="card dashboard-card shadow-sm border-0">
        <div class="card-header">
            <h5 class="mb-0">All Users</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="usersTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Full Name</th>
                            <th>Email/Username</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($users)): ?>
                            <?php foreach ($users as $user): ?>
                                <tr data-user-id="<?= esc($user['id']) ?>" class="<?= isset($user['status']) && $user['status'] === 'inactive' ? 'table-secondary opacity-75' : '' ?>">
                                    <td><?= esc($user['id']) ?></td>
                                    <td>
                                        <?= esc($user['name']) ?>
                                        <?php if (isset($user['is_protected']) && $user['is_protected'] == 1): ?>
                                            <span class="badge bg-warning text-dark ms-2" title="Protected Admin Account">
                                                <i class="bi bi-shield-lock"></i> Protected
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= esc($user['email']) ?></td>
                                    <td>
                                        <?php if (isset($user['is_protected']) && $user['is_protected'] == 1 && $user['id'] == $protectedAdminId): ?>
                                            <span class="badge bg-primary"><?= ucfirst(esc($user['role'])) ?></span>
                                        <?php else: ?>
                                            <select class="form-select form-select-sm role-select" 
                                                    data-user-id="<?= esc($user['id']) ?>" 
                                                    style="max-width: 140px;">
                                                <option value="student" <?= $user['role'] === 'student' ? 'selected' : '' ?>>Student</option>
                                                <option value="teacher" <?= $user['role'] === 'teacher' ? 'selected' : '' ?>>Teacher</option>
                                                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                            </select>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge <?= (isset($user['status']) && $user['status'] === 'active') ? 'bg-success' : 'bg-secondary' ?>">
                                            <?= isset($user['status']) ? ucfirst(esc($user['status'])) : 'Active' ?>
                                        </span>
                                    </td>
                                    <td><?= date('M j, Y', strtotime($user['created_at'])) ?></td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <?php if ($user['role'] === 'admin'): ?>
                                                <button type="button" class="btn btn-sm btn-outline-secondary" disabled>
                                                    <i class="bi bi-shield-lock"></i> Protected
                                                </button>
                                            <?php else: ?>
                                                <button type="button" class="btn btn-sm <?= (isset($user['status']) && $user['status'] === 'active') ? 'btn-outline-danger' : 'btn-outline-success' ?> toggle-status-btn" 
                                                        data-user-id="<?= esc($user['id']) ?>"
                                                        data-current-status="<?= isset($user['status']) ? esc($user['status']) : 'active' ?>">
                                                    <i class="bi <?= (isset($user['status']) && $user['status'] === 'active') ? 'bi-person-x' : 'bi-person-check' ?>"></i>
                                                    <?= (isset($user['status']) && $user['status'] === 'active') ? 'Deactivate' : 'Activate' ?>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">No users found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background-color: rgba(0, 48, 73, 0.95); border: 1px solid rgba(247, 127, 0, 0.3); color: #EAE2B7;">
            <div class="modal-header" style="border-bottom: 1px solid rgba(247, 127, 0, 0.2);">
                <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addUserForm">
                <div class="modal-body">
                    <div id="addUserAlert"></div>
                    <div class="mb-3">
                        <label for="addUserName" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="addUserName" name="name" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="addUserEmail" class="form-label">Email/Username</label>
                        <input type="email" class="form-control" id="addUserEmail" name="email" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="addUserPassword" class="form-label">Password</label>
                        <input type="password" class="form-control" id="addUserPassword" name="password" required>
                        <small class="text-muted">Must be at least 8 characters with uppercase, lowercase, and number</small>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="addUserRole" class="form-label">Role</label>
                        <select class="form-select" id="addUserRole" name="role" required>
                            <option value="">Select Role</option>
                            <option value="student">Student</option>
                            <option value="teacher">Teacher</option>
                            <option value="admin">Admin</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid rgba(247, 127, 0, 0.2);">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Change Password Modal (for protected admin) -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background-color: rgba(0, 48, 73, 0.95); border: 1px solid rgba(247, 127, 0, 0.3); color: #EAE2B7;">
            <div class="modal-header" style="border-bottom: 1px solid rgba(247, 127, 0, 0.2);">
                <h5 class="modal-title" id="changePasswordModalLabel">Change Password</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="changePasswordForm">
                <input type="hidden" id="changePasswordUserId" name="user_id">
                <div class="modal-body">
                    <div id="changePasswordAlert"></div>
                    <div class="mb-3">
                        <label for="changePasswordNew" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="changePasswordNew" name="password" required>
                        <small class="text-muted">Must be at least 8 characters with uppercase, lowercase, and number</small>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="changePasswordConfirm" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="changePasswordConfirm" name="password_confirm" required>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid rgba(247, 127, 0, 0.2);">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Change Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Setup CSRF token for AJAX requests
    $.ajaxSetup({
        beforeSend: function(xhr, settings) {
            if (settings.type === 'POST' || settings.type === 'PUT' || settings.type === 'DELETE') {
                const token = $('meta[name="<?= csrf_header() ?>"]').attr('content');
                if (token) {
                    xhr.setRequestHeader('<?= csrf_header() ?>', token);
                }
            }
        }
    });
    
    // Add User Form Submission
    $('#addUserForm').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const alertDiv = $('#addUserAlert');
        const submitBtn = form.find('button[type="submit"]');
        
        // Clear previous alerts and validation
        alertDiv.empty().removeClass('alert alert-success alert-danger').hide();
        form.find('.is-invalid').removeClass('is-invalid');
        form.find('.invalid-feedback').text('');
        
        submitBtn.prop('disabled', true).text('Adding...');
        
        $.ajax({
            url: '<?= site_url('admin/users/add') ?>',
            method: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alertDiv.addClass('alert alert-success').html('<i class="bi bi-check-circle"></i> ' + response.message).show();
                    form[0].reset();
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    alertDiv.addClass('alert alert-danger').html('<i class="bi bi-exclamation-triangle"></i> ' + response.message).show();
                    if (response.errors) {
                        $.each(response.errors, function(field, message) {
                            const input = form.find('[name="' + field + '"]');
                            input.addClass('is-invalid');
                            input.siblings('.invalid-feedback').text(Array.isArray(message) ? message[0] : message);
                        });
                    }
                }
            },
            error: function(xhr) {
                alertDiv.addClass('alert alert-danger').html('<i class="bi bi-exclamation-triangle"></i> An error occurred. Please try again.').show();
            },
            complete: function() {
                submitBtn.prop('disabled', false).text('Add User');
            }
        });
    });

    // Role Change - Store original values and bind event
    $('.role-select').each(function() {
        $(this).data('original-value', $(this).val());
    });
    
    // Use both direct and delegated binding to ensure it works
    $(document).off('change', '.role-select').on('change', '.role-select', function() {
        const select = $(this);
        const userId = select.data('user-id');
        const newRole = select.val();
        const originalRole = select.data('original-value');
        
        if (!userId) {
            console.error('No user ID found on select element');
            return;
        }
        
        console.log('Role change triggered:', {userId, newRole, originalRole});
        
        // Check if this is protected admin
        const row = select.closest('tr');
        if (row.find('.badge.bg-warning').length > 0) {
            alert('Cannot change role of protected admin account');
            select.val(originalRole);
            return;
        }
        
        // Disable select while processing
        select.prop('disabled', true);
        
        // Get CSRF token
        const csrfHeaderName = '<?= csrf_header() ?>';
        const csrfToken = $('meta[name="' + csrfHeaderName + '"]').attr('content');
        const csrfName = '<?= csrf_token() ?>';
        
        if (!csrfToken) {
            console.error('CSRF token not found');
            alert('Security token missing. Please refresh the page.');
            select.val(originalRole);
            select.prop('disabled', false);
            return;
        }
        
        console.log('Sending AJAX request:', {url: '<?= site_url('admin/users/update-role') ?>', userId, newRole, csrfToken: csrfToken ? 'present' : 'missing'});
        
        $.ajax({
            url: '<?= site_url('admin/users/update-role') ?>',
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                [csrfHeaderName]: csrfToken
            },
            data: {
                user_id: userId,
                role: newRole,
                [csrfName]: csrfToken
            },
            dataType: 'json',
            success: function(response) {
                console.log('Role update response:', response);
                if (response.success) {
                    // Update the original value
                    select.data('original-value', newRole);
                    // Show temporary success indicator
                    select.css('border-color', '#28a745');
                    setTimeout(function() {
                        select.css('border-color', '');
                    }, 2000);
                    // Show success message
                    showNotification('success', 'Role updated successfully');
                } else {
                    showNotification('danger', response.message || 'Failed to update role');
                    select.val(originalRole);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', {xhr, status, error, responseText: xhr.responseText});
                let errorMsg = 'An error occurred. Please try again.';
                try {
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    } else if (xhr.responseText) {
                        const response = JSON.parse(xhr.responseText);
                        if (response.message) {
                            errorMsg = response.message;
                        }
                    }
                } catch (e) {
                    console.error('Error parsing response:', e);
                }
                alert(errorMsg);
                select.val(originalRole);
            },
            complete: function() {
                select.prop('disabled', false);
            }
        });
    });

    // Toggle Status (Activate/Deactivate) - Use delegated binding for dynamically loaded content
    $(document).on('click', '.toggle-status-btn', function() {
        const btn = $(this);
        const userId = btn.data('user-id');
        const currentStatus = btn.data('current-status');
        
        if (!confirm('Are you sure you want to ' + (currentStatus === 'active' ? 'deactivate' : 'activate') + ' this user?')) {
            return;
        }
        
        btn.prop('disabled', true);
        
        $.ajax({
            url: '<?= site_url('admin/users/toggle-status') ?>',
            method: 'POST',
            data: {
                user_id: userId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Show success message
                    showNotification('success', response.message || 'User status updated successfully');
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    showNotification('danger', response.message || 'Failed to update status');
                    btn.prop('disabled', false);
                }
            },
            error: function(xhr) {
                let errorMsg = 'An error occurred. Please try again.';
                try {
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                } catch (e) {}
                showNotification('danger', errorMsg);
                btn.prop('disabled', false);
            }
        });
    });
    
    // Notification helper function
    function showNotification(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const icon = type === 'success' ? 'bi-check-circle' : 'bi-exclamation-triangle';
        const notification = $('<div class="alert ' + alertClass + ' alert-dismissible fade show"><i class="bi ' + icon + '"></i> ' + message + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
        $('#notification-container').html(notification);
        setTimeout(function() {
            notification.fadeOut(function() {
                $(this).remove();
            });
        }, 4000);
    }

    // Change Password Modal
    $('#changePasswordModal').on('show.bs.modal', function(event) {
        const button = $(event.relatedTarget);
        const userId = button.data('user-id');
        $('#changePasswordUserId').val(userId);
        $('#changePasswordForm')[0].reset();
        $('#changePasswordAlert').empty().removeClass('alert alert-success alert-danger').hide();
    });

    // Change Password Form Submission
    $('#changePasswordForm').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const alertDiv = $('#changePasswordAlert');
        const submitBtn = form.find('button[type="submit"]');
        const password = $('#changePasswordNew').val();
        const passwordConfirm = $('#changePasswordConfirm').val();
        
        // Clear previous alerts
        alertDiv.empty().removeClass('alert alert-success alert-danger').hide();
        form.find('.is-invalid').removeClass('is-invalid');
        
        // Validate passwords match
        if (password !== passwordConfirm) {
            alertDiv.addClass('alert alert-danger').html('<i class="bi bi-exclamation-triangle"></i> Passwords do not match').show();
            $('#changePasswordConfirm').addClass('is-invalid');
            return;
        }
        
        // Validate password strength
        if (password.length < 8 || !/(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/.test(password)) {
            alertDiv.addClass('alert alert-danger').html('<i class="bi bi-exclamation-triangle"></i> Password must be at least 8 characters with uppercase, lowercase, and number').show();
            $('#changePasswordNew').addClass('is-invalid');
            return;
        }
        
        submitBtn.prop('disabled', true).text('Changing...');
        
        $.ajax({
            url: '<?= site_url('admin/users/change-password') ?>',
            method: 'POST',
            data: {
                user_id: $('#changePasswordUserId').val(),
                password: password
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alertDiv.addClass('alert alert-success').html('<i class="bi bi-check-circle"></i> ' + response.message).show();
                    setTimeout(function() {
                        $('#changePasswordModal').modal('hide');
                        form[0].reset();
                    }, 1500);
                } else {
                    alertDiv.addClass('alert alert-danger').html('<i class="bi bi-exclamation-triangle"></i> ' + response.message).show();
                }
            },
            error: function() {
                alertDiv.addClass('alert alert-danger').html('<i class="bi bi-exclamation-triangle"></i> An error occurred. Please try again.').show();
            },
            complete: function() {
                submitBtn.prop('disabled', false).text('Change Password');
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

.badge.bg-success {
    background-color: rgba(40, 167, 69, 0.8) !important;
}

.badge.bg-secondary {
    background-color: rgba(108, 117, 125, 0.8) !important;
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

.btn-outline-danger {
    border-color: #D62828;
    color: #D62828;
}

.btn-outline-danger:hover {
    background-color: #D62828;
    border-color: #D62828;
    color: #fff;
}

.btn-outline-success {
    border-color: #28a745;
    color: #28a745;
}

.btn-outline-success:hover {
    background-color: #28a745;
    border-color: #28a745;
    color: #fff;
}
</style>

<?= $this->endSection() ?>

