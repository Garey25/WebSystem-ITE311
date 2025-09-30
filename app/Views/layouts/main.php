<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'ITE311' ?></title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom Styles -->
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        
        .navbar {
            background-color: #181818;
            padding: 1rem 2rem;
            box-shadow: none;
        }

        .navbar-brand {
            font-weight: 600;
            color: #1DB954 !important;
            font-size: 1.5rem;
        }

        .nav-link {
            color: #b3b3b3 !important;
            font-weight: 500;
            margin-right: 1rem;
            transition: color 0.3s;
        }

        .nav-link:hover,
        .nav-link.active {
            color: #ffffff !important;
        }

        .container {
            background-color: #181818;
            border-radius: 12px;
            padding: 30px;
            margin-top: 30px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
        }

        h1, h2, h3, h4, h5 {
            color: #ffffff;
        }

        .btn-primary {
            background-color: #1DB954;
            border-color: #1DB954;
            font-weight: 500;
        }

        .btn-primary:hover {
            background-color: #1ed760;
            border-color: #1ed760;
        }

        .form-control,
        .form-select {
            background-color: #2a2a2a;
            border: 1px solid #444;
            color: #fff;
        }

        .form-control:focus {
            background-color: #2a2a2a;
            color: #fff;
            border-color: #1DB954;
            box-shadow: 0 0 0 0.2rem rgba(29, 185, 84, 0.25);
        }

        .text-muted {
            color: #b3b3b3 !important;
        }

        a {
            color: #1DB954;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
            color: #1ed760;
        }

        .card {
            background-color: #2a2a2a;
            border: 1px solid #444;
        }

        .card-header {
            background-color: #333;
            border-bottom: 1px solid #444;
            color: #fff;
        }

        .card-body {
            color: #fff;
        }
    </style>
</head>
<body>

<?php $isLoggedIn = session('isLoggedIn') ?? false; $role = session('role') ?? null; ?>
<nav class="navbar navbar-dark bg-dark navbar-expand">
    <div class="container">
        <a class="navbar-brand" href="<?= site_url('/') ?>">ITE311</a>
        <ul class="navbar-nav ms-auto">
            <li class="nav-item"><a class="nav-link" href="<?= site_url('/') ?>">Home</a></li>
            <li class="nav-item"><a class="nav-link" href="<?= site_url('about') ?>">About</a></li>
            <li class="nav-item"><a class="nav-link" href="<?= site_url('contact') ?>">Contact</a></li>

            <?php if ($isLoggedIn): ?>
                <li class="nav-item"><a class="nav-link" href="<?= site_url('dashboard') ?>">Dashboard</a></li>

                <?php if ($role === 'admin'): ?>
                    <li class="nav-item"><a class="nav-link" href="#">Users</a></li>
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
</nav>

<main class="container py-4">
    <?php if (session('success')): ?>
        <div class="alert alert-success"><?= esc(session('success')) ?></div>
    <?php endif; ?>
    <?php if (session('error')): ?>
        <div class="alert alert-danger"><?= esc(session('error')) ?></div>
    <?php endif; ?>

    <?= $this->renderSection('content') ?>
</main>

</body>
</html>
