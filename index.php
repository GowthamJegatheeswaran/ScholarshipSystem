<?php
session_start();
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'student') {
        header("Location: student_dashboard.php");
        exit;
    } elseif ($_SESSION['role'] === 'coordinator') {
        header("Location: coordinator_dashboard.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Scholarship Management System</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .main-buttons {
            text-align: center;
            margin-top: 40px;
        }
        .main-buttons a {
            display: inline-block;
            margin: 10px;
            padding: 12px 25px;
            background-color: #2980b9;
            color: white;
            text-decoration: none;
            font-size: 16px;
            border-radius: 5px;
        }
        .main-buttons a:hover {
            background-color: #1e6fa4;
        }
    </style>
</head>
<body>

    <h1>University of Jaffna <span>Scholarship Management System</span></h1>
    <hr>

    <div class="main-buttons">
        <a href="login.php">Login</a>
        <a href="register_student.php">Student Registration</a>
    </div>

    <footer>
        <p>&copy; 2025 University of Jaffna. All rights reserved.</p>
    </footer>

</body>
</html>
