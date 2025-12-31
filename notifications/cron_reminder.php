<?php
include '../config/database.php';

$q = $conn->query("
SELECT u.email, t.title
FROM notifications n
JOIN tasks t ON n.task_id = t.id
JOIN users u ON t.user_id = u.id
WHERE n.notify_at <= NOW()
AND n.status='pending'
");

while($row = $q->fetch_assoc()){
    mail(
        $row['email'],
        "⏰ Reminder Deadline",
        "Deadline: ".$row['title']
    );
}
?>
