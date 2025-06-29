<?php
session_start();
include 'db.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Try student login
    $stmt = $conn->prepare("SELECT student_id, name, password FROM Student WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $student_result = $stmt->get_result();

    if ($student_result->num_rows > 0) {
        $student = $student_result->fetch_assoc();
        if (password_verify($password, $student['password']) || $password === $student['password']) {
            $_SESSION['user'] = $student['student_id'];
            $_SESSION['role'] = 'student';
            header("Location: student_dashboard.php");
            exit;
        } else {
            $message = "<p class='error'>Invalid student password.</p>";
        }
    } else {
        // Try coordinator login
        $stmt = $conn->prepare("SELECT coordinator_id, name, password FROM Coordinator WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $coord_result = $stmt->get_result();

        if ($coord_result->num_rows > 0) {
            $coord = $coord_result->fetch_assoc();

            // Accept hashed or plain-text passwords
            if (password_verify($password, $coord['password']) || $password === $coord['password']) {
                $_SESSION['user'] = $coord['coordinator_id'];
                $_SESSION['role'] = 'coordinator';
                header("Location: coordinator_dashboard.php");
                exit;
            } else {
                $message = "<p class='error'>Invalid coordinator password.</p>";
            }
        } else {
            $message = "<p class='error'>Email not found.</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .login-form {
            max-width: 400px;
            margin: 40px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 8px rgba(0, 0, 0, 0.1);
        }

        .login-form label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        .login-form input[type="email"],
        .login-form input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 16px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .login-form input[type="submit"] {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 6px;
            width: 100%;
            font-size: 16px;
            cursor: pointer;
        }

        .login-form input[type="submit"]:hover {
            background-color: #0056b3;
        }

        .error {
            color: red;
            text-align: center;
        }

        h1 {
            text-align: center;
        }
    </style>
</head>
<body>

<h1>University of Jaffna <br><span>Scholarship Management System</span></h1>
<hr>

<div class="login-form">
    <h2>Login</h2>

    <?= $message ?>

    <form method="POST">
        <label>Email:</label>
        <input type="email" name="email" required>

        <label>Password:</label>
        <input type="password" name="password" required>

        <input type="submit" value="Login">
    </form>
</div>

<footer>
    <p style="text-align: center;">&copy; 2025 University of Jaffna. All rights reserved.</p>
</footer>

</body>
</html>
