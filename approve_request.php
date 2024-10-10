<?php
session_start();
if (!isset($_SESSION['U_name']) || !$_SESSION['is_admin']) {
    header("Location: admin_login.php");
    exit();
}

include 'db_connect.php';

$message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $request_id = $_POST['request_id'];
    $return_date = $_POST['return_date'];

    // Update the request status to "approved"
    $sql = "UPDATE requests SET status = 'approved', return_date = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $return_date, $request_id);

    if ($stmt->execute()) {
        $message = "Request successfully approved.";
    } else {
        $message = "Error approving request: " . $stmt->error;
    }

    $stmt->close();
}

if (isset($_GET['message'])) {
    $message = htmlspecialchars($_GET['message']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="approve_request.css">
    <title>Approve Request</title>
</head>
<body>
    <header>
        <div class="logo">
            <img src="Images/lbmslogo.png" alt="Library Logo">
            <nav>
                <ul>
                    <li><a href="admin_dashboard.php">Admin Dashboard</a></li>
                    <li><a href="admin_books.php">View Books</a></li>
                    <li><a href="notifications.php">Notifications</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <section>
        <h1>Book Approval</h1>
        <?php if ($message): ?>
            <p style="color: green;"><?php echo $message; ?></p>
        <?php else: ?>
            <p>No message to display.</p>
        <?php endif; ?>

        <h2>Approve Book Request</h2>
        <form method="post">
            <label for="request_id">Request ID:</label>
            <input type="text" name="request_id" required>
            <label for="return_date">Return Date:</label>
            <input type="date" name="return_date" required>
            <button type="submit">Approve Request</button>
        </form>
    </section>

    <footer>
        &copy; 2024 Online Library Management System
    </footer>
</body>
</html>
