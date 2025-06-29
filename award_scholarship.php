<?php
session_start();
include 'db.php';

if ($_SESSION['role'] !== 'coordinator') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $application_id = $_POST['application_id'];
    $date = date('Y-m-d');

    $stmt = $conn->prepare("INSERT INTO Scholarship_Awarded (application_id, award_date) VALUES (?, ?)");
    $stmt->bind_param("is", $application_id, $date);

    if ($stmt->execute()) {
        echo "🎉 Award recorded successfully.";
    } else {
        echo "❌ Error: " . $stmt->error;
    }
}

$applications = $conn->query("SELECT * FROM Application WHERE status = 'Approved' AND application_id NOT IN (SELECT application_id FROM Scholarship_Awarded)");
?>

<h2>Award Approved Applications</h2>
<form method="POST">
    <label>Select Application:</label>
    <select name="application_id" required>
        <?php while ($a = $applications->fetch_assoc()): ?>
            <option value="<?= $a['application_id'] ?>">App ID: <?= $a['application_id'] ?> | Score: <?= $a['score'] ?></option>
        <?php endwhile; ?>
    </select><br>
    <input type="submit" value="Award Scholarship">
</form>
