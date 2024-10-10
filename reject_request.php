<?php
session_start();
if (!isset($_SESSION['U_name']) || !$_SESSION['is_admin']) {
    header("Location: admin_login.php");
    exit();
}

include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $request_id = $_POST['request_id'];

    // Update the request status to "rejected"
    $sql = "UPDATE requests SET status = 'rejected' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $request_id);

    if ($stmt->execute()) {
        header("Location: notifications.php?message=Request successfully rejected.");
        exit();
    } else {
        echo "Error rejecting request: " . $stmt->error;
    }

    $stmt->close();
}
?>
