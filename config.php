<?php
// config.php

$servername = "localhost";
$username = "root"; // Your database username
$password = "akankithu";     // Your database password (replace with your actual password)
$dbname = "edmstrophy";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
// Function to set session messages
function set_message($message, $type = 'success') {
    $_SESSION['message'] = $message;
    $_SESSION['message_type'] = $type;
}
?>

