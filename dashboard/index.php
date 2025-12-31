<?php
session_start();

require_once '../config/database.php';
require_once '../services/AnalyticsService.php';

/* ================= PROTEKSI LOGIN ================= */
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

/* ================= KONEKSI ================= */
$conn = Database::getConnection();

$user_id = (int) $_SESSION['user_id'];
$email   = $_SESSION['email'] ?? '';
$today   = date('Y-m-d');

/* ================= ANALYTICS ================= */
$analytics = new AnalyticsService($conn, $user_id);
$metrics   = $analytics->getMetrics();
$insight   = $analytics->getRecommendation($metrics);

/* ================= EXPORT CSV ================= */
if (isset($_GET['export']) && $_GET['export'] === 'csv') {

    $rows = $analytics->getCsvData();

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="deadline_akademik.csv"');

    $out = fopen('php://output', 'w');
    fputcsv($out, ['Judul', 'Mulai', 'Deadline', 'Workload (Jam)']);

    foreach ($rows as $r) {
        fputcsv($out, $r);
    }
    fclose($out);
    exit;
}

include '../partials/header.php';
require_once '../config/mail.php';

/* ================= EMAIL REMINDER ================= */
$notif = $conn->prepare("
    SELECT id, title, end_date
    FROM tasks
    WHERE user_id = ?
      AND DATEDIFF(end_date, ?) BETWEEN 0 AND 3
      AND email_sent = 0
");
$notif->bind_param("is", $user_id, $today);
$notif->execute();
$resNotif = $notif->get_result();

while ($t = $resNotif->fetch_assoc()) {

    $msg = "
        <h3>⏰ Reminder Deadline Akademik</h3>
        <p><strong>{$t['title']}</strong></p>
        <p>Deadline: {$t['end_date']}</p>
    ";

    if ($email && sendEmail($email, "Reminder Deadline Akademik", $msg)) {
        $up = $conn->prepare("UPDATE tasks SET email_sent = 1 WHERE id = ?");
        $up->bind_param("i", $t['id']);
        $up->execute();
    }
}

/* ================= GANTT DATA ================= */
$stmt = $conn->prepare("
    SELECT t.id, t.title, t.start_date, t.end_date, c.course_name
    FROM tasks t
    JOIN courses c ON t.course_id = c.id
    WHERE t.user_id = ?
    ORDER BY t.end_date
");
$stmt->bind_param("i", $user_id);
$stmt->execute();

$ganttTasks = [];
$res = $stmt->get_result();
while ($r = $res->fetch_assoc()) {
    $ganttTasks[] = [
        "id"    => $r['id'],
        "name"  => $r['title'] . " (" . $r['course_name'] . ")",
        "start" => $r['start_date'],
        "end"   => $r['end_date']
    ];
}

/* ================= CHART ================= */
$chart = $conn->prepare("
    SELECT DATE_FORMAT(start_date, '%Y-%m') periode, SUM(workload) total
    FROM tasks
    WHERE user_id = ?
    GROUP BY periode
    ORDER BY periode
");
$chart->bind_param("i", $user_id);
$chart->execute();

$labels = [];
$data   = [];
$resChart = $chart->get_result();
while ($c = $resChart->fetch_assoc()) {
    $labels[] = $c['periode'];
    $data[]   = (int)$c['total'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Deadline Akademik</title>

    <link href="https://cdn.jsdelivr.net/npm/frappe-gantt@0.6.1/dist/frappe-gantt.css" rel="stylesheet">
</head>

<body class="bg-light">
<div class="container py-4">

    <div class="d-flex justify-content-between mb-3">
        <h3>📘 Dashboard Deadline Akademik</h3>
        <a href="../tasks/add.php" class="btn btn-primary">➕ Tambah Tugas</a>
    </div>

    <!-- RINGKASAN -->
    <div class="row mb-4">
        <div class="col-md-3"><div class="card text-center"><div class="card-body"><h6>Total Tugas</h6><h3><?= $metrics['total_tasks'] ?></h3></div></div></div>
        <div class="col-md-3"><div class="card text-center"><div class="card-body"><h6>Total Workload</h6><h3><?= $metrics['total_workload'] ?> Jam</h3></div></div></div>
        <div class="col-md-3"><div class="card text-center text-danger"><div class="card-body"><h6>Terlambat</h6><h3><?= $metrics['overdue'] ?></h3></div></div></div>
        <div class="col-md-3"><div class="card text-center text-warning"><div class="card-body"><h6>Deadline ≤3 Hari</h6><h3><?= $metrics['near_deadline'] ?></h3></div></div></div>
    </div>

    <!-- REKOMENDASI -->
    <div class="card mb-4">
        <div class="card-body d-flex justify-content-between">
            <p class="mb-0"><?= $insight ?></p>
            <a href="?export=csv" class="btn btn-outline-success">⬇️ CSV</a>
        </div>
    </div>

    <!-- GANTT -->
    <div class="card mb-4">
        <div class="card-header">📅 Timeline</div>
        <div class="card-body"><div id="gantt"></div></div>
    </div>

    <!-- CHART -->
    <div class="card">
        <div class="card-header">📊 Workload</div>
        <div class="card-body"><canvas id="chart"></canvas></div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/frappe-gantt@0.6.1/dist/frappe-gantt.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
new Gantt("#gantt", <?= json_encode($ganttTasks) ?>, { date_format: 'YYYY-MM-DD' });

new Chart(document.getElementById('chart'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($labels) ?>,
        datasets: [{
            label: 'Workload (Jam)',
            data: <?= json_encode($data) ?>,
            backgroundColor: '#0d6efd'
        }]
    }
});
</script>

</body>
</html>
