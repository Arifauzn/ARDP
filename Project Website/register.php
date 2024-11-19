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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and retrieve form data
    $name = htmlspecialchars($_POST['name']);
    $description = htmlspecialchars($_POST['description']);
    $price = htmlspecialchars($_POST['price']);
    $currency = htmlspecialchars($_POST['currency']);
    $convertedPrice = htmlspecialchars($_POST['convertedPrice']);
    
    // Handle file upload
    $target_dir = "uploads/"; // Make sure this directory exists and is writable
    $target_file = $target_dir . basename($_FILES["image"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if ($check === false) {
        echo "File is not an image.";
        $uploadOk = 0;
    }

    // Check file size (limit to 5MB)
    if ($_FILES["image"]["size"] > 5000000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    } else {
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            // Prepare and bind for the event
            $stmt = $conn->prepare("INSERT INTO events (name, description, price, currency, converted_price, image_path) VALUES (?, ?, ?, ?, ?, ?)");
            if ($stmt === false) {
                die("Prepare failed: " . $conn->error);
            }

            // Bind parameters
            $stmt->bind_param("ssisss", $name, $description, $price, $currency, $convertedPrice, $target_file);

            // Execute the statement
            if ($stmt->execute()) {
                $eventId = $stmt->insert_id; // Get the last inserted event ID

                // Process ticket options
                if (!empty($_POST['ticket_types']) && !empty($_POST['ticket_prices'])) {
                    $ticketTypes = $_POST['ticket_types'];
                    $ticketPrices = $_POST['ticket_prices'];

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

                echo "New event registered successfully.";
            } else {
                echo "Error: " . $stmt->error;
            }

            // Close the event statement
            $stmt->close();
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}

// Close the database connection
$conn->close();
?>