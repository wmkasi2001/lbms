<?php
session_start();
if (!isset($_SESSION['U_name']) || !$_SESSION['is_admin']) {
    header("Location: student_login.php");
    exit();
}

include 'db_connect.php';

if (isset($_POST['add_book'])) {
    $isbn = $_POST['isbn'];
    $author = $_POST['author'];
    $title = $_POST['title'];
    $accession_number = $_POST['accession_number'];
    $added_by = $_SESSION['U_name'];

    $sql = "INSERT INTO books (isbn, author, title, accession_number, added_by) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $isbn, $author, $title, $accession_number, $added_by);

    if ($stmt->execute()) {
        echo "Book added successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

if (isset($_POST['delete_book'])) {
    $isbn = $_POST['isbn'];

    $sql = "DELETE FROM books WHERE isbn = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $isbn);

    if ($stmt->execute()) {
        echo "Book deleted successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
