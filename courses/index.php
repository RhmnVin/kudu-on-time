<?php
session_start();
include '../config/database.php';
include '../partials/header.php';
$conn = Database::getConnection();

$courses = $conn->query("SELECT * FROM courses ORDER BY semester");
?>

<div class="container py-4">

<div class="d-flex justify-content-between mb-3">
    <h4>📚 Data Mata Kuliah</h4>
    <a href="add.php" class="btn btn-success">➕ Tambah</a>
</div>

<div class="card">
<div class="card-body">

<table class="table table-bordered table-striped">
<thead>
<tr>
    <th>#</th>
    <th>Kode</th>
    <th>Nama</th>
    <th>Semester</th>
    <th>SKS</th>
    <th width="160">Aksi</th>
</tr>
</thead>

<tbody>
<?php $no=1; while($c=$courses->fetch_assoc()): ?>
<tr>
    <td><?= $no++ ?></td>
    <td><?= htmlspecialchars($c['course_code']) ?></td>
    <td><?= htmlspecialchars($c['course_name']) ?></td>
    <td><?= $c['semester'] ?></td>
    <td><?= $c['sks'] ?></td>
    <td class="text-center">
        <a href="edit.php?id=<?= $c['id'] ?>" class="btn btn-sm btn-warning">
            ✏️ Edit
        </a>
        <a href="delete.php?id=<?= $c['id'] ?>"
           class="btn btn-sm btn-danger mt-2"
           onclick="return confirm('Yakin ingin menghapus mata kuliah ini?')">
            🗑️ Hapus
        </a>
    </td>
</tr>
<?php endwhile; ?>
</tbody>

</table>

</div>
</div>

</div>
