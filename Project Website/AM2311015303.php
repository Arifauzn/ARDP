<?php
session_start(); // Start the session

// Check if the user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$username = $isLoggedIn ? htmlspecialchars($_SESSION['username']) : 'Guest';
$userRole = isset($_SESSION['user_role']) ? htmlspecialchars($_SESSION['user_role']) : '';

// Determine the welcome message
$welcomeMessage = $isLoggedIn ? "Welcome, $username!" : "Welcome, Guest!";
if ($userRole === 'admin') {
    $welcomeMessage = "Welcome, Admin!";
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Website Ticketing System</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <!-- css file -->
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!--navbar-->
    <div class="container-fluid p-0">
        <!--1st Navbar-->
        <nav class="navbar navbar-expand-lg bg-info">
            <div class="container-fluid">
                <img src="./Images/logo.png" alt="" class="logo">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="#">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="RegisterEvent.php"><i class="fa-solid fa-calendar"></i> Register for Event</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="add_to_cart.php"><i class="fa-solid fa-cart-shopping"></i> Carts </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="dashboard.php"><i class="fi fi-rr-list"></i>Manage Events</a>
                        </li>
                    </ul>
                    <form class="d-flex" role="search">
                        <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                        <button class="btn btn-outline-success" type="submit">Search</button>
                    </form>
                </div>
            </div>
        </nav>

        <!--2nd Navbar-->
        <nav class="navbar navbar-expand-lg navbar-dark bg-secondary">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="#"><?php echo $welcomeMessage; ?></a>
                </li>
                <?php if (!$isLoggedIn): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="Login.php">Login</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="Logout.php">Logout</a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>

        <!-- 3rd -->
        <div class="bg-light">
            <h3 class="text-center">Event Store</h3>
            <p class="text-center">One-way purchase your favourite events tickets</p>
        </div>

        <!-- Display Events -->
        <div class="container my-4">
            <h4 class="text-center">Current Events</h4>
            <div class="row">
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
                // Fetch events from the database
                $sql = "SELECT id, name, description, price, currency, converted_price, image_path FROM events";
                $result = $conn->query($sql);

                // Check if the query was successful
                if ($result === false) {
                        die("Error executing query: " . $conn->error);
                    }

                // Check if there are results
                if ($result->num_rows > 0) {
                // Output data of each row
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="col-md-4 mb-4">';
            echo '<div class="card">';
            echo '<img src="' . htmlspecialchars($row['image_path']) . '" class="card-img-top" alt="' . htmlspecialchars($row['name']) . '" style="height: 200px; object-fit: cover;">';
            echo '<div class="card-body">';
            echo '<h5 class="card-title">' . htmlspecialchars($row['name']) . '</h5>';
            echo '<p class="card-text">' . htmlspecialchars($row['description']) . '</p>';
            echo '<p class="card-text"><strong>Price:</strong> ' . htmlspecialchars($row['price']) . ' ' . htmlspecialchars($row['currency']) . '</p>';
            echo '<p class="card-text"><strong>Converted Price:</strong> ' . htmlspecialchars($row['converted_price']) . '</p>';
            // Update the button to link to Payment.php with the event ID
            echo '<a href="add_to_cart.php?event_id=' . htmlspecialchars($row['id']) . '" class="btn btn-primary">Buy Ticket</a>'; // Link to Payment.php
            echo '</div>';
            echo '</div>'; // Close card
            echo '</div>'; // Close column
            }
            } else  {
            echo '<div class="col-12"><p class="text-center">No events found.</p></div>';
            }

                // Close the database connection
                $conn->close();
                ?>
            </div> <!-- Close row -->
        </div> <!-- Close container -->

        <!-- Footer -->
        <div class="bg-info p-3 text-center">
            <p>Copyright © 2024 Ticket2U Sdn Bhd. All rights reserved.</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>