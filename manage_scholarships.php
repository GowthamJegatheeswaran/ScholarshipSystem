<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'coordinator') {
    header("Location: login.php");
    exit;
}

$coordinator_id = $_SESSION['user'];

// Fetch scholarships managed by this coordinator
$stmt = $conn->prepare("SELECT * FROM Scholarship WHERE coordinator_id = ?");
$stmt->bind_param("s", $coordinator_id);
$stmt->execute();
$result = $stmt->get_result();

// Handle delete request
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    // First delete dependent records
    $conn->query("DELETE sp FROM Scholarship_Payment sp JOIN Application a ON sp.award_id = a.application_id WHERE a.scholarship_id = '$id'");
    $conn->query("DELETE FROM Scholarship_Awarded WHERE application_id IN (SELECT application_id FROM Application WHERE scholarship_id = '$id')");
    $conn->query("DELETE FROM Notification WHERE application_id IN (SELECT application_id FROM Application WHERE scholarship_id = '$id')");
    $conn->query("DELETE FROM Application WHERE scholarship_id = '$id'");
    $conn->query("DELETE FROM Eligibility_Criteria WHERE scholarship_id = '$id'");
    $conn->query("DELETE FROM Scholarship WHERE scholarship_id = '$id'");

    header("Location: manage_scholarships.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Scholarships</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        table {
            margin: 20px auto;
            border-collapse: collapse;
            width: 80%;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #2980b9;
            color: white;
        }

        .btn-delete {
            padding: 6px 12px;
            background-color: crimson;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-delete:hover {
            background-color: darkred;
        }

        .back-btn {
            display: block;
            width: 200px;
            margin: 20px auto;
            padding: 10px;
            background-color: #2980b9;
            color: white;
            border-radius: 5px;
            text-align: center;
            text-decoration: none;
        }
    </style>
</head>
<body>

<h1 style="text-align:center;">University of Jaffna</h1>
<h2 style="text-align:center;">Manage Scholarships</h2>

<?php if ($result->num_rows === 0): ?>
    <p style="text-align:center;">No scholarships found.</p>
<?php else: ?>
    <table>
        <tr>
            <th>Scholarship Name</th>
            <th>Amount</th>
            <th>Deadline</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['amount']) ?></td>
                <td><?= htmlspecialchars($row['deadline']) ?></td>
                <td>
                    <a href="manage_scholarships.php?delete=<?= $row['scholarship_id'] ?>" onclick="return confirm('Are you sure you want to delete this scholarship?')">
                        <button class="btn-delete">Delete</button>
                    </a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
<?php endif; ?>

<a href="coordinator_dashboard.php" class="back-btn">⬅️ Back to Dashboard</a>

</body>
</html>
