<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'coordinator') {
    header("Location: login.php");
    exit;
}

// ✅ When coordinator updates application status
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['application_id'];
    $status = $_POST['status'];

    // Get scholarship name for notification
    $app_query = $conn->query("
        SELECT sc.name AS scholarship_name 
        FROM Application a 
        JOIN Scholarship sc ON a.scholarship_id = sc.scholarship_id 
        WHERE a.application_id = $id
    ");
    $app_data = $app_query->fetch_assoc();
    $scholarship_name = $app_data['scholarship_name'];

    // Notification message
    $msg = "Your application for '$scholarship_name' has been $status.";

    // Update application status
    $conn->query("UPDATE Application SET status = '$status' WHERE application_id = $id");

    // Insert notification
    $stmt = $conn->prepare("INSERT INTO Notification (application_id, notif_date, status_message) VALUES (?, NOW(), ?)");
    $stmt->bind_param("is", $id, $msg);
    $stmt->execute();

    // ✅ Add to Scholarship_Awarded table if approved
    if ($status === 'Approved') {
        $check = $conn->query("SELECT * FROM Scholarship_Awarded WHERE application_id = $id");
        if ($check->num_rows == 0) {
            $conn->query("INSERT INTO Scholarship_Awarded (application_id, award_date) VALUES ($id, NOW())");
        }
    }
}

// ✅ Fetch all applications
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
LEFT JOIN Student s ON a.student_id = s.student_id
LEFT JOIN Scholarship sc ON a.scholarship_id = sc.scholarship_id
LEFT JOIN Eligibility_Criteria ec ON sc.scholarship_id = ec.scholarship_id
ORDER BY a.application_id DESC
";

$result = $conn->query($sql);
?>

<h2>Review Applications</h2>

<?php if ($result->num_rows === 0): ?>
    <p>No applications found.</p>
<?php else: ?>
    <?php while ($row = $result->fetch_assoc()): ?>
        <div style="border:1px solid #ccc; padding:10px; margin:10px;">
            <strong>Application ID:</strong> <?= $row['application_id'] ?><br>
            <strong>Student:</strong> <?= $row['student_name'] ?? 'Unknown' ?><br>
            <strong>Scholarship:</strong> <?= $row['scholarship_name'] ?? 'Unknown' ?><br>
            <strong>Score:</strong> <?= $row['score'] ?><br>
            <strong>Min Required:</strong> <?= $row['min_score'] ?? 'Not Set' ?><br>
            <strong>Status:</strong> <?= $row['status'] ?><br>
            <form method="POST">
                <input type="hidden" name="application_id" value="<?= $row['application_id'] ?>">
                <select name="status">
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

<!-- 🔙 Back Button -->
<br>
<a href="coordinator_dashboard.php">
    <button style="padding:10px 20px; background:#2980b9; border:none; color:white; border-radius:5px;">⬅️ Back</button>
</a>
