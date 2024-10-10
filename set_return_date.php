<?php
session_start();
if (!isset($_SESSION['U_name']) || !$_SESSION['is_admin']) {
    header("Location: admin_login.php");
    exit();
}

include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $request_id = $_POST['request_id'];
    $return_date = $_POST['return_date'];

    // Update the request status to "approved" and set the return date
    $sql = "UPDATE requests SET status = 'approved', return_date = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $return_date, $request_id);

    if ($stmt->execute()) {
        header("Location: notifications.php?message=Request approved and return date set.");
        exit();
    } else {
        echo "Error setting return date: " . $stmt->error;
    }

    $stmt->close();
} elseif (isset($_GET['request_id'])) {
    $request_id = $_GET['request_id'];
} else {
    echo "Invalid request.";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="style.css">
    <title>Set Return Date</title>
</head>
<body>
    <header>
        <div class="logo">
            <img src="Images/lbmslogo.png" alt="Library Logo">
        </div>
        <nav>
            <ul>
                <li><a href="admin_dashboard.php">Admin Dashboard</a></li>
                <li><a href="admin_books.php">View Books</a></li>
                <li><a href="notifications.php">Notifications</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <section>
        <h1>Set Return Date</h1>
        <form method="POST" action="">
            <input type="hidden" name="request_id" value="<?php echo htmlspecialchars($request_id); ?>">
            <label for="return_date">Return Date:</label>
            <input type="date" name="return_date" id="return_date" required>
            <input type="submit" value="Set Return Date" class="btn">
        </form>
    </section>

    <footer>
        &copy; 2024 Online Library Management System
    </footer>
</body>
</html>
