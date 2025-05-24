<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($username === 'admin' && $password === 'password') {
        $_SESSION['admin_logged_in'] = true;
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $password_upper = strtoupper($password);
        $sql = "SELECT * FROM students WHERE id = ? AND last_name = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $username, $password_upper);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $_SESSION['student_id'] = $username;
            header("Location: student_dashboard.php");
            exit();
        } else {
            $error = "Invalid login credentials!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <!-- Font Awesome for eye icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>

    <style>
    body {
        font-family: 'Segoe UI', sans-serif;
        background: url('xavier_university_ateneo_de_cagayan_cover.jpg') no-repeat center center fixed;
        background-size: cover;
        color: white;
        margin: 0;
        padding: 0;
        position: relative;
    }

    .header, form {
        position: relative;
        z-index: 1;
    }

    .header {
        background-color: rgba(14, 26, 64, 0.85);
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
        color: #FFFFFF;
    }

    .subtitle {
        font-size: 14px;
        color: #ccc;
        margin-top: 4px;
    }

    form {
        max-width: 400px;
        margin: 60px auto;
        background-color: white;
        padding: 30px;
        border-radius: 10px;
        color: #333;
    }

    h2 {
        text-align: center;
        color: #0e1a40;
        margin-bottom: 25px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    label {
        display: block;
        margin-bottom: 6px;
        font-weight: bold;
    }

    input[type="text"],
    input[type="password"] {
        width: 100%;
        padding: 10px;
        font-size: 16px;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
    }

    .password-wrapper {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    #eye-icon {
        cursor: pointer;
        color: #555;
        font-size: 18px;
    }

    button {
        width: 100%;
        padding: 12px;
        font-size: 16px;
        background-color: #002147;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-weight: bold;
    }

    button:hover {
        background-color: #003366;
    }

    .error {
        color: red;
        text-align: center;
        margin-bottom: 15px;
    }
    </style>
</head>
<body>

    <!-- HEADER -->
    <div class="header">
        <img src="XU_logo_type_ver_2.png" alt="Xavier University Logo">
        <div>
            <div class="header-title">Xavier University</div>
            <div class="subtitle">Lost and Found System</div>
        </div>
    </div>

    <form method="POST" action="login.php">
        <h2>Login</h2>

        <?php if (isset($error)): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <div class="form-group">
            <label for="username">Username (Student ID or Admin):</label>
            <input type="text" id="username" name="username" required value="<?= isset($username) ? htmlspecialchars($username) : '' ?>">
        </div>

        <div class="form-group">
            <label for="password">Password:</label>
            <div class="password-wrapper">
                <input type="password" id="password" name="password" required placeholder="Enter password">
                <span id="eye-icon"><i class="fas fa-eye"></i></span>
            </div>
        </div>

        <button type="submit">Login</button>
    </form>

    <!-- JavaScript for toggle password visibility -->
    <script>
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eye-icon').querySelector('i');

        document.getElementById('eye-icon').addEventListener('click', function () {
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        });
    </script>

</body>
</html>
