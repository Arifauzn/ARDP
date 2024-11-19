<?php
require('receipt/fpdf.php'); // Include the FPDF library

session_start();

// Check if receipt information is available
if (!isset($_SESSION['receipt'])) {
    die("No receipt information available.");
}

// Retrieve receipt information
$receipt = $_SESSION['receipt'];

// Extract details from the receipt
$receiptDate = $receipt['date'];
$totalAmount = $receipt['total_amount'];
$currency = $receipt['currency'];
$items = $receipt['items'];

// Currency symbol mapping
$currencySymbols = [
    'USD' => '$',
    'EUR' => '€',
    'GBP' => '£',
    'JPY' => '¥',
    'MYR' => 'RM',
    // Add more currencies as needed
];

// Determine currency symbol
$currencySymbol = isset($currencySymbols[$currency]) ? $currencySymbols[$currency] : '';

// Create a new PDF document
$pdf = new FPDF();
$pdf->AddPage();

// Set font
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Payment Receipt', 0, 1, 'C');

// Add date and total amount
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, 'Date: ' . $receiptDate, 0, 1);
$pdf->Cell(0, 10, 'Total Amount: ' . $currencySymbol . number_format($totalAmount, 2) . ' ' . $currency, 0, 1);
$pdf->Ln(10); // Line break

// Add purchased items
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(40, 10, 'Ticket Type', 1);
$pdf->Cell(30, 10, 'Price', 1);
$pdf->Cell(30, 10, 'Quantity', 1);
$pdf->Cell(30, 10, 'Total', 1);
$pdf->Ln();

$pdf->SetFont('Arial', '', 12);
foreach ($items as $item) {
    $itemTotal = $item['price'] * $item['quantity'];
    $pdf->Cell(40, 10, $item['type'], 1);
    $pdf->Cell(30, 10, $currencySymbol . number_format($item['price'], 2), 1);
    $pdf->Cell(30, 10, $item['quantity'], 1);
    $pdf->Cell(30, 10, $currencySymbol . number_format($itemTotal, 2), 1);
    $pdf->Ln();
}

// Output the PDF
$pdf->Output('D', 'receipt.pdf'); // Force download

// Clear the receipt from the session after generating the PDF
unset($_SESSION['receipt']);
?>