<?php
include 'db_connect.php';

// Fetch all books
$sql = "SELECT * FROM books";
$all_books_result = $conn->query($sql);

if ($all_books_result->num_rows > 0) {
    while ($row = $all_books_result->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($row['isbn']) . '</td>';
        echo '<td>' . htmlspecialchars($row['author']) . '</td>';
        echo '<td>' . htmlspecialchars($row['title']) . '</td>';
        echo '<td>' . htmlspecialchars($row['accession_number']) . '</td>';
        echo '<td>' . htmlspecialchars($row['added_by']) . '</td>';
        echo '<td>' . htmlspecialchars($row['date_added']) . '</td>';
        echo '<td>';

        // Check if the book is borrowed
        $borrow_sql = "SELECT request_date FROM requests WHERE book_id = ? AND return_date IS NULL";
        if ($borrow_stmt = $conn->prepare($borrow_sql)) {
            $borrow_stmt->bind_param("s", $row['isbn']);
            $borrow_stmt->execute();
            $borrow_result = $borrow_stmt->get_result();
            // If there are results, the book is currently borrowed
            if ($borrow_result->num_rows > 0) {
                echo "Borrowed";
            } else {
                echo "Available";
            }
            $borrow_stmt->close();
        } else {
            echo "Error: " . $conn->error;
        }

        echo '</td>';
        echo '</tr>';
    }
} else {
    echo '<tr><td colspan="7">No books available.</td></tr>';
}

$conn->close();
?>
