<?php
session_start();
include 'db.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$successMessage = "";
if (isset($_GET['success']) && $_GET['success'] == 1) {
    $successMessage = "Item submitted successfully!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: url('xavier_university_ateneo_de_cagayan_cover.jpg') no-repeat center center fixed;
            background-size: cover;
            color: #333;
        }

        .header {
            background-color: rgba(14, 26, 64, 0.85);
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
            font-size: 22px;
            font-weight: bold;
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
            font-weight: bold;
            padding: 8px 16px;
            border: 1px solid white;
            border-radius: 4px;
            transition: background-color 0.3s;
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

        .nav {
            text-align: center;
            margin-bottom: 20px;
        }

        .nav a {
            margin: 0 15px;
            text-decoration: none;
            font-weight: bold;
            color: #002147;
            cursor: pointer;
        }

        .section {
            display: none;
            animation: slideFade 0.4s ease-in-out forwards; 
        }

        .section.active {
            display: block; 
        }

        @keyframes slideFade {
            from {
                opacity: 0;
                transform: translateX(30px); 
            }
            to {
                opacity: 1;
                transform: translateX(0); 
            }
        }

        input[type="text"], textarea, input[type="file"] {
            width: 100%;
            padding: 10px;
            margin: 8px 0 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            padding: 12px;
            width: 100%;
            background-color: #002147;
            color: white;
            border: none;
            border-radius: 4px;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background-color: #003366;
        }

        img#imagePreview {
            display: none;
            max-width: 300px;
            margin-top: 10px;
        }

        .item-box {
            display: flex;
            align-items: flex-start;
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            background-color: #f9f9f9;
            gap: 20px;
        }

        .item-box img {
            width: 120px;
            height: auto;
            border-radius: 4px;
            object-fit: cover;
            cursor: pointer;
        }

        .item-info {
            flex: 1;
        }

        .success {
            color: green;
            text-align: center;
            font-weight: bold;
        }

        #imageModal {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.8);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        #modalImage {
            max-width: 70%;  
            max-height: 70%; 
            border: 6px solid white;
            border-radius: 10px;
            box-shadow: 0 0 30px rgba(255, 255, 255, 0.7);
        }

        #imageModal span {
            position: absolute;
            top: 20px;
            right: 30px;
            font-size: 30px;
            color: white;
            cursor: pointer;
        }

        #searchInput {
            padding: 10px;
            width: 100%;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
    </style> 
</head>
<body>

<div class="header">
    <img src="XU_logo_type_ver_2.png" alt="Xavier University Logo">
    <div style="flex-grow: 1;">
        <div class="header-title">Xavier University</div>
        <div class="subtitle">Lost and Found System</div>
    </div>
    <div class="logout">
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="container">
    <h2 style="text-align:center;">Student Dashboard</h2>

    <?php if (!empty($successMessage)): ?>
        <div id="successMessage">
            <p class="success"><?= $successMessage ?></p>
        </div>
    <?php endif; ?>

    <div class="nav">
        <a href="#report" onclick="showSection('reportSection')">Report Lost Item</a> |
        <a href="#view" onclick="showSection('viewSection')">View Lost Items</a>
    </div>

    <div id="reportSection" class="section active">
        <h3>Turn Over a Lost Item</h3>
        <form action="upload.php" method="POST" enctype="multipart/form-data">
            <input type="text" name="item_name" placeholder="Item Name" required>
            <textarea name="description" placeholder="Description" required></textarea>
            <input type="file" name="photo" accept="image/*" id="photo" onchange="previewImage(event)" required>
            <img id="imagePreview" alt="Image Preview">
            <div style="display: flex; gap: 10px; justify-content: space-between;">
                <button type="submit" name="submit" style="flex: 1;">Submit</button>
                <button type="reset" style="flex: 1;" onclick="clearPreview()">Cancel</button>
            </div>
        </form>
    </div>

    <div id="viewSection" class="section">
        <h3>Lost Items</h3>
        <input type="text" id="searchInput" placeholder="Search for an item..." onkeyup="filterItems()">
        <select id="statusFilter" onchange="filterItems()" style="margin-bottom: 20px; padding: 10px; border-radius: 4px; border: 1px solid #ccc;">
            <option value="all">All</option>
            <option value="unclaimed">Unclaimed</option>
            <option value="claimed">Claimed</option>
        </select>
        <div id="itemsContainer">
            <?php
            $sql = "SELECT * FROM lost_items ORDER BY timestamp DESC";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $itemName = htmlspecialchars($row['item_name']);
                    $status = $row['is_claimed'] ? "Claimed" : "Unclaimed";
                    $color = $row['is_claimed'] ? "#006400" : "#a30000";

                    echo "<div class='item-box'>";
                    echo "<div><img src='" . $row['photo_path'] . "' alt='Item' onclick='openImage(this.src)'></div>";
                    echo "<div class='item-info'>";
                    echo "<strong class='item-name'>$itemName</strong><br>";
                    echo "<p>Status: <strong style='color: $color;'>$status</strong></p>";
                    echo "</div>";
                    echo "</div>";
                }
            } else {
                echo "<p style='text-align:center;'>No lost items found.</p>";
            }
            ?>
        </div>
    </div>
</div>

<div id="imageModal">
    <span onclick="closeModal()">&times;</span>
    <img id="modalImage" src="">
</div>

<script>

    function clearPreview() {
        const image = document.getElementById("imagePreview");
        image.style.display = "none";
        image.src = "";
    }

    function previewImage(event) {
        const image = document.getElementById("imagePreview");
        image.style.display = "block";
        image.src = URL.createObjectURL(event.target.files[0]);
    }

    function showSection(id) {
        document.querySelectorAll('.section').forEach(section => {
            section.classList.remove('active');
        });
        document.getElementById(id).classList.add('active');

        const successMsg = document.getElementById("successMessage");
        if (id === 'viewSection' && successMsg) {
            successMsg.style.display = "none";
        }
    }

    function openImage(src) {
        const modal = document.getElementById("imageModal");
        const modalImg = document.getElementById("modalImage");
        modal.style.display = "flex";
        modalImg.src = src;
    }

    function closeModal() {
        document.getElementById("imageModal").style.display = "none";
    }

    function filterItems() {
        const textFilter = document.getElementById("searchInput").value.toLowerCase();
        const statusFilter = document.getElementById("statusFilter").value;
        const items = document.querySelectorAll("#itemsContainer .item-box");

        items.forEach(item => {
            const itemName = item.querySelector(".item-name").textContent.toLowerCase();
            const statusText = item.querySelector("p strong").textContent.toLowerCase(); // "claimed" or "unclaimed"

            const nameMatch = itemName.includes(textFilter);
            const statusMatch = (statusFilter === "all") || (statusText === statusFilter);

            item.style.display = nameMatch && statusMatch ? "" : "none";
        });
    }

</script>

</body>
</html>

<?php $conn->close(); ?>
