<?php
session_start();

// Destroy the session and redirect to login page
session_unset();  // Unset all session variables
session_destroy();  // Destroy the session
header("Location: login.php");  // Redirect to the login page (or home page)
exit();
?>
