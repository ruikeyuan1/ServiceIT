<?php
$server = "localhost";
$username = "root";
$password = "";

// Create connection
$conn = new mysqli($server, $username, $password, "serviceIT");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?> 