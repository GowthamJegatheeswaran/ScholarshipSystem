<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit;
}

$student_id = $_SESSION['user'];

// ✅ Get student name
$stmt = $conn->prepare("SELECT name FROM Student WHERE student_id = ?");
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

if (!$student) {
    echo "<p style='color:red;'>Student not found. Please log in again.</p>";
    exit;
}

$name = $student['name'];

// ✅ Get awarded scholarships
$stmt = $conn->prepare("
    SELECT sc.name AS scholarship_name, sc.amount, sa.award_date
    FROM Scholarship_Awarded sa
    JOIN Application a ON sa.application_id = a.application_id
    JOIN Scholarship sc ON a.scholarship_id = sc.scholarship_id
    WHERE a.student_id = ?
    ORDER BY sa.award_date DESC
");
$stmt->bind_param("s", $student_id);
$stmt->execute();
$awards = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Awards</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .back-btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #007bff;
            color: white;
            font-size: 16px;
            font-weight: bold;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            margin-top: 20px;
        }
        .back-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h1>University of Jaffna <span>Scholarship Management System</span></h1>
    <h2>🎓 Awarded Scholarships for <?= htmlspecialchars($name) ?></h2>

    <?php if ($awards->num_rows == 0): ?>
        <p>No scholarships awarded yet.</p>
    <?php else: ?>
        <?php while ($row = $awards->fetch_assoc()): ?>
            <div style="background: #eaf4ff; padding: 10px; margin-bottom: 10px; border-radius: 5px;">
                <strong>Scholarship:</strong> <?= htmlspecialchars($row['scholarship_name']) ?><br>
                <strong>Amount:</strong> Rs.<?= $row['amount'] ?><br>
                <strong>Awarded on:</strong> <?= $row['award_date'] ?><br>
            </div>
        <?php endwhile; ?>
    <?php endif; ?>

    <a href="student_dashboard.php">
        <button class="back-btn">⬅ Back to Dashboard</button>
    </a>
</body>
</html>
