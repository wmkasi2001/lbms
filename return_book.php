<?php
session_start();
include 'db_connect.php';

// Check if request_id is passed in the URL
if (isset($_GET['request_id'])) {
    $request_id = intval($_GET['request_id']); // Get the request ID from the URL

    // Update the request status to 'returned' and set the return date to the current date
    $sql = "UPDATE requests SET status = 'returned', return_date = NOW() WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }

    $stmt->bind_param("i", $request_id);
    if ($stmt->execute()) {
        // Redirect back to the student dashboard after successfully returning the book
        header("Location: student_dashboard.php?return_status=success");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
} else {
    // If no request_id is passed
    header("Location: student_dashboard.php?return_status=error");
    exit();
}

$conn->close();
?>
