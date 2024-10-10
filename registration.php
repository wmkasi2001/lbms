<?php
include 'db_connect.php'; // Include the database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $f_name = $_POST['f_name'];
    $l_name = $_POST['l_name'];
    $u_name = $_POST['u_name'];
    $password = $_POST['password'];
    $email = $_POST['email'];

    // SQL query to insert user
    $sql = "INSERT INTO users (Fname, Lname, U_name, password, email) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $f_name, $l_name, $u_name, $password, $email);

    if ($stmt->execute()) {
        // Redirect to the login page after successful registration
        header("Location: student_login.php");
        exit(); // Ensure no further code is executed after redirection
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="registration.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Student Registration</title>
    <script src="validation.js"></script>
</head>
<body>
    <header>
        <div class="logo">
            <img src="Images/lbmslogo.png" alt="Library Logo">
        </div>
        <nav>
            <ul>
                <li><a href="dashboard.<?php  ?>">HOME</a></li>
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
            <h2>Registration Form</h2>
            <form name="loginreg" action="" method="POST">
                <input class="form-control" type="text" name="f_name" placeholder="First Name" required>
                <input class="form-control" type="text" name="l_name" placeholder="Last Name" required>
                <input class="form-control" type="" name="u_name" placeholder="ID Number" required>
                <input class="form-control" type="password" name="password" placeholder="Password" required>
                <input class="form-control" type="text" name="email" placeholder="Email" required>
                <input class="btn" type="submit" name="submit" value="Sign Up">
            </form>
        </div>
    </section>

    <footer>
        &copy; 2024 Online Library Management System
    </footer>
</body>
</html>
