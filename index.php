<?php
session_start();
require_once 'config/database.php';

// Jika sudah login, langsung ke dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard/index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login | Web Pengingatt Deadline Akademik</title>

    <!-- BOOTSTRAP 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- ICON -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #0d6efd, #6610f2);
            min-height: 100vh;
        }
        .login-card {
            border-radius: 15px;
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            <div class="card login-card shadow-lg">
                <div class="card-body p-4">

                    <div class="text-center mb-4">
                        <i class="bi bi-calendar-check fs-1 text-primary"></i>
                        <h4 class="mt-2 fw-bold">Web Pengingatt Deadline</h4>
                        <p class="text-muted small">Login Dosen</p>
                    </div>

                    <form method="POST" action="auth/login.php">
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-envelope"></i>
                                </span>
                                <input
                                    type="email"
                                    name="email"
                                    class="form-control"
                                    placeholder="email@kampus.ac.id"
                                    required
                                >
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-lock"></i>
                                </span>
                                <input
                                    type="password"
                                    name="password"
                                    class="form-control"
                                    placeholder="********"
                                    required
                                >
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 mt-2">
                            <i class="bi bi-box-arrow-in-right"></i> Login
                        </button>
                    </form>

                    <hr>

                    <div class="text-center">
                        <span class="small">Belum punya akun?</span><br>
                        <a href="auth/register.php" class="text-decoration-none fw-semibold">
                            Daftar Sekarang
                        </a>
                    </div>

                </div>
            </div>

            <p class="text-center text-white small mt-3">
                © <?= date('Y') ?> Web Pengingat Deadline Akademik
            </p>
        </div>
    </div>
</div>

</body>
</html>
