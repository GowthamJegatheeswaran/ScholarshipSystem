<?php
include 'db.php';
$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = $_POST['student_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $dept = $_POST['department'];
    $dob = $_POST['dob'];
    $password = $_POST['password'];

    // Validate student ID format
    if (!preg_match("/^20\d{2}\/E\/\d{3}$/", $student_id)) {
        $message = "<p class='error'>Invalid Student ID format. Use: 20XX/E/NNN</p>";
    } else {
        // Check for duplicates
        $stmt = $conn->prepare("SELECT * FROM Student WHERE student_id = ? OR email = ?");
        $stmt->bind_param("ss", $student_id, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $message = "<p class='error'>Student ID or Email already exists.</p>";
        } else {
            // Hash password before storing
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert into Student table
            $stmt = $conn->prepare("INSERT INTO Student (student_id, name, email, dept, dob, password) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $student_id, $name, $email, $dept, $dob, $hashedPassword);

            if ($stmt->execute()) {
                $message = "<p class='success'>Student registered successfully!</p>";
            } else {
                $message = "<p class='error'>Error: Could not register student.</p>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Registration</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<h1>University of Jaffna <span>Scholarship Management System</span></h1>
<hr>

<h2>Student Registration</h2>

<?= $message ?>

<form method="POST">
    <label>Student ID (20XX/E/NNN):</label>
    <input type="text" name="student_id" required placeholder="e.g., 2022/E/001">

    <label>Full Name:</label>
    <input type="text" name="name" required>

    <label>Email Address:</label>
    <input type="email" name="email" required>

    <label>Department:</label>
    <input type="text" name="department" required>

    <label>Date of Birth:</label>
    <input type="date" name="dob" required>

    <label>Password:</label>
    <input type="password" name="password" required>

    <input type="submit" value="Register">
</form>

<a href="login.php"><button class="back-btn">Back to Login</button></a>

<footer>
    <p>&copy; 2025 University of Jaffna. All rights reserved.</p>
</footer>

</body>
</html>
