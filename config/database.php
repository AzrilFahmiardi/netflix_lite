<?php
// Database configuration
$host = "localhost";
$user = "root";
$pass = "12345678";
$db = "streamflix";

// Create database connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
