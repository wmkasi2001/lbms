<?php
session_start();
if (!isset($_SESSION['U_name']) || !$_SESSION['is_admin']) {
    header("Location: admin_login.php");
    exit();
}

include 'db_connect.php';

// Fetch all pending borrowing requests, including the student ID and full name
$sql = "SELECT requests.id, users.Fname AS first_name, users.Lname AS last_name, requests.student_id, 
               books.title, requests.request_date, requests.status 
        FROM requests 
        JOIN books ON requests.book_id = books.isbn 
        JOIN users ON requests.student_id = users.U_name 
        WHERE requests.status = 'pending'";
$result = $conn->query($sql);

// Initialize message variable
$message = '';

// Handle the form submission for approval
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['approve'])) {
    $request_id = $_POST['request_id'];
    $return_date = $_POST['return_date'];

    // Update the request with the return date and status
    $update_sql = "UPDATE requests SET return_date = ?, status = 'approved' WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("si", $return_date, $request_id);

    if ($stmt->execute()) {
        // Notify student about approval
        sendNotification($request_id, "Your request for the book has been approved. Return date: $return_date.");
        
        // Set success message
        $message = "Book has been approved.";
    } else {
        $message = "Error approving request: " . $conn->error;
    }
    $stmt->close();
}

// Handle the form submission for rejection
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reject'])) {
    $request_id = $_POST['request_id'];

    // Update the request status to rejected
    $update_sql = "UPDATE requests SET status = 'rejected' WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("i", $request_id);

    if ($stmt->execute()) {
        // Notify student about rejection
        sendNotification($request_id, "Your request for the book has been rejected.");
        
        // Set rejection message
        $message = "Book has been rejected.";
    } else {
        $message = "Error rejecting request: " . $conn->error;
    }
    $stmt->close();
}

// Function to send notifications to students
function sendNotification($request_id, $message) {
    global $conn;

    // Prepare to fetch the student ID associated with the request
    $stmt2 = $conn->prepare("SELECT student_id FROM requests WHERE id = ?");
    
    if ($stmt2 === false) {
        die("Error preparing query: " . $conn->error); // Debugging output
    }
    
    $stmt2->bind_param("i", $request_id);
    
    if (!$stmt2->execute()) {
        die("Error executing query: " . $stmt2->error); // Debugging output
    }
    
    $result = $stmt2->get_result();
    $student = $result->fetch_assoc();
    
    if (!$student) {
        die("No student found for the given request ID"); // Debugging output
    }
    
    $student_id = $student['student_id'];

    // Prepare to insert the notification
    $insert_sql = "INSERT INTO notifications (student_id, message, request_id, return_date) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_sql);
    
    if ($stmt === false) {
        die("Error preparing notification query: " . $conn->error); // Debugging output
    }

    // Add a placeholder for return date
    $return_date = null; // Or set this if needed
    $stmt->bind_param("sssi", $student_id, $message, $request_id, $return_date);
    
    if (!$stmt->execute()) {
        die("Error executing notification insert: " . $stmt->error); // Debugging output
    }
    
    $stmt->close();
    $stmt2->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="notifications.css">
    <title>Notifications</title>
    <style>
        .action-btn {
            background-color: #003366;
            color: white;
            padding: 8px 16px;
            border: none;
            cursor: pointer;
            border-radius: 4px;
            margin-right: 5px;
            text-align: center;
        }

        .action-btn:hover {
            background-color: #0059b3;
        }

        .reject-btn {
            background-color: #ff4d4d;
            color: white;
        }

        .reject-btn:hover {
            background-color: #cc0000;
        }

        .action-form {
            display: inline-block;
        }
    </style>
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
        <h1>Borrowing Requests</h1>
        <?php if ($message): ?>
            <p><?php echo htmlspecialchars($message); ?></p>
            <p>You will be redirected to the notifications page in 5 seconds...</p>
            <script>
                setTimeout(function(){
                    window.location.href = "notifications.php";
                }, 5000);
            </script>
        <?php else: ?>
            <?php if ($result->num_rows > 0): ?>
                <table border="1">
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Student ID</th> <!-- Column for Student ID -->
                            <th>Book Title</th>
                            <th>Request Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['student_id']); ?></td> <!-- Display Student ID -->
                                <td><?php echo htmlspecialchars($row['title']); ?></td>
                                <td><?php echo htmlspecialchars($row['request_date']); ?></td>
                                <td><?php echo htmlspecialchars($row['status']); ?></td>
                                <td>
                                    <form method="POST" action="" class="action-form">
                                        <label for="return_date">Return Date:</label>
                                        <input type="date" name="return_date" required>
                                        <input type="hidden" name="request_id" value="<?php echo $row['id']; ?>">
                                        <input type="submit" name="approve" value="Approve" class="action-btn">
                                    </form>
                                    <form method="POST" action="" class="action-form">
                                        <input type="hidden" name="request_id" value="<?php echo $row['id']; ?>">
                                        <input type="submit" name="reject" value="Reject" class="action-btn reject-btn">
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No pending requests.</p>
            <?php endif; ?>
        <?php endif; ?>
    </section>

    <footer>
        &copy; 2024 Online Library Management System
    </footer>
</body>
</html>
