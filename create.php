<?php
$conn = new mysqli("localhost", "root", "kiethly1", "last&found");

$title = $_POST['title'];
$description = $_POST['description'];
$status = $_POST['status'];

$sql = "INSERT INTO lost_found (title, description, status) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $title, $description, $status);
$stmt->execute();

echo "Item reported successfully.";
?>
