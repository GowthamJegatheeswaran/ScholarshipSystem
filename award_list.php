<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'coordinator') {
    header("Location: login.php");
    exit;
}

$coordinator_id = $_SESSION['user'];

// ✅ Handle payment submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['pay_now'])) {
    $award_id = $_POST['award_id'];
    $amount = $_POST['amount'];
    $year = $_POST['year'];
    $month = $_POST['month'];
    $reference = $_POST['reference'];

    $month_year = $month . ' ' . $year;

    $check = $conn->prepare("SELECT * FROM Scholarship_Payment WHERE award_id = ? AND payment_month = ?");
    $check->bind_param("is", $award_id, $month_year);
    $check->execute();
    $res = $check->get_result();

    if ($res->num_rows === 0) {
        $stmt = $conn->prepare("INSERT INTO Scholarship_Payment 
            (award_id, payment_date, amount, transfer_reference, payment_month) 
            VALUES (?, NOW(), ?, ?, ?)");
        $stmt->bind_param("idss", $award_id, $amount, $reference, $month_year);
        if ($stmt->execute()) {
            $appRes = $conn->prepare("SELECT application_id FROM Scholarship_Awarded WHERE award_id = ?");
            $appRes->bind_param("i", $award_id);
            $appRes->execute();
            $appRow = $appRes->get_result()->fetch_assoc();

            if ($appRow) {
                $application_id = $appRow['application_id'];
                $message = "Scholarship payment for $month_year has been credited. Ref: $reference.";
                $notif = $conn->prepare("INSERT INTO Notification (application_id, notif_date, status_message) VALUES (?, NOW(), ?)");
                $notif->bind_param("is", $application_id, $message);
                $notif->execute();
            }
        }
    }
}

// ✅ Get awarded scholarships only under coordinator's control
$sql = "
SELECT 
    sa.award_id,
    s.name AS student_name,
    sc.name AS scholarship_name,
    sc.amount AS monthly_amount,
    sa.award_date
FROM Scholarship_Awarded sa
JOIN Application a ON sa.application_id = a.application_id
JOIN Student s ON a.student_id = s.student_id
JOIN Scholarship sc ON a.scholarship_id = sc.scholarship_id
WHERE sc.coordinator_id = ?
ORDER BY sa.award_date DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $coordinator_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Awarded Scholarships – Monthly Payments</title>
    <style>
        body { font-family: Arial; background: #f4f4f4; padding: 20px; }
        h2 { color: #333; }
        .award-block { background: #fff; border: 1px solid #ccc; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
        .green-text { color: green; font-weight: bold; }
        .form-group { margin-bottom: 10px; }
        input[type="text"], input[type="number"], select { padding: 5px; width: 250px; }
        input[type="submit"] { padding: 8px 16px; background: #2980b9; color: white; border: none; border-radius: 5px; }
        input[type="submit"]:hover { background: #1e6fa4; }
    </style>
</head>
<body>

<h2>Awarded Scholarships – Monthly Payments</h2>

<?php if ($result->num_rows === 0): ?>
    <p>No awarded scholarships found under your coordination.</p>
<?php else: ?>
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="award-block">
            <strong>Student:</strong> <?= htmlspecialchars($row['student_name']) ?><br>
            <strong>Scholarship:</strong> <?= htmlspecialchars($row['scholarship_name']) ?><br>
            <strong>Monthly Amount:</strong> Rs.<?= htmlspecialchars($row['monthly_amount']) ?><br>
            <strong>Award Date:</strong> <?= htmlspecialchars($row['award_date']) ?><br><br>

            <?php
                $aid = $row['award_id'];
                $paidMonths = [];

                $paid_q = $conn->query("SELECT payment_month FROM Scholarship_Payment WHERE award_id = $aid");
                while ($pm = $paid_q->fetch_assoc()) {
                    $paidMonths[] = $pm['payment_month'];
                }

                $allMonths = ['January','February','March','April','May','June','July','August','September','October','November','December'];
                $selectedYear = isset($_POST['year']) ? $_POST['year'] : date('Y');

                $unpaidMonths = [];
                foreach ($allMonths as $m) {
                    $combo = $m . ' ' . $selectedYear;
                    if (!in_array($combo, $paidMonths)) {
                        $unpaidMonths[] = $m;
                    }
                }
            ?>

            <?php if (count($unpaidMonths) > 0): ?>
                <form method="POST">
                    <input type="hidden" name="award_id" value="<?= $aid ?>">
                    <div class="form-group">
                        <label>Year:
                            <select name="year" required>
                                <?php
                                $currentYear = date('Y');
                                for ($y = $currentYear - 1; $y <= $currentYear + 1; $y++) {
                                    $sel = ($selectedYear == $y) ? "selected" : "";
                                    echo "<option value='$y' $sel>$y</option>";
                                }
                                ?>
                            </select>
                        </label>
                    </div>
                    <div class="form-group">
                        <label>Month:
                            <select name="month" required>
                                <?php foreach ($unpaidMonths as $m): ?>
                                    <option value="<?= $m ?>"><?= $m ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                    </div>
                    <div class="form-group">
                        <label>Amount:
                            <input type="number" name="amount" value="<?= $row['monthly_amount'] ?>" required>
                        </label>
                    </div>
                    <div class="form-group">
                        <label>Transfer Reference:
                            <input type="text" name="reference" required>
                        </label>
                    </div>
                    <input type="submit" name="pay_now" value="Pay Scholarship">
                </form>
            <?php else: ?>
                <p style="color:gray;">✅ All months paid for <?= $selectedYear ?>.</p>
            <?php endif; ?>

            <strong>Paid Months:</strong>
            <?php if (count($paidMonths) == 0): ?>
                None
            <?php else: ?>
                <ul>
                    <?php
                        usort($paidMonths, function($a, $b) {
                            return strtotime($a) - strtotime($b);
                        });
                        foreach ($paidMonths as $p): ?>
                            <li class="green-text">✅ <?= $p ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    <?php endwhile; ?>
<?php endif; ?>

<a href="coordinator_dashboard.php">
    <button style="padding:10px 20px; background:#2980b9; color:white; border:none; border-radius:5px;">⬅️ Back</button>
</a>

</body>
</html>
