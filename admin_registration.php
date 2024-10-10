<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="admin_registration.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Registration</title>
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
            <h2>Admin Registration Form</h2>
            <form name="adminreg" action="admin_registration_process.php" method="POST">
                <input class="form-control" type="text" name="f_name" placeholder="First Name" required>
                <input class="form-control" type="text" name="l_name" placeholder="Last Name" required>
                <input class="form-control" type="text" name="U_name" placeholder="Username" required>
                <input class="form-control" type="password" name="password" placeholder="Password" required>
                <input class="form-control" type="text" name="email" placeholder="Email" required>
                <input class="btn" type="submit" name="submit" value="Register">
            </form>
        </div>
    </section>

    <footer>
        &copy; 2024 Online Library Management System
    </footer>
</body>
</html>
