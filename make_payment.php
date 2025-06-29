<?php
session_start();
include 'db.php';

if ($_SESSION['role'] !== 'coordinator') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $award_id = $_POST['award_id'];
    $amount = $_POST['amount'];
    $ref = $_POST['reference'];
    $date = date('Y-m-d');

    $stmt = $conn->prepare("INSERT INTO Scholarship_Payment (award_id, payment_date, amount, transfer_reference) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isds", $award_id, $date, $amount, $ref);

    if ($stmt->execute()) {
        echo "💸 Payment recorded.";
    } else {
        echo "❌ Error: " . $stmt->error;
    }
}

$awards = $conn->query("SELECT * FROM Scholarship_Awarded WHERE award_id NOT IN (SELECT award_id FROM Scholarship_Payment)");
?>

<h2>Record Payment for Awarded Students</h2>
<form method="POST">
    <label>Select Award ID:</label>
    <select name="award_id" required>
        <?php while ($a = $awards->fetch_assoc()): ?>
            <option value="<?= $a['award_id'] ?>">Award ID: <?= $a['award_id'] ?></option>
        <?php endwhile; ?>
    </select><br>
    <label>Amount:</label><input type="number" name="amount" step="0.01" required><br>
    <label>Transfer Reference:</label><input type="text" name="reference" required><br>
    <input type="submit" value="Record Payment">
</form>
