<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: /schedule/index.php");
    exit;
}
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm mb-4">
<div class="container">

    <a class="navbar-brand fw-bold" href="/schedule/dashboard/index.php">
        📘 Deadline Akademik
    </a>

    <div class="collapse navbar-collapse">
        <ul class="navbar-nav me-auto">
            <li class="nav-item">
                <a class="nav-link" href="/schedule/dashboard/index.php">Dashboard</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/schedule/tasks/add.php">Tambah Tugas</a>
            </li>
            <!-- <li class="nav-item">
                <a class="nav-link" href="/schedule/courses/index.php">Course</a>
            </li> -->
        </ul>

        <span class="text-white me-3">
            👤 <?= htmlspecialchars($_SESSION['name']) ?>
        </span>

        <a href="/schedule/auth/logout.php" class="btn btn-light btn-sm">
            Logout
        </a>
    </div>

</div>
</nav>
