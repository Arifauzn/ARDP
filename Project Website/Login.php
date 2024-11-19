<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            max-width: 400px;
            margin-top: 100px;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .btn-link {
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="mt-5">Login</h2>
        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
        <p class="mt-3">Don't have an account? 
            <a href="register_user.php" class="btn btn-link">Register as User</a> 
            or 
            <a href="register_admin.php" class="btn btn-link">Register as Admin</a>
        </p>
        <p class="mt-4">Go back? <a href="AM2311015303.php" class="back-button">Back to Homepage</a></p>
    </div>

    <?php
    // Include database connection
    include 'db_connection.php';

    session_start(); // Start a session

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Check user credentials in both users and admins tables
        $sqlUser   = "SELECT * FROM users WHERE username = ?";
        $stmtUser   = $conn->prepare($sqlUser  );
        $stmtUser  ->bind_param("s", $username);
        $stmtUser  ->execute();
        $resultUser   = $stmtUser  ->get_result();

        // Check if the user exists in users table
        if ($resultUser  ->num_rows > 0) {
            $user = $resultUser  ->fetch_assoc();

            // Verify the password
            if (password_verify($password, $user['password'])) {
                // Password is correct, set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username']; // Store username in session
                $_SESSION['user_role'] = $user['user_role']; // Store user role in session

                // Redirect based on user role
                if ($user['user_role'] === 'admin') {
                    header("Location: admin_dashboard.php"); // Redirect to admin dashboard
                } else {
                    header("Location: Welcome.php"); // Redirect to user welcome page
                }
                exit();
            } else {
                echo "<div class='alert alert-danger'>Invalid password.</div>";
            }
        } else {
            // Check admin credentials if not found in users table
            $sqlAdmin = "SELECT * FROM admins WHERE username = ?";
            $stmtAdmin = $conn->prepare($sqlAdmin);
            $stmtAdmin->bind_param("s", $username);
            $stmtAdmin->execute();
            $resultAdmin = $stmtAdmin->get_result();

            // Check if the admin exists
            if ($resultAdmin->num_rows > 0) {
                $admin = $resultAdmin->fetch_assoc();

                // Verify the password
                if (password_verify($password, $admin['password'])) {
                    // Password is correct, set session variables
                    $_SESSION['user_id'] = $admin['id'];
                    $_SESSION['username'] = $admin['username']; // Ensure this is set
                    $_SESSION['user_role'] = 'admin'; // Set user role as admin

                    // Redirect to admin dashboard
                    header("Location: admin_dashboard.php");
                    exit();
                } else {
                    echo "<div class='alert alert-danger'>Invalid password.</div>";
                }
            } else {
                echo "<div class='alert alert-danger'>No user found with that username.</div>";
            }
        }

        $stmtUser ->close();
        $stmtAdmin->close();
        $conn->close();
    }
    ?>
</body>
</html>