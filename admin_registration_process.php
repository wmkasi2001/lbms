<?php
include 'db_connect.php'; // Include the database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $f_name = $_POST['f_name'];
    $l_name = $_POST['l_name'];
    $U_name = $_POST['U_name']; // Username field for admin
    $password = $_POST['password'];
    $email = $_POST['email'];
    $is_admin = 1; // Set flag for admin

    // SQL query to insert admin
    $sql = "INSERT INTO users (Fname, Lname, U_name, password, email, is_admin) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $f_name, $l_name, $U_name, $password, $email, $is_admin);

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        // Redirect to admin login page after successful registration
        header("Location: admin_login.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
