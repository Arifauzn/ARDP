<?php
session_start(); // Start the session

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php"); // Redirect to login if not logged in or not an admin
    exit();
}

// Include database connection
include 'db_connection.php';

// Fetch users from the database
$sql = "SELECT id, username, email, user_role, created_at FROM users";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 20px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Admin Dashboard</h2>
        <div class="text-right mb-3">
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>
        
        <h4>Registered Users</h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>User Role</th>
                    <th>Created At</th>
                    <th>Actions</th> <!-- Added Actions column -->
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['user_role']); ?></td>
                            <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                            <td>
                                <a href="delete_user.php?user_id=<?php echo htmlspecialchars($row['id']); ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">No users found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <h4>Manage Events</h4>
        <div class="mb-3">
            <a href="RegisterEvent.php" class="btn btn-primary">Register New Event</a>
            <a href="dashboard.php" class="btn btn-secondary">View Events</a>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>