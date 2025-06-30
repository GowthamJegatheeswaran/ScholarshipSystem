<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'coordinator') {
    header("Location: login.php");
    exit;
}

$coordinator_id = $_SESSION['user']; // assuming stored as coordinator_id

// ✅ When coordinator updates application status
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['application_id'];
    $status = $_POST['status'];

    // Get scholarship name for notification
    $app_query = $conn->prepare("
        SELECT sc.name AS scholarship_name 
        FROM Application a 
        JOIN Scholarship sc ON a.scholarship_id = sc.scholarship_id 
        WHERE a.application_id = ?
    ");
    $app_query->bind_param("i", $id);
    $app_query->execute();
    $app_data = $app_query->get_result()->fetch_assoc();
    $scholarship_name = $app_data['scholarship_name'];

    // Notification message
    $msg = "Your application for '$scholarship_name' has been $status.";

    // Update status
    $stmt1 = $conn->prepare("UPDATE Application SET status = ? WHERE application_id = ?");
    $stmt1->bind_param("si", $status, $id);
    $stmt1->execute();

    // Insert notification
    $stmt2 = $conn->prepare("INSERT INTO Notification (application_id, notif_date, status_message) VALUES (?, NOW(), ?)");
    $stmt2->bind_param("is", $id, $msg);
    $stmt2->execute();

    // ✅ Add to Scholarship_Awarded if approved and not already added
    if ($status === 'Approved') {
        $check = $conn->prepare("SELECT * FROM Scholarship_Awarded WHERE application_id = ?");
        $check->bind_param("i", $id);
        $check->execute();
        $check_result = $check->get_result();

        if ($check_result->num_rows == 0) {
            $insert = $conn->prepare("INSERT INTO Scholarship_Awarded (application_id, award_date) VALUES (?, NOW())");
            $insert->bind_param("i", $id);
            $insert->execute();
        }
    }
}

// ✅ Fetch applications only for this coordinator’s scholarships
$sql = "
SELECT 
    a.application_id,
    a.student_id,
    a.scholarship_id,
    a.score,
    a.status,
    s.name AS student_name,
    sc.name AS scholarship_name,
    ec.min_score
FROM Application a
JOIN Scholarship sc ON a.scholarship_id = sc.scholarship_id
LEFT JOIN Student s ON a.student_id = s.student_id
LEFT JOIN Eligibility_Criteria ec ON sc.scholarship_id = ec.scholarship_id
WHERE sc.coordinator_id = ?
ORDER BY a.application_id DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $coordinator_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Review Applications</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<h1>University of Jaffna</h1>
<h2>Review Scholarship Applications</h2>

<?php if ($result->num_rows === 0): ?>
    <p>No applications found for your scholarships.</p>
<?php else: ?>
    <?php while ($row = $result->fetch_assoc()): ?>
        <div style="border:1px solid #ccc; padding:10px; margin:10px;">
            <strong>Application ID:</strong> <?= $row['application_id'] ?><br>
            <strong>Student:</strong> <?= $row['student_name'] ?? 'Unknown Student' ?><br>
            <strong>Scholarship:</strong> <?= $row['scholarship_name'] ?? 'Unknown Scholarship' ?><br>
            <strong>Score:</strong> <?= $row['score'] ?><br>
            <strong>Min Required:</strong> <?= $row['min_score'] ?? 'Not Set' ?><br>
            <strong>Status:</strong> <?= $row['status'] ?><br>
            <form method="POST">
                <input type="hidden" name="application_id" value="<?= $row['application_id'] ?>">
                <select name="status" required>
                    <option value="Pending" <?= $row['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="Shortlisted" <?= $row['status'] === 'Shortlisted' ? 'selected' : '' ?>>Shortlisted</option>
                    <option value="Approved" <?= $row['status'] === 'Approved' ? 'selected' : '' ?>>Approved</option>
                    <option value="Rejected" <?= $row['status'] === 'Rejected' ? 'selected' : '' ?>>Rejected</option>
                </select>
                <input type="submit" value="Update Status">
            </form>
        </div>
    <?php endwhile; ?>
<?php endif; ?>

<br>
<a href="coordinator_dashboard.php">
    <button style="padding:10px 20px; background:#2980b9; color:white; border:none; border-radius:5px;">⬅️ Back</button>
</a>

</body>
</html>
