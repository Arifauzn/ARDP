<?php
session_start();

// Check if the cart is set
if (isset($_SESSION['cart'])) {
    $eventId = $_POST['event_id'];
    $ticketType = $_POST['ticket_type'];

    // Loop through the cart to find and remove the item
    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['event_id'] == $eventId && $item['type'] == $ticketType) {
            unset($_SESSION['cart'][$key]);
            break; // Exit the loop once the item is found and removed
        }
    }

    // Re-index the cart array
    $_SESSION['cart'] = array_values($_SESSION['cart']);
}

// Redirect back to the add_to_cart page
header("Location: add_to_cart.php");
exit();
?>