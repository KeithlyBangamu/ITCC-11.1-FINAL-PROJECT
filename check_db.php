<?php
$host = "localhost";
$user = "root";
$pass = "kiethly123";
$dbname = "lost_and_found_db";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}
echo "✅ Connected successfully!";
?>
