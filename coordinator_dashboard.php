<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'coordinator') {
    header("Location: login.php");
    exit;
}

$coordinator_id = $_SESSION['user'];

// Get coordinator name
$stmt = $conn->prepare("SELECT name FROM Coordinator WHERE coordinator_id = ?");
$stmt->bind_param("s", $coordinator_id); // coordinator_id is VARCHAR
$stmt->execute();
$res = $stmt->get_result();
$coordinator = $res->fetch_assoc();
$name = $coordinator ? $coordinator['name'] : "Coordinator";

// Get student count per scholarship
$scholarship_counts = [];
$sql = "
SELECT s.name AS scholarship_name, COUNT(sa.award_id) AS student_count
FROM Scholarship s
LEFT JOIN Application a ON s.scholarship_id = a.scholarship_id
LEFT JOIN Scholarship_Awarded sa ON a.application_id = sa.application_id
GROUP BY s.name
";
$res = $conn->query($sql);
while ($row = $res->fetch_assoc()) {
    $scholarship_counts[] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Coordinator Dashboard</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f2f5;
            padding: 20px;
        }
        h1, h2 {
            text-align: center;
            color: #333;
        }
        .summary-box {
            max-width: 700px;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 2px 10px rgba(0,0,0,0.1);
        }
        .button-group {
            text-align: center;
            margin-top: 20px;
        }
        .link-button {
            display: inline-block;
            padding: 12px 20px;
            margin: 10px;
            background-color: #2980b9;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-size: 16px;
        }
        .link-button:hover {
            background-color: #1e6fa4;
        }
        select {
            padding: 8px;
            width: 300px;
            font-size: 14px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        form select, form button {
            margin-top: 10px;
        }
    </style>
</head>
<body>

<h1>University of Jaffna</h1>
<h2>Welcome, <?= htmlspecialchars($name) ?> (Coordinator)</h2>

<div class="summary-box">
    <h3>🎓 Scholarship Award Summary</h3>

    <form action="view_awarded_students.php" method="GET" style="text-align: center;">
        <select name="scholarship_name" required>
            <option value="">-- Select Scholarship --</option>
            <?php foreach ($scholarship_counts as $sch): ?>
                <option value="<?= htmlspecialchars($sch['scholarship_name']) ?>">
                    <?= htmlspecialchars($sch['scholarship_name']) ?> (<?= $sch['student_count'] ?> student(s))
                </option>
            <?php endforeach; ?>
        </select>
        <br>
        <button type="submit" class="link-button">View Awarded Students</button>
    </form>

    <div class="button-group">
        <a class="link-button" href="review_application.php">Review Applications</a>
        <a class="link-button" href="award_list.php">Award Scholarships</a>
        <a class="link-button" href="add_scholarship.php">Add Scholarship</a>
        <a class="link-button" href="manage_scholarships.php">Manage Scholarships</a>
        <a class="link-button" href="logout.php">Logout</a>
    </div>
</div>

<footer style="text-align: center; margin-top: 30px;">
    <p>&copy; 2025 University of Jaffna. All rights reserved.</p>
</footer>

</body>
</html>
