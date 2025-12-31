<?php
session_start();

require_once '../vendor/autoload.php';
require_once '../config/database.php';
require_once '../config/google.php';

/* ================= AUTH ================= */
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

$conn    = Database::getConnection();
$user_id = (int) $_SESSION['user_id'];

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    header("Location: add.php");
    exit;
}

/* ================= AMBIL TASK ================= */
$stmt = $conn->prepare("
    SELECT google_event_id 
    FROM tasks 
    WHERE id = ? AND user_id = ?
");
$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();
$task = $stmt->get_result()->fetch_assoc();

if (!$task) {
    header("Location: add.php");
    exit;
}

/* ================= HAPUS GOOGLE EVENT ================= */
if ($task['google_event_id']) {
    try {
        $client = getGoogleClient($user_id);
        if ($client && $client->getAccessToken()) {
            $service = new Google_Service_Calendar($client);
            $service->events->delete('primary', $task['google_event_id']);
        }
    } catch (Exception $e) {
        // gagal hapus google event → tetap lanjut hapus database
    }
}

/* ================= HAPUS DATABASE ================= */
$del = $conn->prepare("
    DELETE FROM tasks 
    WHERE id = ? AND user_id = ?
");
$del->bind_param("ii", $id, $user_id);
$del->execute();

header("Location: add.php");
exit;
