<?php
session_start();

// Check if total amount is set
if (!isset($_POST['total_amount'])) {
    die("No payment information provided.");
}

// Simulate payment processing (this should be replaced with actual payment gateway integration)
$totalAmount = floatval($_POST['total_amount']);
$paymentSuccessful = true; // Simulate a successful payment

if ($paymentSuccessful) {
    // Clear the cart after successful payment
    $_SESSION['cart'] = [];

    // Generate receipt
    $_SESSION['receipt'] = [
        'date' => date('Y-m-d H:i:s'),
        'total_amount' => $totalAmount,
        'currency' => 'USD', // Change as needed
        'items' => $_SESSION['cart']
    ];

    // Redirect to generate_receipt.php
    header("Location: generate_receipt.php");
    exit();
} else {
    // Provide more informative feedback to the user
    echo "<div class='alert alert-danger'>Payment failed. Please check your payment details and try again.</div>";
}
?>