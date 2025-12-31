<?php
session_start();

require_once '../config/database.php';

/* ================= PROTEKSI LOGIN ================= */
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

/* ================= KONEKSI ================= */
$conn = Database::getConnection();

/* ================= HITUNG DATA ================= */

// Jumlah User
$resUser = $conn->query("SELECT COUNT(*) AS total_user FROM users");
$totalUser = $resUser->fetch_assoc()['total_user'];

// Jumlah Course
$resCourse = $conn->query("SELECT COUNT(*) AS total_course FROM courses");
$totalCourse = $resCourse->fetch_assoc()['total_course'];

include '../partials/header-admin.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
</head>

<body class="bg-light">
<div class="container py-4">

    <h3 class="mb-4">📊 Dashboard Admin</h3>

    <div class="row">
        <!-- JUMLAH USER -->
        <div class="col-md-6">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">👤 Total User</h5>
                    <h2 class="fw-bold"><?= $totalUser ?></h2>
                </div>
            </div>
        </div>

        <!-- JUMLAH COURSE -->
        <div class="col-md-6">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">📚 Total Mata Kuliah</h5>
                    <h2 class="fw-bold"><?= $totalCourse ?></h2>
                </div>
            </div>
        </div>
    </div>

</div>
</body>
</html>
