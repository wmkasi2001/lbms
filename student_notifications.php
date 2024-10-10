<?php
session_start();
include 'db_connect.php';

// Check if user is logged in and not an admin
if (!isset($_SESSION['U_name']) || $_SESSION['is_admin']) {
    header("Location: student_login.php");
    exit();
}

$u_name = $_SESSION['U_name'];

// Fetch book requests made by the student with 'approved' or 'pending' status
$sql_requests = "SELECT requests.id, books.title, requests.request_date, requests.status, requests.return_date
                 FROM requests 
                 JOIN books ON requests.book_id = books.isbn 
                 WHERE requests.student_name = ? AND requests.status IN ('approved', 'pending')";

$stmt = $conn->prepare($sql_requests);
if ($stmt === false) {
    die('Prepare failed: ' . htmlspecialchars($conn->error));
}
$stmt->bind_param("s", $u_name);
$stmt->execute();
$result_requests = $stmt->get_result();

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="student_dashboard.css">
    <title>Borrowed Books</title>
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
        <h1>Your Borrowed Books</h1>

        <!-- Display Book Requests -->
        <div class="section-box">
            <h2>Borrow Requests</h2>
            <table class="styled-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Request Date</th>
                        <th>Status</th>
                        <th>Return Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result_requests->num_rows > 0) {
                        while ($row = $result_requests->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['title']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['request_date']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                            echo "<td>" . (isset($row['return_date']) ? htmlspecialchars($row['return_date']) : 'Pending') . "</td>";
                            
                            // Only show the return button if the status is 'approved'
                            if ($row['status'] == 'approved') {
                                echo "<td>
                                        <form method='POST' action='return_book.php'>
                                            <input type='hidden' name='request_id' value='" . $row['id'] . "'>
                                            <button type='submit'>Return</button>
                                        </form>
                                      </td>";
                            } else {
                                echo "<td>N/A</td>";
                            }

                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>You have no borrowed books at the moment.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        
        <!-- Return Button to go back -->
        <div class="return-button">
            <button onclick="history.back()">Return</button>
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
