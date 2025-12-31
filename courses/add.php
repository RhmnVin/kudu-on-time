<?php
session_start();

require_once '../config/database.php';
include '../partials/header.php';

// 🔐 Proteksi login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

// Koneksi database (OOP)
$conn = Database::getConnection();

$error = '';

/* ================== SUBMIT ================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $course_name = trim($_POST['course_name'] ?? '');
    $course_code = trim($_POST['course_code'] ?? '');
    $semester    = (int) ($_POST['semester'] ?? 0);
    $sks         = (int) ($_POST['sks'] ?? 0);

    // Validasi
    if (!$course_name || !$course_code || $semester <= 0 || $sks <= 0) {
        $error = "❌ Semua field wajib diisi dengan benar";
    } else {

        $stmt = $conn->prepare("
            INSERT INTO courses (course_name, course_code, semester, sks)
            VALUES (?, ?, ?, ?)
        ");

        if (!$stmt) {
            $error = "❌ Query error: " . $conn->error;
        } else {
            $stmt->bind_param("ssii", $course_name, $course_code, $semester, $sks);

            if ($stmt->execute()) {
                header("Location: index.php?success=1");
                exit;
            } else {
                $error = "❌ Gagal menyimpan data";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Mata Kuliah</title>
</head>

<body class="bg-light">
<div class="container py-4">

<div class="row justify-content-center">
<div class="col-md-6">

<div class="card shadow-sm">
<div class="card-header bg-success text-white">
    ➕ Tambah Mata Kuliah
</div>

<div class="card-body">

<?php if ($error): ?>
    <div class="alert alert-danger">
        <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<form method="post">

<div class="mb-3">
    <label class="form-label">Nama Mata Kuliah</label>
    <input type="text" name="course_name" class="form-control" required>
</div>

<div class="mb-3">
    <label class="form-label">Kode Mata Kuliah</label>
    <input type="text" name="course_code" class="form-control" required>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Semester</label>
        <input type="number" name="semester" class="form-control" min="1" required>
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">SKS</label>
        <input type="number" name="sks" class="form-control" min="1" required>
    </div>
</div>

<div class="d-flex justify-content-between">
    <a href="index.php" class="btn btn-secondary">← Kembali</a>
    <button class="btn btn-success">💾 Simpan</button>
</div>

</form>

</div>
</div>

</div>
</div>

</div>
</body>
</html>
