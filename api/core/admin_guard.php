<?php
// Ensure session is started on any page that uses this guard
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Check if the user is not logged in OR 2. if they are not an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    // Set an error message to display on the public login page
    $_SESSION['error_message'] = "Access Denied. You do not have permission to view that page.";
    
    // Redirect them to the PUBLIC login page
    header("Location: ../public/login.php"); 
    exit(); // Stop all script execution
}
?>
