<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h2 class="mt-5">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
        <p>You are now logged in.</p>
        <a href="AM2311015303.php" class="btn btn-primary">Go to Homepage</a> <!-- Button to go to homepage -->
        <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>
</body>
</html>