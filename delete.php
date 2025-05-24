<?php
$conn = new mysqli("localhost", "root", "kiethly1", "last&found");
$id = $_GET['id'];

$sql = "DELETE FROM lost_found WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: list.php");
exit;
?>
