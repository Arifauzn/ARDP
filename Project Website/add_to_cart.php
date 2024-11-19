<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}

// Initialize cart if not already set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Initialize the events array
$events = [];

// Database connection
$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "website"; 

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all events and their ticket options
$sql = "SELECT e.id AS event_id, e.name AS event_name, t.type AS ticket_type, t.price AS ticket_price, e.currency AS currency 
        FROM events e 
        JOIN tickets t ON e.id = t.event_id";
$result = $conn->query($sql);

// Check if the query was successful and has results
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $events[$row['event_id']]['name'] = $row['event_name'];
        $events[$row['event_id']]['tickets'][] = [
            'type' => $row['ticket_type'],
            'price' => $row['ticket_price'],
            'currency' => $row['currency']
        ];
    }
} else {
    // Handle the case where no events are found
    echo "<div class='alert alert-warning'>No events found.</div>";
}
$conn->close();

// Handle form submission to add tickets to the cart
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['ticket_quantity'] as $eventId => $quantities) {
        foreach ($quantities as $index => $quantity) {
            if ($quantity > 0) {
                $ticketType = $events[$eventId]['tickets'][$index]['type'];
                $ticketPrice = $events[$eventId]['tickets'][$index]['price'];
                $_SESSION['cart'][] = [
                    'event_id' => $eventId,
                    'event_name' => $events[$eventId]['name'],
                    'type' => $ticketType,
                    'price' => $ticketPrice,
                    'quantity' => $quantity
                ];
            }
        }
    }
    // Redirect to the same page with a success message
    header("Location: add_to_cart.php?added=true");
    exit();
}

// Calculate total price from cart
$totalPrice = 0;
$totalTickets = 0;
foreach ($_SESSION['cart'] as $item) {
    $totalPrice += $item['price'] * $item['quantity'];
    $totalTickets += $item['quantity'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add to Cart</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .cart-item {
            border-bottom: 1px solid #ddd;
            padding: 10px 0;
        }
        .cart-item:last-child {
            border-bottom: none;
        }
        .cart-item .remove-btn {
            margin-left: auto;
            color: #dc3545;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Add to Cart</h2>
        <form method="POST">
            <h4>Select Ticket Options from Events</h4>
            <?php foreach ($events as $eventId => $event): ?>
                <h5><?php echo htmlspecialchars($event['name']); ?></h5>
                <?php foreach ($event['tickets'] as $index => $ticket): ?>
                    <div class="mb-3">
                        <label><?php echo htmlspecialchars($ticket['type']); ?> - Price: <?php echo htmlspecialchars($ticket['price']) . ' ' . htmlspecialchars($ticket['currency']); ?></label>
                        <input type="number" class="form-control" name="ticket_quantity[<?php echo $eventId; ?>][<?php echo $index; ?>]" min="0" placeholder="Enter quantity" required>
                    </div>
                <?php endforeach; ?>
            <?php endforeach; ?>
            <button id="submit" class="btn btn-primary">Add to Cart</button>
        </form>

        <div class="mt-4">
            <h4>Your Cart</h4>
            <?php if (!empty($_SESSION['cart'])): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Event Name</th>
                            <th>Ticket Type</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="cart-items">
                        <?php foreach ($_SESSION['cart'] as $item): ?>
                            <tr class="cart-item">
                                <td><?php echo htmlspecialchars($item['event_name']); ?></td>
                                <td><?php echo htmlspecialchars($item['type']); ?></td>
                                <td><?php echo htmlspecialchars($item['price']) . ' ' . htmlspecialchars($item['currency']); ?></td>
                                <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                                <td><?php echo htmlspecialchars($item['price'] * $item['quantity']) . ' ' . htmlspecialchars($item['currency']); ?></td>
                                <td>
                                    <form method="POST" action="remove_from_cart.php" style="display:inline;">
                                        <input type="hidden" name="event_id" value="<?php echo htmlspecialchars($item['event_id']); ?>">
                                        <input type="hidden" name="ticket_type" value="<?php echo htmlspecialchars($item['type']); ?>">
                                        <button type="submit" class="btn btn-link remove-btn" title="Remove"><i class="fas fa-trash-alt"></i></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <h5>Total Tickets: <?php echo htmlspecialchars($totalTickets); ?></h5>
                <h5>Total Amount: <?php echo htmlspecialchars($totalPrice) . ' ' . htmlspecialchars($item['currency']); ?></h5>
                <form action="process_payment.php" method="POST">
                    <input type="hidden" name="total_amount" value="<?php echo htmlspecialchars($totalPrice); ?>">
                    <button type="submit" class="btn btn-success">Proceed to Payment</button>
                </form>
            <?php else: ?>
                <p>Your cart is empty.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>