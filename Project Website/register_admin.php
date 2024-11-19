<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Admin</title>
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
        <h2 class="mt-5">Register Admin</h2>
        <form action="register_admin.php" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="secret_key">Secret Key</label>
                <input type="text" class="form-control" id="secret_key" name="secret_key" required>
            </div>
            <div class="form-group">
                <label for="user_role">User  Role</label>
                <select class="form-control" id="user_role" name="user_role" required>
                    <option value="admin">Admin</option>
                    <option value="super_admin">Super Admin</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Register</button>
        </form>
        <p class="mt-3">Already have an account? <a href="Login.php" class="btn btn-link">Login here</a></p>
    </div>

    <?php
    // Include database connection
    include 'db_connection.php';

    // Define the secret key
    $secret_key = 'key'; // Replace with your secret key

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password
        $secret_key_input = $_POST['secret_key'];
        $user_role = $_POST['user_role']; // Get the user role from the form

        // Check if the secret key is correct
        if ($secret_key_input !== $secret_key) {
            echo "<div class='alert alert-danger'>Invalid secret key.</div>";
            exit;
        }

        // Insert admin into the database
        $sql = "INSERT INTO admins (username, email, password, user_role) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $username, $email, $password, $user_role); // Bind the user role as well

        if ($stmt->execute()) {
            echo "<div class='alert alert-success'>Registration successful!</div>";
        } else {
            echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>"; // Display error
        }

        $stmt->close();
        $conn->close();
    }
    ?>
</body>
</html>