<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $u_name = $_POST['u_name'];
    $password = $_POST['password'];

    // SQL query to check if the user exists and is an admin
    $sql = "SELECT * FROM users WHERE U_name = ? AND password = ? AND is_admin = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $u_name, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['U_name'] = $user['U_name'];
        $_SESSION['is_admin'] = true;

        // Redirect to admin dashboard
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $error = "Invalid credentials or not an admin.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="admin_login.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login</title>
    <script src="validation.js"></script>
</head>
<body>
    <header>
        <div class="logo">
            <img src="Images/lbmslogo.png" alt="Library Logo">
        </div>
        <nav>
            <ul>
                <li><a href="dashboard.php">HOME</a></li>
                <li><a href="books.php">BOOKS</a></li>
                <li><a href="student_login.php">STUDENT LOGIN</a></li>
                <li><a href="admin_login.php">ADMIN LOGIN</a></li>
                <li><a href="feedback.php">FEEDBACK</a></li>
            </ul>
        </nav>
    </header>
    
    <section>
        <div class="box">
            <h1>Library Management System</h1>
            <h2>Admin Login Form</h2>
            <form name="login" action="" method="POST">
                <input class="form-control" type="text" name="u_name" placeholder="ID Number" required>
                <input class="form-control" type="password" name="password" placeholder="Password" required>
                <input class="btn" type="submit" name="submit" value="Login">
                <p>
                    <a href="forgot_password.php">Forgot password?</a>
                    &nbsp;&nbsp;
                    New to this website?
                   <a href="admin_registration.php">Register as Admin</a>

                </p>
                 <?php if (isset($error)) { echo "<p style='color:red;'>$error</p>"; } ?>
            </form>
        </div>
    </section>

    <footer>
        &copy; 2024 Online Library Management System
    </footer>
</body>
</html>
