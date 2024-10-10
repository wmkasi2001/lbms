<?php
include 'db_connect.php'; // Include the database connection

$feedbackSubmitted = false;

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $feedback = $_POST['feedback'];

    // SQL query to insert feedback
    $sql = "INSERT INTO feedback (name, feedback) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $name, $feedback);

    if ($stmt->execute()) {
        $feedbackSubmitted = true;
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Fetch feedback from the database
$sql = "SELECT name, feedback, timestamp FROM feedback ORDER BY timestamp DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="feedback.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Feedback</title>
    <script src="form_validation.js"></script>
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
        <div class="box">
            <h1>Library Management System</h1>
            <h2>Feedback</h2>
            <div class="feedback-container">
                <form name="feedbackForm" action="feedback.php" method="POST" onsubmit="return validateFeedbackForm()">
                    <input class="form-control" type="text" name="name" placeholder="Your Name" required>
                    <textarea class="form-control" name="feedback" placeholder="Your Feedback" required></textarea>
                    <input class="btn" type="submit" name="submit" value="Submit Feedback">
                </form>
                
                <div class="feedback-list">
                    <h3>Feedback from Users</h3>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<div class='feedback-item'>";
                            echo "<p><strong>" . htmlspecialchars($row['name']) . "</strong> (" . $row['timestamp'] . ")</p>";
                            echo "<p>" . htmlspecialchars($row['feedback']) . "</p>";
                            echo "</div>";
                        }
                    } else {
                        echo "<p>No feedback yet. Be the first to submit your feedback!</p>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </section>

    <footer>
        &copy; 2024 Online Library Management System
    </footer>

    <?php if ($feedbackSubmitted): ?>
    <script>
        alert('Feedback submitted successfully!');
    </script>
    <?php endif; ?>

</body>
</html>
