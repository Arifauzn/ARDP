<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            max-width: 800px;
            margin-top: 50px;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        h2 {
            margin-bottom: 20px;
            color: #007bff;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .ticket-option {
            border: 1px solid #ced4da;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 15px;
            background-color: #f1f1f1;
        }
        .remove-ticket {
            margin-top: 10px;
            color: #dc3545;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Event Registration Form</h2>
        <form action="register.php" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="name" class="form-label">Event Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Event Description</label>
                <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
            </div>
            <div class="mb-3">
                <label for="eventDate" class="form-label">Event Date</label>
                <input type="date" class="form-control" id="eventDate" name="event_date" required>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Price (in USD)</label>
                <input type="number" class="form-control" id="price" name="price" value="0" required>
            </div>
            <div class="mb-3">
                <label for="currency" class="form-label">Currency</label>
                <select class="form-control" id="currency" name="currency" required>
                    <option value="USD">USD</option>
                    <option value="EUR">EUR</option>
                    <option value="GBP">GBP</option>
                    <option value="JPY">JPY</option>
                    <option value="MYR">MYR</option>
                    <!-- Add more currencies as needed -->
                </select>
            </div>
            <div class="mb-3">
                <label for="convertedPrice" class="form-label">Converted Price</label>
                <input type="number" class="form-control" id="convertedPrice" name="convertedPrice" readonly>
            </div>
            <div id="ticketOptions">
                <h4>Ticket Options</h4>
                <div class="ticket-option mb-3">
                    <label for="ticketType1" class="form-label">Ticket Type</label>
                    <input type="text" class="form-control" id="ticketType1" name="ticket_types[]" placeholder="e.g. General Admission" required>
                    <label for="ticketPrice1" class="form-label">Price (in USD)</label>
                    <input type="number" class="form-control ticket-price" id="ticketPrice1" name="ticket_prices[]" placeholder="e.g. 50" required>
                ```html
                </div>
            </div>
            <button type="button" class="btn btn-secondary" id="addTicket">Add Another Ticket Option</button>
            <br><br>
            <div class="mb-3">
                <label for="image" class="form-label">Upload Event Image</label>
                <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
            </div>
            <button type="submit" class="btn btn-primary">Register Event</button>
        </form>
        <br>
        <button class="btn btn-warning" onclick="promptForEventId()">Update Event</button>
    </div>

    <script>
        const exchangeRates = {
            'USD': 1,
            'EUR': 0.85, // Example conversion rate
            'GBP': 0.75,
            'JPY': 110.0,
            'MYR': 4.47
            // Add more currencies and their rates
        };

        document.getElementById('price').addEventListener('input', convertPrice);
        document.getElementById('currency').addEventListener('change', convertPrice);

        function convertPrice() {
            const price = parseFloat(document.getElementById('price').value);
            const currency = document.getElementById('currency').value;
            
            if (!isNaN(price)) {
                const convertedPrice = price * exchangeRates[currency];
                document.getElementById('convertedPrice').value = convertedPrice.toFixed(2);
            } else {
                document.getElementById('convertedPrice').value = '';
            }
        }

        function addTicketOption() {
            const ticketOptionsDiv = document.getElementById('ticketOptions');
            const ticketCount = ticketOptionsDiv.getElementsByClassName('ticket-option').length + 1;

            const newTicketOption = document.createElement('div');
            newTicketOption.classList.add('ticket-option', 'mb-3');
            newTicketOption.innerHTML = `
                <label for="ticketType${ticketCount}" class="form-label">Ticket Type</label>
                <input type="text" class="form-control" id="ticketType${ticketCount}" name="ticket_types[]" placeholder="e.g. VIP" required>
                <label for="ticketPrice${ticketCount}" class="form-label">Price (in USD)</label>
                <input type="number" class="form-control ticket-price" id="ticketPrice${ticketCount}" name="ticket_prices[]" placeholder="e.g. 100" required>
                <span class="remove-ticket" onclick="removeTicketOption(this)">Remove</span>
            `;
            ticketOptionsDiv.appendChild(newTicketOption);
        }

        function removeTicketOption(element) {
            element.parentElement.remove();
        }

        document.getElementById('addTicket').addEventListener('click', addTicketOption);

        function promptForEventId() {
            const eventId = prompt("Please enter the Event ID you want to update:");
            if (eventId) {
                // Redirect to the update event page with the event ID
                window.location.href = `update_event.php?event_id=${eventId}`;
            }
        }
    </script>
</body>
</html>