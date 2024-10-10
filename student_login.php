<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the form fields are set
    if (isset($_POST['u_name']) && isset($_POST['password'])) {
        $u_name = $_POST['u_name'];
        $password = $_POST['password'];

        // Prepare and execute the SQL query
        $sql = "SELECT * FROM users WHERE U_name = ? AND password = ?";
        $stmt = $conn->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("ss", $u_name, $password);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                $_SESSION['U_name'] = $user['U_name'];
                $_SESSION['is_admin'] = $user['is_admin'];

                // Redirect based on user type
                if ($user['is_admin']) {
                    header("Location: admin_dashboard.php"); // Redirect to admin dashboard
                } else {
                    header("Location: student_dashboard.php"); // Redirect to student dashboard
                }
                exit();
            } else {
                $error = "Invalid login credentials.";
            }

            $stmt->close();
        } else {
            $error = "Error preparing the SQL statement.";
        }
    } else {
        $error = "Please fill in all required fields.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="student_login.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Student Login</title>
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
            <h2>Student Login Form</h2>
            <form name="login" action="" method="POST">
                <input class="form-control" type="text" name="u_name" placeholder="ID Number" required>
                <input class="form-control" type="password" name="password" placeholder="Password" required>
                <input class="btn" type="submit" name="submit" value="Login">
                <?php if (isset($error)): ?>
                    <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
                <?php endif; ?>
                <p>
                    <a href="forgot_password.php">Forgot password?</a>
                    &nbsp;&nbsp;
                    New to this website?
                    <a href="registration.php">Sign Up</a>
                </p>
            </form>
        </div>
    </section>

    <footer>
        &copy; 2024 Online Library Management System
    </footer>
</body>
</html>
