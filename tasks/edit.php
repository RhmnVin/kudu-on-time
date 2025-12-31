<?php
session_start();

require_once '../vendor/autoload.php';
require_once '../config/database.php';
require_once '../config/google.php';
include '../partials/header.php';

/* ================= AUTH ================= */
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

$conn    = Database::getConnection();
$user_id = (int) $_SESSION['user_id'];

/* ================= AMBIL ID ================= */
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    header("Location: add.php");
    exit;
}

/* ================= AMBIL TASK ================= */
$stmt = $conn->prepare("
    SELECT * FROM tasks 
    WHERE id = ? AND user_id = ?
");
$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();
$task = $stmt->get_result()->fetch_assoc();

if (!$task) {
    echo "<div class='container py-4'><div class='alert alert-danger'>Task tidak ditemukan</div></div>";
    exit;
}

/* ================= GOOGLE CLIENT ================= */
$client = null;
try {
    $client = getGoogleClient($user_id);
} catch (Exception $e) {
    $client = null;
}

$error = '';
$success = '';

/* ================= SUBMIT UPDATE ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $course_id  = (int) $_POST['course_id'];
    $title      = trim($_POST['title']);
    $type       = $_POST['type'];
    $start_date = $_POST['start_date'];
    $end_date   = $_POST['end_date'];

    if ($course_id <= 0 || !$title || !$start_date || !$end_date) {
        $error = "❌ Semua field wajib diisi";
    } else {

        /* ===== UPDATE GOOGLE CALENDAR ===== */
        if ($client && $task['google_event_id']) {
            try {
                $service = new Google_Service_Calendar($client);
                $event   = $service->events->get('primary', $task['google_event_id']);

                $event->setSummary($title);
                $event->setDescription($type);

                // START
                $start = new Google_Service_Calendar_EventDateTime();
                $start->setDateTime($start_date . 'T00:00:00');
                $start->setTimeZone('Asia/Makassar');
                $event->setStart($start);

                // END
                $end = new Google_Service_Calendar_EventDateTime();
                $end->setDateTime($end_date . 'T23:59:00');
                $end->setTimeZone('Asia/Makassar');
                $event->setEnd($end);

                $service->events->update('primary', $event->getId(), $event);

            } catch (Exception $e) {
                $error = "❌ Gagal update Google Calendar: " . $e->getMessage();
            }
        }

        /* ===== UPDATE DATABASE ===== */
        if (!$error) {
            $up = $conn->prepare("
                UPDATE tasks 
                SET course_id = ?, title = ?, type = ?, start_date = ?, end_date = ?
                WHERE id = ? AND user_id = ?
            ");
            $up->bind_param(
                "issssii",
                $course_id,
                $title,
                $type,
                $start_date,
                $end_date,
                $id,
                $user_id
            );

            if ($up->execute()) {
                $success = "✅ Task berhasil diperbarui";
            } else {
                $error = "❌ Gagal update database";
            }
        }
    }
}
?>

<div class="container py-4">

<h3>✏️ Edit Task</h3>

<?php if ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<form method="POST">

    <div class="mb-3">
        <label>Mata Kuliah</label>
        <select name="course_id" class="form-select" required>
            <?php
            $courses = $conn->query("SELECT id, course_name FROM courses ORDER BY course_name");
            while ($c = $courses->fetch_assoc()):
            ?>
                <option value="<?= $c['id'] ?>" <?= $c['id'] == $task['course_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($c['course_name']) ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>

    <div class="mb-3">
        <label>Judul Task</label>
        <input type="text" name="title" class="form-control"
               value="<?= htmlspecialchars($task['title']) ?>" required>
    </div>

    <div class="mb-3">
        <label>Jenis</label>
        <select name="type" class="form-select">
            <?php foreach (['Tugas','Ujian','Proyek'] as $t): ?>
                <option <?= $task['type'] === $t ? 'selected' : '' ?>><?= $t ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="row mb-3">
        <div class="col">
            <label>Start Date</label>
            <input type="date" name="start_date" class="form-control"
                   value="<?= $task['start_date'] ?>" required>
        </div>
        <div class="col">
            <label>End Date</label>
            <input type="date" name="end_date" class="form-control"
                   value="<?= $task['end_date'] ?>" required>
        </div>
    </div>

    <button class="btn btn-primary">💾 Update</button>
    <a href="add.php" class="btn btn-secondary">Kembali</a>

</form>
</div>
