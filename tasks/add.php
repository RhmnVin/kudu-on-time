<?php
session_start();

require_once '../vendor/autoload.php';
require_once '../config/database.php';
require_once '../config/google.php';
include '../partials/header.php';

/* ================== AUTH ================== */
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

$conn = Database::getConnection();
$user_id = (int) $_SESSION['user_id'];

$error = '';
$success = '';

/* ================== GOOGLE CLIENT ================== */
$client = null;
try {
    $client = getGoogleClient($user_id);
} catch (Exception $e) {
    $client = null;
}

if (!$client || !$client->getAccessToken()) {
?>
    <div class="container py-5">
        <div class="alert alert-warning">
            <h5>⚠️ Google Calendar belum terhubung</h5>
            <a href="../config/google_oauth.php" class="btn btn-danger">
                🔗 Hubungkan Google Calendar
            </a>
            <a href="/schedule/dashboard/index.php" class="btn btn-secondary ms-2">
                ⬅️ Kembali
            </a>
        </div>
    </div>
<?php
    exit;
}

/* ================== SUBMIT ADD ================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $course_id  = (int) $_POST['course_id'];
    $title      = trim($_POST['title']);
    $type       = $_POST['type'];
    $start_date = $_POST['start_date'];
    $end_date   = $_POST['end_date'];
    $workload   = 1;

    if ($course_id <= 0 || !$title || !$start_date || !$end_date) {
        $error = "❌ Semua field wajib diisi";
    } else {

        $google_event_id = null;

        try {
            $service = new Google_Service_Calendar($client);

            $event = new Google_Service_Calendar_Event();
            $event->setSummary($title);
            $event->setDescription($type);

            /* START */
            $start = new Google_Service_Calendar_EventDateTime();
            $start->setDateTime($start_date . 'T00:00:00');
            $start->setTimeZone('Asia/Makassar');
            $event->setStart($start);

            /* END */
            $end = new Google_Service_Calendar_EventDateTime();
            $end->setDateTime($end_date . 'T23:59:00');
            $end->setTimeZone('Asia/Makassar');
            $event->setEnd($end);

            /* INSERT */
            $created = $service->events->insert('primary', $event);
            $google_event_id = $created->getId();
        } catch (Exception $e) {
            $error = "❌ Google Calendar Error";
        }

        if (!$error) {
            $stmt = $conn->prepare("
                INSERT INTO tasks
                (user_id, course_id, title, type, start_date, end_date, workload, google_event_id, email_sent)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0)
            ");
            $stmt->bind_param(
                "iissssis",
                $user_id,
                $course_id,
                $title,
                $type,
                $start_date,
                $end_date,
                $workload,
                $google_event_id
            );

            if ($stmt->execute()) {
                $success = "✅ Task berhasil ditambahkan";
            } else {
                $error = "❌ Gagal menyimpan task";
            }
        }
    }
}
?>

<div class="container py-4">

    <h3>➕ Tambah Task Akademik</h3>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <!-- ================= FORM TAMBAH ================= -->
    <form method="POST" class="mb-5">

        <div class="mb-3">
            <label>Mata Kuliah</label>
            <select name="course_id" class="form-select" required>
                <option value="">-- Pilih Mata Kuliah --</option>
                <?php
                $courses = $conn->query("SELECT id, course_name FROM courses ORDER BY course_name");
                while ($c = $courses->fetch_assoc()):
                ?>
                    <option value="<?= $c['id'] ?>">
                        <?= htmlspecialchars($c['course_name']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label>Judul Task</label>
            <input type="text" name="title" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Jenis</label>
            <select name="type" class="form-select">
                <option>Tugas</option>
                <option>Ujian</option>
                <option>Proyek</option>
            </select>
        </div>

        <div class="row mb-3">
            <div class="col">
                <label>Start</label>
                <input type="date" name="start_date" class="form-control" required>
            </div>
            <div class="col">
                <label>End</label>
                <input type="date" name="end_date" class="form-control" required>
            </div>
        </div>

        <button class="btn btn-primary">💾 Simpan</button>
        <a href="/schedule/dashboard/index.php" class="btn btn-secondary">Kembali</a>
    </form>

    <!-- ================= LIST TASK ================= -->
    <h4 class="mb-3">📋 Daftar Task</h4>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Judul</th>
                <th>Mata Kuliah</th>
                <th>Tanggal</th>
                <th width="160">Aksi</th>
            </tr>
        </thead>

        <tbody>
            <?php
            $tasks = $conn->prepare("
    SELECT t.*, c.course_name
    FROM tasks t
    JOIN courses c ON t.course_id = c.id
    WHERE t.user_id = ?
    ORDER BY t.end_date
");
            $tasks->bind_param("i", $user_id);
            $tasks->execute();
            $res = $tasks->get_result();

            while ($t = $res->fetch_assoc()):
            ?>
                <tr>
                    <td><?= htmlspecialchars($t['title']) ?></td>
                    <td><?= htmlspecialchars($t['course_name']) ?></td>
                    <td><?= $t['start_date'] ?> → <?= $t['end_date'] ?></td>
                    <td>
                        <a href="edit.php?id=<?= $t['id'] ?>" class="btn btn-sm btn-warning">✏️ Edit</a>
                        <a href="delete.php?id=<?= $t['id'] ?>"
                            onclick="return confirm('Hapus task ini?')"
                            class="btn btn-sm btn-danger mt-2">🗑️ Hapus</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

</div>