<?php
session_start();
include 'db.php';

// Admin authentication
if (!isset($_SESSION['admin_user'])) {
    header("Location: index.html");
    exit();
}

if (isset($_GET['id']) && isset($_GET['status'])) {
    $item_id = $_GET['id'];
    $new_status = $_GET['status'];

    $sql = "UPDATE lost_items SET is_claimed = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $new_status, $item_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        header("Location: admin_dashboard.php");
    } else {
        echo "Error updating claim status.";
    }

    $stmt->close();
} else {
    echo "Invalid request.";
}

$conn->close();
?>
