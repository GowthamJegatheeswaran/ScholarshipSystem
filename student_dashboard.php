<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit;
}

$student_id = $_SESSION['user'];
$stmt = $conn->prepare("SELECT name FROM Student WHERE student_id = ?");
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .dashboard-container {
            text-align: center;
            margin-top: 50px;
        }

        .dashboard-btn {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 14px 28px;
            font-size: 16px;
            border: none;
            border-radius: 8px;
            margin: 12px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .dashboard-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<h1>University of Jaffna <span>Scholarship Management System</span></h1>
<hr>

<h2>Welcome, <?= htmlspecialchars($student['name']) ?>!</h2>

<div class="dashboard-container">
    <a href="apply_scholarship.php"><button class="dashboard-btn">Apply for Scholarship</button></a>
    <a href="view_notifications.php"><button class="dashboard-btn">View Notifications</button></a>
    <a href="view_awards.php"><button class="dashboard-btn">View Awards</button></a>
    <a href="logout.php"><button class="dashboard-btn">Logout</button></a>
</div>

<footer>
    <p>&copy; 2025 University of Jaffna. All rights reserved.</p>
</footer>

</body>
</html>
