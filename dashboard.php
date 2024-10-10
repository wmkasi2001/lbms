<?php
session_start();
if (!isset($_SESSION['u_name'])) {
    header("Location: student_login.php");
    exit();
}

include 'db_connect.php';

$u_name = $_SESSION['u_name'];
$search_query = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $search_query = $_POST['search_query'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="style.css">
    <title>Dashboard</title>
</head>
<body>
    <header>
        <div class="logo">
            <img src="Images/lbmslogo.png" alt="Library Logo">
        </div>
        <nav>
            <ul>
                <li><a href="dashboard.php">DASHBOARD</a></li>
                <li><a href="logout.php">LOGOUT</a></li>
            </ul>
        </nav>
    </header>
    <section>
        <h1>Welcome, <?php echo htmlspecialchars($u_name); ?>!</h1>
        <h2>Student Dashboard</h2>
        <p>Here you can view your borrowing history and available books.</p>

        <form method="POST" action="">
            <input type="text" name="search_query" placeholder="Search for your books" value="<?php echo htmlspecialchars($search_query); ?>">
            <input class="btn" type="submit" value="Search">
        </form>

        <h3>Your Borrowed Books</h3>
        <table>
            <thead>
                <tr>
                    <th>Book Name</th>
                    <th>Return Date</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $sql = "SELECT books.title AS book_name, borrow.return_date 
                    FROM borrow 
                    JOIN books ON borrow.book_id = books.ISBN 
                    WHERE borrow.u_name = ?";

            if (!empty($search_query)) {
                $sql .= " AND books.title LIKE ?";
                $search_query = '%' . $search_query . '%';
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ss", $u_name, $search_query);
            } else {
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $u_name);
            }

            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['book_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['return_date']); ?></td>
                    </tr>
                <?php endwhile;
            } else {
                echo "<tr><td colspan='2'>No borrowed books found.</td></tr>";
            }
            $stmt->close();
            ?>
            </tbody>
        </table>
    </section>
</body>
</html>
<?php
$conn->close();
?>
