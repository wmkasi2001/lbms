<?php
session_start();
include 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['U_name'])) {
    header("Location: student_login.php");
    exit();
}

$u_name = $_SESSION['U_name'];

if (isset($_GET['ISBN']) || isset($_GET['barcode'])) {
    // Get ISBN or barcode from GET request
    $isbn = isset($_GET['ISBN']) ? $_GET['ISBN'] : null;
    $barcode = isset($_GET['barcode']) ? $_GET['barcode'] : null;
    $book_id = $isbn ? $isbn : $barcode;  // Use ISBN if available, else use barcode

    // Check if book_id is not null
    if ($book_id) {
        // Insert the borrowing request using ISBN or Barcode
        $sql = "INSERT INTO requests (student_name, student_id, book_id, status, request_date) 
                VALUES (?, ?, ?, 'pending', NOW())";
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            die('Prepare failed: ' . htmlspecialchars($conn->error));
        }

        // Bind U_name as student_id
        $stmt->bind_param("sss", $u_name, $u_name, $book_id);
        
        if ($stmt->execute()) {
            $message = "Your request has been submitted for approval. Please wait.";
        } else {
            // Detailed error message
            $message = "Failed to submit the request. Error: " . htmlspecialchars($stmt->error);
        }

        $stmt->close();
    } else {
        $message = "No book selected for borrowing.";
    }
} else {
    $message = "No book selected for borrowing.";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="student_dashboard.css">
    <title>Borrow Request Submitted</title>
</head>
<body>
    <header>
        <div class="logo">
            <img src="Images/lbmslogo.png" alt="Library Logo">
        </div>
        <nav>
            <ul>
                <li><a href="student_dashboard.php">STUDENT DASHBOARD</a></li>
                <li><a href="logout.php">LOGOUT</a></li>
            </ul>
        </nav>
    </header>

    <section>
        <div class="box">
            <h1>Borrow Request</h1>
            <p><?php echo htmlspecialchars($message); ?></p>
            <p>You will be redirected to the dashboard in 5 seconds...</p>
            <script>
                setTimeout(function(){
                    window.location.href = "student_dashboard.php";
                }, 5000);
            </script>
        </div>
    </section>

    <footer>
        &copy; 2024 Online Library Management System
    </footer>
</body>
</html>
