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
    $receipt = [
        'date' => date('Y-m-d H:i:s'),
        'total_amount' => $totalAmount,
        'currency' => 'USD', // Change as needed
        'items' => $_SESSION['cart']
    ];
    
    // Display receipt
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Payment Receipt</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    </head>
    <body>
        <div class="container mt-5">
            <h2 class="text-center">Payment Successful</h2>
            <div class="receipt">
                <h4>Receipt</h4>
                <p>Date: <?php echo htmlspecialchars($receipt['date']); ?></p>
                <p>Total Amount: <?php echo htmlspecialchars($receipt['total_amount']) . ' ' . htmlspecialchars($receipt['currency']); ?></p>
                <h5>Purchased Items:</h5>
                <ul>
                    <?php foreach ($receipt['items'] as $item): ?>
                        <li><?php echo htmlspecialchars($item['type']) . ' - ' . htmlspecialchars($item['quantity']) . ' x ' . htmlspecialchars($item['price']) . ' ' . htmlspecialchars($receipt['currency']); ?></li>
                    <?php endforeach; ?>
                </ul>
                <p>Thank you for your purchase!</p>
            </div>
        </div>
    </body>
    </html>
    <?php
} else {
    echo "Payment failed. Please try again.";
}
?>