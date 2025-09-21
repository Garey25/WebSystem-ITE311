<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>WebSystem</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom Spotify-Like Styles -->
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        
        <?php if (uri_string() != 'login' && uri_string() != 'register'): ?>
        body {
            background-color: #121212;
            color: #ffffff;
        }
        <?php endif; ?>

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
    </style>
</head>
<body>

<?php if (uri_string() != 'login' && uri_string() != 'register'): ?>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?= site_url('/') ?>">WebSystem</a>
        <button class="navbar-toggler bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link <?= uri_string() == '' ? 'active' : '' ?>" href="<?= site_url('/') ?>">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= uri_string() == 'about' ? 'active' : '' ?>" href="<?= site_url('about') ?>">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= uri_string() == 'contact' ? 'active' : '' ?>" href="<?= site_url('contact') ?>">Contact</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<?php endif; ?>

<!-- Page Content -->
<?php if (uri_string() == 'login' || uri_string() == 'register'): ?>
    <?= $this->renderSection('content') ?>
<?php else: ?>
    <div class="container">
        <?= $this->renderSection('content') ?>
    </div>
<?php endif; ?>

</body>
</html>
