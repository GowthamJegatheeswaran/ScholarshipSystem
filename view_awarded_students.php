<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'coordinator') {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['scholarship_name'])) {
    echo "No scholarship selected.";
    exit;
}

$scholarship_name = $_GET['scholarship_name'];

$stmt = $conn->prepare("
    SELECT s.student_id, s.name AS student_name, sa.award_date
    FROM Scholarship_Awarded sa
    JOIN Application a ON sa.application_id = a.application_id
    JOIN Student s ON a.student_id = s.student_id
    JOIN Scholarship sc ON a.scholarship_id = sc.scholarship_id
    WHERE sc.name = ?
");
$stmt->bind_param("s", $scholarship_name);
$stmt->execute();
$res = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Awarded Students – <?= htmlspecialchars($scholarship_name) ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<h2>Awarded Students – <?= htmlspecialchars($scholarship_name) ?></h2>

<?php if ($res->num_rows === 0): ?>
    <p>No students have been awarded this scholarship yet.</p>
<?php else: ?>
    <table class="table">
        <tr>
            <th>Student ID</th>
            <th>Name</th>
            <th>Award Date</th>
        </tr>
        <?php while ($row = $res->fetch_assoc()): ?>
            <tr>
                <td><?= $row['student_id'] ?></td>
                <td><?= htmlspecialchars($row['student_name']) ?></td>
                <td><?= $row['award_date'] ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
<?php endif; ?>

<a href="coordinator_dashboard.php">
    <button class="link-button" style="margin-top: 20px;">⬅️ Back to Dashboard</button>
</a>

</body>
</html>
