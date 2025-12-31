<?php
session_start(); // ✅ WAJIB

require_once '../config/database.php';

// koneksi via OOP
$conn = Database::getConnection();

$email    = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (!$email || !$password) {
    echo "<script>
        alert('Email dan password wajib diisi!');
        window.location.href='../index.php';
    </script>";
    exit;
}

/* ================= AMBIL USER ================= */
$stmt = $conn->prepare("SELECT id, name, email, password FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();

$result = $stmt->get_result();
$user   = $result->fetch_assoc();

/* ================= VALIDASI LOGIN ================= */
if ($user && password_verify($password, $user['password'])) {

    // SIMPAN SESSION (INI YANG DIPAKAI GOOGLE CALENDAR)
    $_SESSION['user_id']    = $user['id'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['name']       = $user['name'];
    
    // REDIRECT
    if($_SESSION['email'] == "admin@gmail.com"){
        header("Location: ../dashboard-admin/index.php");
    }
    else{
    header("Location: ../dashboard/index.php");
    }
    exit;

} else {
    echo "<script>
        alert('Email atau password salah!');
        window.location.href='../index.php';
    </script>";
    exit;
}
