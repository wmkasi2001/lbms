<?php
session_start();
if (!isset($_SESSION['U_name']) || !$_SESSION['is_admin']) {
    header("Location: admin_login.php");
    exit();
}

include 'db_connect.php';

$message = ""; // Initialize the message variable

if (isset($_POST['add_book'])) {
    $ISBN = trim($_POST['ISBN']);
    $barcode = trim($_POST['barcode']);
    $author = trim($_POST['author']);
    $title = trim($_POST['title']);
    $accession_number = trim($_POST['accession_number']);
    $added_by = trim($_POST['added_by']);
    $date_added = trim($_POST['date_added']);
    $category = trim($_POST['category']);

    // Check that all required fields are filled out
    if (!empty($ISBN) && !empty($barcode) && !empty($author) && !empty($title) && !empty($accession_number) && !empty($added_by) && !empty($category)) {
        // Check if the book already exists
        $check_sql = "SELECT * FROM books WHERE ISBN = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $ISBN);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
        
        } else {
            // Proceed to insert the new book
            $sql = "INSERT INTO books (ISBN, barcode, author, title, accession_number, added_by, date_added, category) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssssss", $ISBN, $barcode, $author, $title, $accession_number, $added_by, $date_added, $category);

            if ($stmt->execute()) {
                $message = "Book added successfully!";
            } else {
                $message = "Error: " . $stmt->error;
            }
            $stmt->close();
        }
        $check_stmt->close();
    } else {
        $message = "All required fields must be filled out."; // Show this message if fields are empty
    }
}

if (isset($_POST['delete_book'])) {
    $ISBN = trim($_POST['ISBN']);

    if (!empty($ISBN)) {
        $sql = "DELETE FROM books WHERE ISBN = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $ISBN);

        if ($stmt->execute()) {
            $message = "Book deleted successfully!";
        } else {
            $message = "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $message = "ISBN field is required.";
    }
}

// Fetch all books to display them in the table, sorted from newest to oldest
$sql = "SELECT ISBN, author, title, accession_number, added_by, date_added, barcode, category 
        FROM books 
        ORDER BY date_added DESC"; // Sort by date_added in descending order
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="admin_dashboard.css">
    <title>Admin Dashboard</title>
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
        <div class="box">
            <h1>Admin Dashboard</h1>
            <?php if (!empty($message)) { echo "<p>$message</p>"; } ?> <!-- Show message only if it's set -->

            <h2>Add New Book</h2>
            <form method="POST" action="">
                <input class="form-control" type="text" name="ISBN" placeholder="ISBN" required>
                <input class="form-control" type="text" name="barcode" placeholder="Barcode" required>
                <input class="form-control" type="text" name="author" placeholder="Author" required>
                <input class="form-control" type="text" name="title" placeholder="Title" required>
                <input class="form-control" type="text" name="accession_number" placeholder="Accession Number" required>
                <input class="form-control" type="text" name="added_by" placeholder="Added by" required>
                <input class="form-control" type="date" name="date_added" placeholder="Date Added" required>
                <select class="form-control" name="category" required>
                    <option value="">Select Category</option>
                    <option value="Computer (000 - 099)">Computer (000 - 099)</option>
                    <option value="Philosophy & Psychology (100 - 199)">Philosophy & Psychology (100 - 199)</option>
                    <option value="Religion (200 - 299)">Religion (200 - 299)</option>
                    <option value="Social Science (300 - 399)">Social Science (300 - 399)</option>
                    <option value="Language (400 - 499)">Language (400 - 499)</option>
                    <option value="Natural Sciences & Mathematics (500 - 599)">Natural Sciences & Mathematics (500 - 599)</option>
                    <option value="Technology (Applied Science) (600 - 650)">Technology (Applied Science) (600 - 650)</option>
                    <option value="Business Accounting & Manufacturing (651 - 699)">Business Accounting & Manufacturing (651 - 699)</option>
                    <option value="The Arts (700 - 799)">The Arts (700 - 799)</option>
                    <option value="Literature (800 - 890)">Literature (800 - 890)</option>
                    <option value="Geography (900 - 999)">Geography (900 - 999)</option>
                </select>
                <input class="btn" type="submit" name="add_book" value="Add Book">
            </form>

            <h2>Delete Book</h2>
            <form method="POST" action="">
                <input class="form-control" type="text" name="ISBN" placeholder="ISBN of Book to Delete" required>
                <input class="btn" type="submit" name="delete_book" value="Delete Book">
            </form>
        </div>
    </section>

    <style>
        .books-table-container {
            width: 100%;
            overflow-x: auto;
            margin-top: 20px;
            background-color: rgba(225, 225, 225, 0.8);
            padding: 20px;
            border-radius: 8px;
        }

        .search-container {
            text-align: center;
            margin-bottom: 20px;
        }

        .search-bar {
            padding: 8px;
            width: 300px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }

        .books-table-container th, .books-table-container td {
            padding: 10px;
            text-align: center;
        }
    </style>

        <h2 style="text-align: center;">Current Books</h2>

    <div class="search-container">
        <input type="text" id="searchBar" class="search-bar" placeholder="Search by ISBN or Barcode...">
    </div>

    <table class="books-table-container" border="1">
        <thead>
            <tr>
                <th>ISBN</th>
                <th>Author</th>
                <th>Title</th>
                <th>Accession Number</th>
                <th>Added By</th>
                <th>Date Added</th>
                <th>Barcode</th>
                <th>Category</th>
            </tr>
        </thead>
        <tbody id="booksTable">
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['ISBN']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['author']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['title']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['accession_number']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['added_by']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['date_added']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['barcode']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['category']) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='8'>No books found.</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <!-- Export Button -->
    <div style="text-align: center; margin-top: 20px;">
        <a href="export_books.php" class="btn">Export to Excel</a>
    </div>

    <footer>
        <p>&copy; 2024 Don Bosco Technological Institute. All rights reserved.</p>
    </footer>


    <script>
        // Search functionality
        document.getElementById('searchBar').addEventListener('keyup', function() {
            const query = this.value.toLowerCase();
            const rows = document.querySelectorAll('#booksTable tr');
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(query) ? '' : 'none';
            });
        });
    </script>
</body>
</html>

<?php
$conn->close();
?>
