<?php
// Start session and check for admin access
session_start();
if (!isset($_SESSION['U_name']) || !$_SESSION['is_admin']) {
    header("Location: admin_login.php");
    exit();
}

// Include database connection
include 'db_connect.php';

// Fetch all books to export
$sql = "SELECT ISBN, author, title, accession_number, added_by, date_added, barcode, category FROM books";
$result = $conn->query($sql);

// Check if there are results
if ($result->num_rows > 0) {
    // Define the headers for the Excel file
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=books_list.xls");

    // Output column names
    echo "ISBN\tAuthor\tTitle\tAccession Number\tAdded By\tDate Added\tBarcode\tCategory\n";

    // Output the data
    while ($row = $result->fetch_assoc()) {
        echo implode("\t", $row) . "\n"; // Tab-separated values
    }
} else {
    echo "No data available to export.";
}

$conn->close();
?>
