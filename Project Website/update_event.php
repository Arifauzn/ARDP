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

// Initialize variables
$event = null;
$tickets = [];

// Check if an event ID is provided for updating
$eventId = isset($_GET['event_id']) ? intval($_GET['event_id']) : 0;

if ($eventId > 0) {
    // Fetch the event details for updating
    $stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
    $stmt->bind_param("i", $eventId);
    $stmt->execute();
    $result = $stmt->get_result();
    $event = $result->fetch_assoc();

    // Fetch ticket options for the event
    $ticketStmt = $conn->prepare("SELECT * FROM tickets WHERE event_id = ?");
    $ticketStmt->bind_param("i", $eventId);
    $ticketStmt->execute();
    $ticketResult = $ticketStmt->get_result();
    while ($row = $ticketResult->fetch_assoc()) {
        $tickets[] = $row;
    }

    // Add the existing image path to the form data
    $existingImagePath = htmlspecialchars($event['image_path']);
} else {
    echo "Invalid Event ID.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Event</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Update Event</h2>
        <form action="update_event_process.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="event_id" value="<?php echo $eventId; ?>">
            <input type="hidden" name="existing_image_path" value="<?php echo $existingImagePath; ?>">
            <div class="mb-3">
                <label for="name" class="form-label">Event Name</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($event['name']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Event Description</label>
                <textarea class="form-control" id="description" name="description" rows="3" required><?php echo htmlspecialchars($event['description']); ?></textarea>
            </div>
            <div class="mb-3">
                <label for="eventDate" class="form-label">Event Date</label>
                <input type="date" class="form-control" id="eventDate" name="event_date" value="<?php echo htmlspecialchars($event['event_date']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">Change Event Image</label>
                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                <small class="form-text text-muted">Leave blank if you do not want to change the image.</small>
            </div>
            <div id="ticketOptions">
                <h4>Ticket Options</h4>
                <?php foreach ($tickets as $index => $ticket): ?>
                    <div class="ticket-option mb-3">
                        <label for="ticketType<?php echo $index + 1; ?>" class="form-label">Ticket Type</label>
                        <input type="text" class="form-control" id="ticketType<?php echo $index + 1; ?>" name="ticket_types[]" value="<?php echo htmlspecialchars($ticket['type']); ?>" required>
                        <label for="ticketPrice<?php echo $index + 1; ?>" class="form-label">Price (in USD)</label>
                        <input type="number" class="form-control" id="ticketPrice<?php echo $index + 1; ?>" name="ticket_prices[]" value="<?php echo htmlspecialchars($ticket['price']); ?>" required>
                    </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="btn btn-secondary" id="addTicket">Add Another Ticket Option</button>
            <br><br>
            <button type="submit" class="btn btn-primary">Update Event</button>
        </form>
    </div>

    <script>
        function addTicketOption() {
            const ticketOptionsDiv = document.getElementById('ticketOptions');
            const ticketCount = ticketOptionsDiv.getElementsByClassName('ticket-option').length + 1;

            const newTicketOption = document.createElement('div');
            newTicketOption.classList.add('ticket-option', 'mb-3');
            newTicketOption.innerHTML = `
                <label for="ticketType${ticketCount}" class="form-label">Ticket Type</label>
                <input type="text" class="form-control" id="ticketType${ticketCount}" name="ticket_types[]" placeholder="e.g. VIP" required>
                <label for="ticketPrice${ticketCount}" class="form-label">Price (in USD)</label>
                <input type="number" class="form-control" id="ticketPrice${ticketCount}" name="ticket_prices[]" placeholder="e.g. 100" required>
            `;
            ticketOptionsDiv.appendChild(newTicketOption);
        }

        document.getElementById('addTicket').addEventListener('click', addTicketOption);
    </script>
</body>
</html>