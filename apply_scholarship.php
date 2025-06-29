<?php
session_start();
include 'db.php';

// Check session role and login status
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit;
}

// Get student_id from session and verify student exists
$student_id = $_SESSION['user'];
$stmt = $conn->prepare("SELECT * FROM Student WHERE student_id = ?");
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

if (!$student) {
    echo "<p class='error'>Student not found. Please log in again.</p>";
    exit;
}

$message = "";

// When form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sid = $_POST['scholarship_id'];
    $gpa = floatval($_POST['gpa']);
    $income = floatval($_POST['income']);
    $siblings = intval($_POST['siblings']);

    // Score calculation logic
    $score = 0;
    $score += ($gpa >= 3.8 ? 50 : ($gpa >= 3.5 ? 40 : ($gpa >= 3.0 ? 30 : 20)));
    $score += ($income < 100000 ? 50 : ($income < 200000 ? 30 : ($income < 300000 ? 10 : 0)));
    $score += ($siblings >= 3 ? 20 : ($siblings == 2 ? 10 : 0));

    // Prevent duplicate applications
    $check = $conn->prepare("SELECT * FROM Application WHERE student_id = ? AND scholarship_id = ?");
    $check->bind_param("si", $student_id, $sid);
    $check->execute();
    $exists = $check->get_result();

    if ($exists->num_rows > 0) {
        $message = "<p class='error'>You have already applied for this scholarship.</p>";
    } else {
        // Insert application
        $stmt = $conn->prepare("INSERT INTO Application (student_id, scholarship_id, sub_date, score) VALUES (?, ?, NOW(), ?)");
        $stmt->bind_param("sii", $student_id, $sid, $score);
        if ($stmt->execute()) {
            $message = "<p class='success'>Application submitted successfully. Your score is <strong>$score</strong>.</p>";
        } else {
            $message = "<p class='error'>Application submission failed.</p>";
        }
    }
}

// Load all scholarships
$scholarships = $conn->query("SELECT * FROM Scholarship");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Apply for Scholarship</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<h1>University of Jaffna <span>Scholarship Management System</span></h1>
<hr>

<h2>Apply for a Scholarship</h2>

<?= $message ?>

<form method="POST">
    <label>Scholarship:</label>
    <select name="scholarship_id" required>
        <?php while ($s = $scholarships->fetch_assoc()): ?>
            <option value="<?= $s['scholarship_id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
        <?php endwhile; ?>
    </select><br>

    <label>GPA:</label>
    <input type="number" name="gpa" step="0.01" min="0" max="4" required><br>

    <label>Family Income (LKR):</label>
    <input type="number" name="income" min="0" required><br>

    <label>Number of Siblings:</label>
    <input type="number" name="siblings" min="0" required><br>

    <input type="submit" value="Apply">
</form>

<a href="student_dashboard.php"><button class="back-btn">Back to Dashboard</button></a>

<footer>
    <p>&copy; 2025 University of Jaffna. All rights reserved.</p>
</footer>

</body>
</html>
