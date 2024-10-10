<?php
session_start();
include 'db_connect.php';

// Check if the user is logged in and not an admin
if (!isset($_SESSION['U_name']) || $_SESSION['is_admin']) {
    header("Location: student_login.php");
    exit();
}

$u_name = $_SESSION['U_name'];

// Fetch borrowed books for the student
$sql_borrowed_books = "SELECT books.isbn, books.title, requests.request_date AS borrow_date, requests.return_date, requests.id AS request_id 
                       FROM books 
                       JOIN requests ON books.ISBN = requests.book_id 
                       WHERE requests.student_name = ? AND requests.status = 'approved'";

$stmt = $conn->prepare($sql_borrowed_books);
$stmt->bind_param("s", $u_name);
$stmt->execute();
$result_borrowed = $stmt->get_result();

// Fetch all available books along with their categories
$sql_all_books = "SELECT * FROM books";
$result_all_books = $conn->query($sql_all_books);

if ($result_all_books === false) {
    die('Query failed: ' . htmlspecialchars($conn->error));
}

// Fetch unique categories for the dropdown
$sql_categories = "SELECT DISTINCT category FROM books";
$result_categories = $conn->query($sql_categories);

if ($result_categories === false) {
    die('Query failed: ' . htmlspecialchars($conn->error));
}

// Handle category selection
$selected_category = isset($_POST['category']) ? $_POST['category'] : '';
$search_query = isset($_POST['search_query']) ? $_POST['search_query'] : '';

// Build the SQL query for filtered books
$sql_filtered_books = "SELECT * FROM books WHERE 1=1"; // Always true condition for easier query building

if ($selected_category) {
    $sql_filtered_books .= " AND category = ?";
}

if ($search_query) {
    $sql_filtered_books .= " AND title LIKE ?";
}

$stmt_filtered = $conn->prepare($sql_filtered_books);

if ($selected_category && $search_query) {
    $like_search_query = "%" . $search_query . "%";
    $stmt_filtered->bind_param("ss", $selected_category, $like_search_query);
} elseif ($selected_category) {
    $stmt_filtered->bind_param("s", $selected_category);
} elseif ($search_query) {
    $like_search_query = "%" . $search_query . "%";
    $stmt_filtered->bind_param("s", $like_search_query);
}

$stmt_filtered->execute();
$result_all_books = $stmt_filtered->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="student_dashboard.css">
    <title>Student Dashboard</title>
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
    
    <section class="dashboard">
        <h1>Student Dashboard</h1>

        <!-- Notification for Return Status -->
        <?php if (isset($_GET['return_status'])): ?>
            <div class="notification">
                <?php
                if ($_GET['return_status'] === 'success') {
                    echo "Book returned successfully.";
                } elseif ($_GET['return_status'] === 'error') {
                    echo "Error returning the book.";
                }
                ?>
            </div>
        <?php endif; ?>

        <!-- Display Borrowed Books -->
        <div class="section-box">
            <h2>Your Borrowed Books</h2>
            <table class="styled-table">
                <thead>
                    <tr>
                        <th>ISBN</th>
                        <th>Title</th>
                        <th>Borrow Date</th>
                        <th>Return Date</th>
                        <th>Action</th> <!-- Return Book Column -->
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result_borrowed->num_rows > 0) {
                        while ($row = $result_borrowed->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . (isset($row['isbn']) ? htmlspecialchars($row['isbn']) : 'N/A') . "</td>";
                            echo "<td>" . (isset($row['title']) ? htmlspecialchars($row['title']) : 'N/A') . "</td>";
                            echo "<td>" . (isset($row['borrow_date']) ? htmlspecialchars($row['borrow_date']) : 'N/A') . "</td>";
                            echo "<td>" . (isset($row['return_date']) ? htmlspecialchars($row['return_date']) : 'N/A') . "</td>";
                            echo "<td>
                                    <form method='GET' action='return_book.php'>
                                        <input type='hidden' name='request_id' value='" . htmlspecialchars($row['request_id']) . "'>
                                        <button type='submit' class='btn return-btn'>Return</button>
                                    </form>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>You haven't borrowed any books yet.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Browse Available Books -->
        <div class="section-box">
            <h2>Browse Available Books</h2>
            
            <!-- Search Bar -->
            <form method="POST" action="student_dashboard.php" style="margin-bottom: 20px;">
                <input type="text" name="search_query" placeholder="Search by Title" value="<?php echo htmlspecialchars($search_query); ?>" />
                <button type="submit" class="btn">Search</button>
            </form>

            <!-- Category Dropdown -->
            <form method="POST" action="student_dashboard.php" style="margin-bottom: 20px;">
                <label for="category">Select Category:</label>
                <select name="category" id="category" onchange="this.form.submit()">
                    <option value="">All Categories</option>
                    <?php
                    if ($result_categories->num_rows > 0) {
                        while ($row = $result_categories->fetch_assoc()) {
                            $category_name = htmlspecialchars($row['category']);
                            $selected = ($category_name == $selected_category) ? 'selected' : '';
                            echo "<option value='$category_name' $selected>$category_name</option>";
                        }
                    }
                    ?>
                </select>
            </form>

            <table class="styled-table">
                <thead>
                    <tr>
                        <th>ISBN</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Category</th> <!-- Added Category Column -->
                        <th>Barcode</th>  <!-- Added Barcode Column -->
                        <th>Borrow</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result_all_books->num_rows > 0) {
                        while ($row = $result_all_books->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . (isset($row['isbn']) ? htmlspecialchars($row['isbn']) : 'N/A') . "</td>";
                            echo "<td>" . (isset($row['title']) ? htmlspecialchars($row['title']) : 'N/A') . "</td>";
                            echo "<td>" . (isset($row['author']) ? htmlspecialchars($row['author']) : 'N/A') . "</td>";
                            echo "<td>" . (isset($row['category']) ? htmlspecialchars($row['category']) : 'N/A') . "</td>"; // Show Category
                            echo "<td>" . (isset($row['barcode']) ? htmlspecialchars($row['barcode']) : 'N/A') . "</td>";  // Show Barcode
                            echo "<td><a href='submit_borrow_request.php?ISBN=" . (isset($row['isbn']) ? htmlspecialchars($row['isbn']) : '') . "&barcode=" . (isset($row['barcode']) ? htmlspecialchars($row['barcode']) : '') . "'>Borrow</a></td>";  // Borrow by ISBN or Barcode
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>No books available.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Notifications for Borrow Requests -->
        <div class="section-box">
            <h2>Notifications</h2>
            <a href="student_notifications.php" class="btn">Check for Approvals</a>
        </div>
    </section>

    <footer>
        &copy; 2024 Online Library Management System
    </footer>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
