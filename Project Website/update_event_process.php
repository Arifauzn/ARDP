<?php
// Database connection
$servername = "localhost"; // Change if necessary
$username = "root"; // Your database username
$password = ""; // Your database password
$dbname = "website"; // Your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and retrieve form data
    $eventId = intval($_POST['event_id']);
    $name = htmlspecialchars($_POST['name']);
    $description = htmlspecialchars($_POST['description']);
    $eventDate = htmlspecialchars($_POST['event_date']);
    $existingImagePath = htmlspecialchars($_POST['existing_image_path']); // Get existing image path

    // Handle file upload if a new image is provided
    $target_file = $existingImagePath; // Default to existing image path
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
        $target_dir = "uploads/"; // Make sure this directory exists and is writable
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        
        // Move the uploaded file to the target directory
        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            echo "Sorry, there was an error uploading your file.";
            // If the upload fails, retain the existing image path
            $target_file = $existingImagePath;
        }
    }

    // Prepare and bind for updating the event
    $stmt = $conn->prepare("UPDATE events SET name=?, description=?, event_date=?, image_path=? WHERE id=?");
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    // Bind parameters
    $stmt->bind_param("ssssi", $name, $description, $eventDate, $target_file, $eventId);

    // Execute the statement
    if ($stmt->execute()) {
        // Process ticket options
        if (!empty($_POST['ticket_types']) && !empty($_POST['ticket_prices'])) {
            $ticketTypes = $_POST['ticket_types'];
            $ticketPrices = $_POST['ticket_prices'];

            // Clear existing tickets
            $conn->query("DELETE FROM tickets WHERE event_id = $eventId");

            // Prepare and bind for tickets
            $ticketStmt = $conn->prepare("INSERT INTO tickets (event_id, type, price) VALUES (?, ?, ?)");
            if ($ticketStmt === false) {
                die("Prepare failed: " . $conn->error);
            }

            foreach ($ticketTypes as $index => $type) {
                $price = $ticketPrices[$index];
                $ticketStmt->bind_param("isd", $eventId, $type, $price);
                $ticketStmt->execute();
            }

            // Close the ticket statement
            $ticketStmt->close();
        }

        echo "Event updated successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the event statement
    $stmt->close();
}

// Close the database connection
$conn->close();
?>