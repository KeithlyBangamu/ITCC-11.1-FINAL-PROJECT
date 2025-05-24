<?php
$conn = new mysqli("localhost", "root", "kiethly1", "last&found");

$result = $conn->query("SELECT * FROM lost_found ORDER BY created_at DESC");

while($row = $result->fetch_assoc()) {
    echo "<div>";
    echo "<h3>" . htmlspecialchars($row['title']) . " (" . $row['status'] . ")</h3>";
    echo "<p>" . htmlspecialchars($row['description']) . "</p>";
    echo "<p>Status: " . ($row['is_claimed'] ? "Claimed" : "Unclaimed") . "</p>";
    echo "<a href='update.php?id=" . $row['id'] . "'>Mark as Claimed</a> | ";
    echo "<a href='delete.php?id=" . $row['id'] . "' onclick=\"return confirm('Delete this item?');\">Delete</a>";
    echo "</div><hr>";
}
?>
