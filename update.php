<?php
$conn = new mysqli("localhost", "root", "kiethly1", "last&found");
$id = $_GET['id'];

$sql = "UPDATE lost_found SET is_claimed = 1 WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: list.php");
exit;
?>
