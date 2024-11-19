<?php
// Database configuration
$host = 'localhost'; // Database host
$db_name = 'website'; // Database name
$username = 'root'; // Database username
$password = ''; // Database password

// Create a connection
$conn = new mysqli($host, $username, $password, $db_name);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Optionally set the character set to UTF-8
$conn->set_charset("utf8");

?>