<?php
include 'db_connect.php';

$search_result = [];

// Handle the search request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $search_query = trim($_POST['search_query']);
    if (!empty($search_query)) {
        $sql = "SELECT * FROM books WHERE title LIKE ? OR isbn LIKE ? OR barcode LIKE ?";
        $search_query = "%{$search_query}%";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die('Prepare failed: ' . htmlspecialchars($conn->error));
        }
        $stmt->bind_param("sss", $search_query, $search_query, $search_query);
        $stmt->execute();
        $result = $stmt->get_result();
        $all_books_result = $result;
    }
}


// Fetch all books
$sql = "SELECT * FROM books";
$all_books_result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="books.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Books List</title>
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
        <h1>Books List</h1>
        
        <form method="POST" action="">
            <input type="text" name="title" placeholder="Enter book title" required>
            <input type="submit" value="Search">
        </form>
        
        <?php if (!empty($search_result)): ?>
            <h2>Search Results</h2>
            <table>
                <thead>
                    <tr>
                        <th>ISBN</th>
                        <th>Author</th>
                        <th>Title</th>
                        <th>Accession Number</th>
                        <th>Borrow Date</th>
                        <th>Return Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($search_result as $row): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['isbn']); ?></td>
                            <td><?php echo htmlspecialchars($row['author']); ?></td>
                            <td><?php echo htmlspecialchars($row['title']); ?></td>
                            <td><?php echo htmlspecialchars($row['accession_number']); ?></td>
                            <td><?php echo htmlspecialchars($row['borrow_date']); ?></td>
                            <td><?php echo htmlspecialchars($row['return_date']); ?></td>
                            <td><?php echo htmlspecialchars($row['status']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <h2>All Books</h2>
        <table>
            <thead>
                <tr>
                    <th>ISBN</th>
                    <th>Author</th>
                    <th>Title</th>
                    <th>Accession Number</th>
                    <th>Added By</th>
                    <th>Date Added</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($all_books_result->num_rows > 0): ?>
                    <?php while ($row = $all_books_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['isbn']); ?></td>
                            <td><?php echo htmlspecialchars($row['author']); ?></td>
                            <td><?php echo htmlspecialchars($row['title']); ?></td>
                            <td><?php echo htmlspecialchars($row['accession_number']); ?></td>
                            <td><?php echo htmlspecialchars($row['added_by']); ?></td>
                            <td><?php echo htmlspecialchars($row['date_added']); ?></td>
                            <td>
                                <?php 
                                // Determine if the book is borrowed
                                $borrow_sql = "SELECT request_date FROM requests WHERE book_id = ? AND return_date IS NULL";
                                if ($borrow_stmt = $conn->prepare($borrow_sql)) {
                                    $borrow_stmt->bind_param("s", $row['isbn']);
                                    $borrow_stmt->execute();
                                    $borrow_result = $borrow_stmt->get_result();
                                    if ($borrow_result->num_rows > 0) {
                                        echo "Borrowed";
                                    } else {
                                        echo "Available";
                                    }
                                    $borrow_stmt->close();
                                } else {
                                    echo "Error: " . $conn->error;
                                }
                                ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">No books available.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </section>

    <footer>
        &copy; 2024 Online Library Management System
    </footer>
</body>
</html>

<?php
$conn->close();
?>
