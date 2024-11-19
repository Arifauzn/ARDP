<?php
session_start(); // Start the session

/*// Check if the user is logged in and has admin role
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php"); // Redirect to login if not logged in or not an admin
    exit();
}*/

// Database connection
include 'db_connection.php';

// Check if event ID is provided
if (isset($_GET['event_id'])) {
    $eventId = intval($_GET['event_id']);

    // Prepare and execute the delete statement
    $stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
    $stmt->bind_param("i", $eventId);

    if ($stmt->execute()) {
        echo "Event deleted successfully.";
    } else {
        echo "Error deleting event: " . $stmt->error;
    }

    $stmt->close();
}

// Redirect back to the dashboard
header("Location: dashboard.php");
exit();
?>