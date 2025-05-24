<?php
session_start();
include 'db.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $item_name = $_POST['item_name'];
    $description = $_POST['description'];
    $timestamp = date("Y-m-d H:i:s");

    // Create uploads folder if it doesn't exist
    $target_dir = "uploads/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir);
    }

    // Handle file upload
    $filename = basename($_FILES["photo"]["name"]);
    $target_file = $target_dir . time() . "_" . $filename;

    if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
        // Insert into database
        $student_id = $_SESSION['student_id']; // pull integer id
        $stmt = $conn->prepare("INSERT INTO lost_items (item_name, description, photo_path, timestamp, is_claimed, student_id) VALUES (?, ?, ?, ?, 0, ?)");
        $stmt->bind_param("ssssi", $item_name, $description, $target_file, $timestamp, $student_id);
        $stmt->execute();
        $stmt->close();

        header("Location: student_dashboard.php?success=1");
        exit();
    } else {
        echo "Failed to upload image.";
    }
} else {
    echo "Invalid request method.";
}

$conn->close();
?>
