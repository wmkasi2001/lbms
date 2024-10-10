<?php
session_start();
if (!isset($_SESSION['U_name']) || !$_SESSION['is_admin']) {
    header("Location: admin_login.php");
    exit();
}

include 'db_connect.php';

// Initialize the search
$searchQuery = "";
if (isset($_POST['search'])) {
    $searchQuery = trim($_POST['search_query']);
}

// Handle Check Out action
if (isset($_POST['check_out'])) {
    $requestId = $_POST['request_id'];
    
    // Prepare SQL to delete the request
    $deleteRequestSql = "DELETE FROM requests WHERE id = ?";
    $deleteStmt = $conn->prepare($deleteRequestSql);
    
    if ($deleteStmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }

    $deleteStmt->bind_param("i", $requestId);
    $executeResult = $deleteStmt->execute();

    if ($executeResult === false) {
        die('Execute failed: ' . htmlspecialchars($deleteStmt->error));
    }

    // Mark book as available (this assumes there is a column to indicate availability)
    // Adjust as necessary based on your actual table structure
    $updateBookSql = "UPDATE books SET available = 1 WHERE isbn = (SELECT book_id FROM requests WHERE id = ?)";
    $updateStmt = $conn->prepare($updateBookSql);
    
    if ($updateStmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }

    $updateStmt->bind_param("i", $requestId);
    $updateExecuteResult = $updateStmt->execute();

    if ($updateExecuteResult === false) {
        die('Execute failed: ' . htmlspecialchars($updateStmt->error));
    }
}

// Fetch book details along with student information, borrow and return dates from requests
$sql = "SELECT 
            b.isbn, 
            b.author, 
            b.title, 
            b.accession_number, 
            b.barcode, 
            CONCAT(u.Fname, ' ', u.Lname) AS student_name,  
            r.student_id AS student_id, 
            r.request_date AS borrow_date, 
            r.return_date,
            r.id AS request_id  -- Get the request ID for the checkout button
        FROM books b 
        JOIN requests r ON b.isbn = r.book_id
        JOIN users u ON r.student_id = u.U_name  
        WHERE r.status = 'approved'";

// Modify query to include search condition if a search is being made
if (!empty($searchQuery)) {
    $sql .= " AND b.barcode = ?";
}

$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die('Prepare failed: ' . htmlspecialchars($conn->error));
}

if (!empty($searchQuery)) {
    $stmt->bind_param("s", $searchQuery); // Bind the barcode to the search query
}

$executeResult = $stmt->execute();

if ($executeResult === false) {
    die('Execute failed: ' . htmlspecialchars($stmt->error));
}

$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="admin_books.css">
    <title>Admin View Books</title>
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
            <h1>Admin View Books</h1>

            <!-- Search bar for barcode -->
            <form method="POST" action="" class="search-form">
                <input type="text" name="search_query" placeholder="Scan or enter barcode" value="<?= htmlspecialchars($searchQuery); ?>">
                <button type="submit" name="search">Search</button>
            </form>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ISBN</th>
                            <th>Author</th>
                            <th>Title</th>
                            <th>Accession Number</th>
                            <th>Barcode</th>
                            <th>Student Name</th>
                            <th>Student ID</th>
                            <th>Borrow Date</th>
                            <th>Return Date</th>
                            <th>Action</th> <!-- New column for actions -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>
                                        <td>" . htmlspecialchars($row["isbn"]) . "</td>
                                        <td>" . htmlspecialchars($row["author"]) . "</td>
                                        <td>" . htmlspecialchars($row["title"]) . "</td>
                                        <td>" . htmlspecialchars($row["accession_number"]) . "</td>
                                        <td>" . htmlspecialchars($row["barcode"]) . "</td>
                                        <td>" . htmlspecialchars($row["student_name"]) . "</td>
                                        <td>" . htmlspecialchars($row["student_id"]) . "</td>
                                        <td>" . htmlspecialchars($row["borrow_date"]) . "</td>
                                        <td>" . htmlspecialchars($row["return_date"]) . "</td>
                                        <td>
                                            <form method='POST' action=''>
                                                <input type='hidden' name='request_id' value='" . htmlspecialchars($row["request_id"]) . "'>
                                                <button type='submit' name='check_out' onclick='return confirm(\"Are you sure you want to check out this book?\");'>Check Out</button>
                                            </form>
                                        </td>
                                      </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='10'>No books found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <footer>
        &copy; 2024 Online Library Management System
    </footer>

    <style>
        .search-form {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .search-form input[type="text"] {
            width: 300px;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .search-form button {
            padding: 10px 20px;
            background-color: #003366;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .search-form button:hover {
            background-color: #0059b3;
        }
    </style>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
