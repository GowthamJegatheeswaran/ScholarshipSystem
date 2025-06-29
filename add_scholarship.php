<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'coordinator') {
    header("Location: login.php");
    exit;
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $provider_id = $_POST['provider_id'];
    $provider_name = $_POST['provider_name'];
    $email = $_POST['email'];
    $contact_no = $_POST['contact_no'];

    $scholarship_name = $_POST['scholarship_name'];
    $amount = $_POST['amount'];
    $deadline = $_POST['deadline'];

    $coordinator_id = $_POST['coordinator_id'];
    $min_score = $_POST['min_score'];

    // Check coordinator exists
    $checkC = $conn->prepare("SELECT * FROM Coordinator WHERE coordinator_id = ?");
    $checkC->bind_param("s", $coordinator_id);
    $checkC->execute();
    $resC = $checkC->get_result();

    if ($resC->num_rows === 0) {
        $message = "<p style='color:red;'>Invalid Coordinator ID</p>";
    } else {
        // Insert Provider (if not exists)
        $stmt = $conn->prepare("INSERT INTO Provider (provider_id, name, email, contact_no)
                                VALUES (?, ?, ?, ?)
                                ON DUPLICATE KEY UPDATE name=name");
        $stmt->bind_param("ssss", $provider_id, $provider_name, $email, $contact_no);
        $stmt->execute();

        // Insert Scholarship
        $stmt2 = $conn->prepare("INSERT INTO Scholarship (name, amount, deadline, provider_id, coordinator_id)
                                VALUES (?, ?, ?, ?, ?)");
        $stmt2->bind_param("sdsss", $scholarship_name, $amount, $deadline, $provider_id, $coordinator_id);
        if ($stmt2->execute()) {
            $scholarship_id = $conn->insert_id;

            // Insert eligibility
            $stmt3 = $conn->prepare("INSERT INTO Eligibility_Criteria (scholarship_id, min_score)
                                     VALUES (?, ?)");
            $stmt3->bind_param("ii", $scholarship_id, $min_score);
            $stmt3->execute();

            $message = "<p style='color:green;'>Scholarship added successfully!</p>";
        } else {
            $message = "<p style='color:red;'>Error adding scholarship.</p>";
        }
    }
}

$coordinators = $conn->query("SELECT coordinator_id FROM Coordinator");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Scholarship</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<h1>University of Jaffna <span>Scholarship Management System</span></h1>
<hr>
<h2>Add New Scholarship</h2>
<?= $message ?>
<form method="POST">
    <label>Provider ID:</label>
    <input type="text" name="provider_id" required>

    <label>Provider Name:</label>
    <input type="text" name="provider_name" required>

    <label>Provider Email:</label>
    <input type="email" name="email" required>

    <label>Provider Contact No:</label>
    <input type="text" name="contact_no" required>

    <label>Scholarship Name:</label>
    <input type="text" name="scholarship_name" required>

    <label>Monthly Amount:</label>
    <input type="number" name="amount" step="0.01" required>

    <label>Deadline:</label>
    <input type="date" name="deadline" required>

    <label>Coordinator ID:</label>
    <select name="coordinator_id" required>
        <option value="">-- Select Coordinator ID --</option>
        <?php while ($c = $coordinators->fetch_assoc()): ?>
            <option value="<?= $c['coordinator_id'] ?>"><?= $c['coordinator_id'] ?></option>
        <?php endwhile; ?>
    </select>

    <label>Minimum Score (Eligibility):</label>
    <input type="number" name="min_score" min="0" max="100" required>

    <input type="submit" value="Add Scholarship">
</form>

<a href="coordinator_dashboard.php">
    <button class="back-btn">⬅️ Back to Dashboard</button>
</a>
</body>
</html>
