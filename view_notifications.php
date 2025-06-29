<?php
session_start();
include 'db.php';

// Validate student session
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit;
}

$student_id = $_SESSION['user'];

// Get student name
$stmt = $conn->prepare("SELECT name FROM Student WHERE student_id = ?");
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

if (!$student) {
    echo "<p class='error'>Student not found. Please login again.</p>";
    exit;
}

// Get notifications
$sql = "
    SELECT n.notif_date, n.status_message
    FROM Notification n
    JOIN Application a ON n.application_id = a.application_id
    WHERE a.student_id = ?
    ORDER BY n.notif_date DESC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$res = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Notifications</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .back-btn {
            padding: 14px 28px;
            font-size: 16px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 8px;
            margin-top: 20px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .back-btn:hover {
            background-color: #0056b3;
        }
        ul.notification-list {
            list-style: none;
            padding: 0;
        }
        ul.notification-list li {
            background: #f9f9f9;
            padding: 12px;
            border-bottom: 1px solid #ccc;
        }
        ul.notification-list li strong {
            color: #007bff;
        }
    </style>
</head>
<body>

<h1>University of Jaffna <span>Scholarship Management System</span></h1>
<hr>

<h2>Hello <?= htmlspecialchars($student['name']) ?>, Your Notifications</h2>

<?php if ($res->num_rows === 0): ?>
    <p>No notifications yet.</p>
<?php else: ?>
    <ul class="notification-list">
        <?php while ($row = $res->fetch_assoc()): ?>
            <li><strong><?= $row['notif_date'] ?>:</strong> <?= htmlspecialchars($row['status_message']) ?></li>
        <?php endwhile; ?>
    </ul>
<?php endif; ?>

<a href="student_dashboard.php">
    <button class="back-btn">⬅️ Back to Dashboard</button>
</a>

<footer>
    <p>&copy; 2025 University of Jaffna. All rights reserved.</p>
</footer>

</body>
</html>
