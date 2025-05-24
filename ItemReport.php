<?php

$conn = new mysqli('localhost', 'root', 'kiethly1', 'last&found');
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $status = $conn->real_escape_string($_POST['status']);
    $sql = "INSERT INTO lost_found (title, description, status) VALUES ('$title', '$description', '$status')";
    if ($conn->query($sql) === TRUE) {
        $success = true;
    }
}
?>
<form method="POST">
    <input type="text" name="title" placeholder="Item title" required>
    <textarea name="description" placeholder="Describe the item" required></textarea>
    <select name="status">
        <option value="lost">Lost</option>
        <option value="found">Found</option>
    </select>
    <button type="submit">Report</button>
</form>
<?php if ($success): ?>
    <p style="color:green;">Report submitted successfully!</p>
<?php endif; ?>
