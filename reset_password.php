<?php
include 'db_connect.php'; // Include the database connection

$tokenValid = false;
$resetSuccess = false;
$errorMessage = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST['token'];
    $newPassword = $_POST['password'];

    // Validate token
    $sql = "SELECT user_id FROM password_resets WHERE token = ? AND expires > NOW()";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        $errorMessage = "Failed to prepare the SQL statement: " . $conn->error;
    } else {
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $userId = $result->fetch_assoc()['user_id'];

            // Update the password
            $sql = "UPDATE users SET password = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT); // Hash the password before storing
            $stmt->bind_param("si", $hashedPassword, $userId);

            if ($stmt->execute()) {
                // Remove the token from the database
                $sql = "DELETE FROM password_resets WHERE token = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $token);
                $stmt->execute();

                $resetSuccess = true;
            } else {
                $errorMessage = "Failed to update the password: " . $stmt->error;
            }
        } else {
            $errorMessage = "Invalid or expired token.";
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="password.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password</title>
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
            <h2>Reset Password</h2>
            <?php if ($resetSuccess): ?>
                <p>Your password has been successfully reset. You can now <a href="student_login.php">log in</a> with your new password.</p>
            <?php else: ?>
                <form action="" method="POST">
                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token']); ?>">
                    <input class="form-control" type="password" name="password" placeholder="New Password" required>
                    <input class="btn" type="submit" value="Reset Password">
                </form>
                <?php if ($errorMessage): ?>
                    <p class="error-message"><?php echo htmlspecialchars($errorMessage); ?></p>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </section>

    <footer>
        &copy; 2024 Online Library Management System
    </footer>
</body>
</html>
