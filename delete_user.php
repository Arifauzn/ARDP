<?php
session_start(); // Start the session

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php"); // Redirect to login if not logged in or not an admin
    exit();
}

// Include database connection
include 'db_connection.php';

// Check if user_id is set in the URL
if (isset($_GET['user_id'])) {
    $user_id = intval($_GET['user_id']); // Validate the user ID

    // Prepare and execute the deletion query
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        // Redirect back to the admin dashboard with a success message
        header("Location: admin_dashboard.php?message=User  deleted successfully.");
        exit();
    } else {
        // Handle error
        echo "Error deleting user: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "User  ID not specified.";
}

$conn->close();
?>