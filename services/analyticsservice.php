<?php

class AnalyticsService
{
    private mysqli $conn;
    private int $user_id;

    public function __construct(mysqli $conn, int $user_id)
    {
        $this->conn = $conn;
        $this->user_id = $user_id;
    }

    /* ================= METRIC ================= */
    public function getMetrics(): array
    {
        $stmt = $this->conn->prepare("
            SELECT
                COUNT(*) AS total_tasks,
                COALESCE(SUM(workload),0) AS total_workload,
                SUM(CASE WHEN end_date < CURDATE() THEN 1 ELSE 0 END) AS overdue,
                SUM(CASE WHEN DATEDIFF(end_date, CURDATE()) BETWEEN 0 AND 3 THEN 1 ELSE 0 END) AS near_deadline
            FROM tasks
            WHERE user_id = ?
        ");
        $stmt->bind_param("i", $this->user_id);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    /* ================= REKOMENDASI ================= */
    public function getRecommendation(array $m): string
    {
        if ($m['overdue'] > 0) {
            return "⚠️ Ada tugas yang melewati deadline. Prioritaskan segera.";
        }

        if ($m['near_deadline'] >= 3) {
            return "⏰ Banyak tugas mendekati deadline. Kurangi beban lain.";
        }

        if ($m['total_workload'] > 40) {
            return "📊 Beban kerja tinggi. Disarankan penjadwalan ulang.";
        }

        return "✅ Beban akademik terkendali. Pertahankan pola kerja ini.";
    }

    /* ================= CSV ================= */
    public function getCsvData(): array
    {
        $stmt = $this->conn->prepare("
            SELECT title, start_date, end_date, workload
            FROM tasks
            WHERE user_id = ?
            ORDER BY end_date ASC
        ");
        $stmt->bind_param("i", $this->user_id);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
