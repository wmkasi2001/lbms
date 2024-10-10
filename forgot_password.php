<?php
include 'db_connect.php'; // Include the database connection

// Initialize variables
$emailSent = false;
$errorMessage = '';
$resetLink = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // Prepare SQL query to check if the email exists
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        // Handle SQL preparation error
        $errorMessage = "Failed to prepare the SQL statement: " . $conn->error;
    } else {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Email exists, generate reset token and send reset link
            $user = $result->fetch_assoc();
            $userId = $user['U_name']; // Use U_name to identify the user

            // Generate a unique reset token
            $token = bin2hex(random_bytes(32));
            $expires = date("Y-m-d H:i:s", strtotime('+1 hour'));

            // Store the reset token in the database
            $sql = "INSERT INTO password_resets (user_id, token, expires) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            if ($stmt === false) {
                $errorMessage = "Failed to prepare the SQL statement for token insertion: " . $conn->error;
            } else {
                $stmt->bind_param("sss", $userId, $token, $expires);
                if ($stmt->execute()) {
                    // Construct the reset link
                    $resetLink = "http://localhost/LBMS2024/reset_password.php?token=$token";
                    $emailSent = true;
                } else {
                    $errorMessage = "Failed to execute the SQL statement for token insertion: " . $stmt->error;
                }
                $stmt->close();
            }
        } else {
            $errorMessage = "No account found with that email address.";
        }
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="password.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Forgot Password</title>
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
            <h2>Forgot Password</h2>
            <form action="forgot_password.php" method="POST">
                <input class="form-control" type="text" name="email" placeholder="Enter your email" required>
                <input class="btn" type="submit" value="Reset Password">
            </form>
            <?php if ($emailSent): ?>
                <p class="success-message">A password reset link has been sent to your email address.</p>
                <a href="<?php echo htmlspecialchars($resetLink); ?>" class="btn">Go to Reset Page</a>
            <?php elseif ($errorMessage): ?>
                <p class="error-message"><?php echo htmlspecialchars($errorMessage); ?></p>
            <?php endif; ?>
        </div>
    </section>

    <footer>
        &copy; 2024 Online Library Management System
    </footer>
</body>
</html>
