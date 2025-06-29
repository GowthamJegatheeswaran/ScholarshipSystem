<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit;
}

$email = $_SESSION['user'];
$student = $conn->query("SELECT student_id, name FROM Student WHERE email = '$email'")->fetch_assoc();
$student_id = $student['student_id'];

$sql = "
SELECT n.notif_date, n.status_message
FROM Notification n
JOIN Application a ON n.application_id = a.application_id
WHERE a.student_id = ?
ORDER BY n.notif_date DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$res = $stmt->get_result();
?>

<h2>Hello <?= $student['name'] ?>, Your Notifications</h2>

<?php if ($res->num_rows === 0): ?>
    <p>No notifications yet.</p>
<?php else: ?>
    <ul>
    <?php while ($row = $res->fetch_assoc()): ?>
        <li><strong><?= $row['notif_date'] ?>:</strong> <?= $row['status_message'] ?></li>
    <?php endwhile; ?>
    </ul>
<?php endif; ?>

<a href="student_dashboard.php">
    <button style="padding:10px; background-color:#2980b9; color:white; border:none; border-radius:5px;">⬅️ Back to Dashboard</button>
</a>
