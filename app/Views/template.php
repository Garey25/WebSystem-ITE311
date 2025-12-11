<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>WebSystem</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        <?php if (uri_string() != 'login' && uri_string() != 'register'): ?>
        body {
            background-color: #003049;
            color: #EAE2B7;
        }
        <?php endif; ?>
        .navbar {
            background-color: #003049;
            padding: 1rem 2rem;
            border-bottom: 2px solid #F77F00;
        }
        .navbar-brand {
            font-weight: 600;
            color: #FCBF49 !important;
            font-size: 1.5rem;
        }
        .nav-link {
            color: #EAE2B7 !important;
            font-weight: 500;
            margin-right: 1rem;
            transition: color 0.3s;
        }
        .nav-link:hover,
        .nav-link.active {
            color: #FCBF49 !important;
        }
        .container {
            background-color: rgba(0, 48, 73, 0.8);
            border-radius: 12px;
            padding: 30px;
            margin-top: 30px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
            border: 1px solid rgba(247, 127, 0, 0.2);
        }
        .btn-primary {
            background-color: #F77F00;
            border-color: #F77F00;
            font-weight: 500;
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
        .form-control,
        .form-select {
            background-color: rgba(234, 226, 183, 0.1);
            border: 1px solid rgba(247, 127, 0, 0.3);
            color: #EAE2B7;
        }
        .form-control:focus {
            background-color: rgba(234, 226, 183, 0.15);
            color: #EAE2B7;
            border-color: #F77F00;
            box-shadow: 0 0 0 0.2rem rgba(247, 127, 0, 0.25);
        }
        a {
            color: #FCBF49;
        }
        a:hover {
            color: #F77F00;
        }
        .alert-success {
            background-color: rgba(252, 191, 73, 0.2);
            border-color: #FCBF49;
            color: #EAE2B7;
        }
        .alert-danger {
            background-color: rgba(214, 40, 40, 0.2);
            border-color: #D62828;
            color: #EAE2B7;
        }
        .alert-info {
            background-color: rgba(247, 127, 0, 0.2);
            border-color: #F77F00;
            color: #EAE2B7;
        }
        .badge.bg-danger {
            background-color: #D62828 !important;
        }
    </style>
</head>
<body>
<?php $isLoggedIn = session('isLoggedIn') ?? false; $role = session('role') ?? null; ?>
<?php if (uri_string() != 'login' && uri_string() != 'register'): ?>
<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?= site_url('/') ?>">WebSystem</a>
        <button class="navbar-toggler bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link <?= uri_string() == '' ? 'active' : '' ?>" href="<?= site_url('/') ?>">Home</a></li>
                <li class="nav-item"><a class="nav-link <?= uri_string() == 'about' ? 'active' : '' ?>" href="<?= site_url('about') ?>">About</a></li>
                <li class="nav-item"><a class="nav-link <?= uri_string() == 'contact' ? 'active' : '' ?>" href="<?= site_url('contact') ?>">Contact</a></li>
                <?php if ($isLoggedIn): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Notifications
                            <span class="badge bg-danger" id="notification-badge" style="display:none;">0</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" id="notification-list">
                            <li><a class="dropdown-item" href="#">No notifications</a></li>
                        </ul>
                    </li>
                    <li class="nav-item"><a class="nav-link <?= uri_string() == 'dashboard' ? 'active' : '' ?>" href="<?= site_url('dashboard') ?>">Dashboard</a></li>
                    <?php if ($role === 'admin'): ?>
                        <li class="nav-item"><a class="nav-link <?= uri_string() == 'admin/users' ? 'active' : '' ?>" href="<?= site_url('admin/users') ?>">Users</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">Courses</a></li>
                    <?php elseif ($role === 'teacher'): ?>
                        <li class="nav-item"><a class="nav-link" href="#">My Courses</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">Quizzes</a></li>
                    <?php elseif ($role === 'student'): ?>
                        <li class="nav-item"><a class="nav-link" href="#">My Enrollments</a></li>
                    <?php endif; ?>
                    <li class="nav-item"><a class="nav-link" href="<?= site_url('logout') ?>">Logout</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="<?= site_url('login') ?>">Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= site_url('register') ?>">Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<?php endif; ?>
<?php if (uri_string() == 'login' || uri_string() == 'register'): ?>
    <?= $this->renderSection('content') ?>
<?php else: ?>
    <div class="container">
        <?php if (session('success')): ?><div class="alert alert-success"><?= esc(session('success')) ?></div><?php endif; ?>
        <?php if (session('error')): ?><div class="alert alert-danger"><?= esc(session('error')) ?></div><?php endif; ?>
        <?= $this->renderSection('content') ?>
    </div>
<?php endif; ?>
<?php if ($isLoggedIn): ?>
<script>
function fetchNotifications() {
    $.get('<?= site_url('notifications') ?>', function(response) {
        if (!response.success) return;
        const badge = $('#notification-badge');
        const list = $('#notification-list');
        if (response.unread_count > 0) {
            badge.text(response.unread_count).show();
        } else {
            badge.hide();
        }
        list.empty();
        if (response.notifications.length === 0) {
            list.append('<li><a class="dropdown-item" href="#">No notifications</a></li>');
            return;
        }
        response.notifications.forEach(function(n) {
            const item = $('<li>');
            const box = $('<div>').addClass('alert ' + (n.is_read == 0 ? 'alert-info' : 'alert-secondary') + ' m-2 mb-0');
            box.html('<p class="mb-1">' + n.message + '</p>' +
                     '<small class="text-muted">' + new Date(n.created_at).toLocaleString() + '</small>' +
                     (n.is_read == 0 ? '<button class="btn btn-sm btn-outline-primary mt-2 mark-read-btn" data-id="' + n.id + '">Mark as Read</button>' : ''));
            item.append(box);
            list.append(item);
        });
    });
}
function markAsRead(id) {
    $.post('<?= site_url('notifications/mark_read') ?>/' + id, function(response) {
        if (response.success) fetchNotifications();
    });
}
$(document).ready(function() {
    fetchNotifications();
    $(document).on('click', '.mark-read-btn', function() {
        markAsRead($(this).data('id'));
    });
});
</script>
<?php endif; ?>
</body>
</html>
