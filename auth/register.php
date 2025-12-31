<?php
session_start(); // ✅ WAJIB

require_once '../config/database.php';

// Koneksi database (OOP)
$conn = Database::getConnection();

// Jika sudah login → redirect
if (isset($_SESSION['user_id'])) {
    header("Location: ../dashboard/index.php");
    exit;
}

$alert = "";

/* ================== SUBMIT ================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validasi dasar
    if (!$name || !$email || !$password) {
        $alert = "<div class='alert alert-danger'>Semua field wajib diisi!</div>";
    } elseif (strlen($password) < 6) {
        $alert = "<div class='alert alert-danger'>Password minimal 6 karakter!</div>";
    } else {

        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Cek email sudah terdaftar
        $cek = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $cek->bind_param("s", $email);
        $cek->execute();
        $cek->store_result();

        if ($cek->num_rows > 0) {
            $alert = "<div class='alert alert-danger'>Email sudah terdaftar!</div>";
        } else {

            // Insert user
            $stmt = $conn->prepare(
                "INSERT INTO users (name, email, password) VALUES (?, ?, ?)"
            );
            $stmt->bind_param("sss", $name, $email, $hashedPassword);

            if ($stmt->execute()) {
                echo "<script>
                    alert('Registrasi berhasil! Silakan login.');
                    window.location.href='../index.php';
                </script>";
                exit;
            } else {
                $alert = "<div class='alert alert-danger'>Gagal registrasi. Coba lagi.</div>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register | Web Pengingat Deadline Akademik</title>

    <!-- BOOTSTRAP 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- ICON -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #198754, #0d6efd);
            min-height: 100vh;
        }
        .register-card {
            border-radius: 15px;
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card register-card shadow-lg">
                <div class="card-body p-4">

                    <div class="text-center mb-4">
                        <i class="bi bi-person-plus-fill fs-1 text-success"></i>
                        <h4 class="mt-2 fw-bold">Registrasi Dosen</h4>
                        <p class="text-muted small">Web Pengingat Deadline Akademik</p>
                    </div>

                    <?= $alert ?>

                    <form method="post">
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-person"></i>
                                </span>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-envelope"></i>
                                </span>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-lock"></i>
                                </span>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success w-100 mt-2">
                            <i class="bi bi-person-check"></i> Daftar
                        </button>
                    </form>

                    <hr>

                    <div class="text-center">
                        <span class="small">Sudah punya akun?</span><br>
                        <a href="../index.php" class="text-decoration-none fw-semibold">
                            Login Sekarang
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
