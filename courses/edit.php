<?php
include '../config/database.php';
include '../partials/header.php';
$conn = Database::getConnection();

$id = $_GET['id'];
$data = $conn->query("SELECT * FROM courses WHERE id=$id")->fetch_assoc();

if (isset($_POST['update'])) {
    $kode = $_POST['course_code'];
    $nama = $_POST['course_name'];
    $semester = $_POST['semester'];
    $sks = $_POST['sks'];

    $conn->query("UPDATE courses SET
        course_code='$kode',
        course_name='$nama',
        semester='$semester',
        sks='$sks'
        WHERE id=$id
    ");

    header("Location: index.php");
}
?>

<div class="container py-4">
<h4>✏️ Edit Mata Kuliah</h4>

<form method="post">
    <div class="mb-2">
        <label>Kode</label>
        <input type="text" name="course_code" class="form-control"
               value="<?= $data['course_code'] ?>" required>
    </div>
    <div class="mb-2">
        <label>Nama</label>
        <input type="text" name="course_name" class="form-control"
               value="<?= $data['course_name'] ?>" required>
    </div>
    <div class="mb-2">
        <label>Semester</label>
        <input type="number" name="semester" class="form-control"
               value="<?= $data['semester'] ?>" required>
    </div>
    <div class="mb-3">
        <label>SKS</label>
        <input type="number" name="sks" class="form-control"
               value="<?= $data['sks'] ?>" required>
    </div>

    <button name="update" class="btn btn-primary">💾 Update</button>
    <a href="index.php" class="btn btn-secondary">⬅️ Kembali</a>
</form>
</div>
