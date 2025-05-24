<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['mark_claimed'])) {
        $item_id = $_POST['item_id'];
        $claimed_by = trim($_POST['claimed_by'] ?? '');

        if ($claimed_by !== '') {
            $stmt = $conn->prepare("UPDATE lost_items SET is_claimed = 1, claimed_by = ? WHERE id = ?");
            $stmt->bind_param("si", $claimed_by, $item_id);
            $stmt->execute();
            $stmt->close();
        }
        header("Location: admin_dashboard.php");
        exit();
    }

    if (isset($_POST['delete_post'])) {
        $item_id = $_POST['item_id'];
        $stmt = $conn->prepare("DELETE FROM lost_items WHERE id = ?");
        $stmt->bind_param("i", $item_id);
        $stmt->execute();
        $stmt->close();

        header("Location: admin_dashboard.php");
        exit();
    }
}

$sql = "SELECT lost_items.*, students.id AS student_id, students.last_name 
        FROM lost_items 
        LEFT JOIN students ON lost_items.student_id = students.id 
        ORDER BY lost_items.timestamp DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background: url('xavier_university_ateneo_de_cagayan_cover.jpg') no-repeat center center fixed;
            background-size: cover;
            color: #333;
        }

        .header {
            background-color: #0e1a40;
            color: white;
            padding: 20px 40px;
            display: flex;
            align-items: center;
            gap: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }

        .header img {
            height: 60px;
        }

        .header-title {
            font-size: 24px;
            font-weight: 600;
        }

        .subtitle {
            font-size: 14px;
            color: #ccc;
        }

        .logout {
            margin-left: auto;
        }

        .logout a {
            color: white;
            text-decoration: none;
            font-weight: 600;
            padding: 8px 18px;
            border: 2px solid white;
            border-radius: 6px;
            transition: 0.3s;
        }

        .logout a:hover {
            background-color: white;
            color: #0e1a40;
        }

        .container {
            max-width: 700px;
            margin: 40px auto;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
        }

        h2 {
            text-align: center;
            font-size: 30px;
            margin-bottom: 30px;
            color: #0e1a40;
        }

        #searchInput {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            margin-bottom: 10px; /* space below search input */
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        #statusFilter {
            width: 17%;
            padding: 10px;
            font-size: 16px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        .item-box {
            display: grid;
            grid-template-columns: 140px 1fr;
            gap: 20px;
            align-items: flex-start;
            margin-bottom: 30px;
            padding: 20px;
            border: 1px solid #e0e0e0;
            border-radius: 12px;
            background-color: #fcfcfc;
        }

        .item-box img {
            width: 100%;
            height: 140px;
            border-radius: 8px;
            object-fit: cover;
            border: 1px solid #ccc;
        }

        .item-info {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .item-info strong {
            font-size: 20px;
            color: #0e1a40;
        }

        .item-info p {
            margin: 0;
            font-size: 15px;
        }

        .status {
            font-weight: 600;
            color: #006400;
        }

        .status.unclaimed {
            color: #a30000;
        }

        .claim-form {
            margin-top: 10px;
        }

        .claim-form input[type="text"] {
            padding: 6px 10px;
            margin-right: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            width: 95%;
            font-size: 14px;
        }

        .side-by-side-buttons {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        .side-by-side-buttons button {
            flex: 1;
            padding: 8px 18px;
            font-weight: 600;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s;
            color: white;
        }

        .side-by-side-buttons button[name="mark_claimed"] {
            background-color: #0e1a40;
        }

        .side-by-side-buttons button[name="mark_claimed"]:hover {
            background-color: #003366;
        }

        .side-by-side-buttons button[name="delete_post"] {
            background-color: #a30000;
        }

        .side-by-side-buttons button[name="delete_post"]:hover {
            background-color: #7a0000;
        }

        .no-items {
            text-align: center;
            font-size: 18px;
            color: #666;
        }
    </style>
    <script>
        function confirmAction(event, form) {
            const btnName = event.submitter.name;
            const claimantInput = form.claimed_by;
            const claimant = claimantInput ? claimantInput.value.trim() : '';

            if (btnName === 'mark_claimed') {
                if (!claimant) {
                    alert("Please enter the claimant's name.");
                    event.preventDefault();
                    return false;
                }
                return confirm("Are you sure you want to mark this item as claimed?");
            }

            if (btnName === 'delete_post') {
                return confirm("Are you sure you want to delete this post?");
            }

            return true;
        }

        function filterItems() {
            const searchInput = document.getElementById("searchInput").value.toLowerCase();
            const statusFilter = document.getElementById("statusFilter").value;
            const items = document.querySelectorAll("#itemsContainer .item-box");
            let visibleCount = 0;

            items.forEach(item => {
                const text = item.innerText.toLowerCase();
                const isClaimed = item.querySelector(".status").classList.contains("unclaimed") ? false : true;

                // Check status filter
                if (statusFilter === "claimed" && !isClaimed) {
                    item.style.display = "none";
                    return;
                } 
                if (statusFilter === "unclaimed" && isClaimed) {
                    item.style.display = "none";
                    return;
                }

                // Check search filter
                if (text.includes(searchInput)) {
                    item.style.display = "";
                    visibleCount++;
                } else {
                    item.style.display = "none";
                }
            });

            document.getElementById("noResults").style.display = (visibleCount === 0) ? "block" : "none";
        }
    </script>
</head>
<body>
    <div class="header">
        <img src="XU_logo_type_ver_2.png" alt="Xavier University Logo" />
        <div style="flex-grow: 1;">
            <div class="header-title">Xavier University</div>
            <div class="subtitle">Lost and Found System</div>
        </div>
        <div class="logout">
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <div class="container">
        <h2>Admin Dashboard</h2>
        <input type="text" id="searchInput" placeholder="Search items..." onkeyup="filterItems()" />
        <select id="statusFilter" onchange="filterItems()">
            <option value="all">All</option>
            <option value="claimed">Claimed</option>
            <option value="unclaimed">Unclaimed</option>
        </select>

        <?php
        if ($result->num_rows > 0) {
            echo "<div id='itemsContainer'>";
            while ($row = $result->fetch_assoc()) {
                $submittedBy = $row['student_id'] ? htmlspecialchars($row['student_id']) . " (" . htmlspecialchars($row['last_name']) . ")" : "Unknown";
                $statusClass = $row['is_claimed'] ? "status" : "status unclaimed";
                $statusText = $row['is_claimed'] ? "Claimed" : "Unclaimed";

                echo "<div class='item-box'>";
                echo "<img src='" . htmlspecialchars($row['photo_path']) . "' alt='Item' />";
                echo "<div class='item-info'>";
                echo "<strong>" . htmlspecialchars($row['item_name']) . "</strong>";
                echo "<p>" . nl2br(htmlspecialchars($row['description'])) . "</p>";
                echo "<p><i class='fas fa-user'></i> Submitted by: " . $submittedBy . "</p>";
                echo "<p><i class='fas fa-clock'></i> Submitted on: " . date("Y-m-d H:i:s", strtotime($row['timestamp'])) . "</p>";
                echo "<p class='$statusClass'>Status: $statusText</p>";

                if ($row['is_claimed'] && !empty($row['claimed_by'])) {
                    echo "<p><i class='fas fa-check-circle'></i> Claimed By: " . htmlspecialchars($row['claimed_by']) . "</p>";
                }

                echo "<form method='POST' class='claim-form' onsubmit='return confirmAction(event, this)'>";
                echo "<input type='hidden' name='item_id' value='" . $row['id'] . "'>";
                if (!$row['is_claimed']) {
                    echo "<input type='text' name='claimed_by' placeholder='Enter name of claimant'>";
                }
                echo "<div class='side-by-side-buttons'>";
                if (!$row['is_claimed']) {
                    echo "<button type='submit' name='mark_claimed'><i class='fas fa-check'></i> Mark as Claimed</button>";
                }
                echo "<button type='submit' name='delete_post'><i class='fas fa-trash'></i> Delete Post</button>";
                echo "</div>";
                echo "</form>";

                echo "</div></div>";
            }
            echo "</div>";
            echo "<p id='noResults' class='no-items' style='display: none;'>No matching items found.</p>";
        } else {
            echo "<p class='no-items'>No lost items found.</p>";
        }
        ?>
    </div>
</body>
</html>
<?php $conn->close(); ?>
