<?php
$host = 'localhost';
$user = 'root';
$pass = ''; // default for XAMPP
$db = 'lost_and_found_db';

$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
