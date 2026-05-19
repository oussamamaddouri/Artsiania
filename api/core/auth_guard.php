<?php
// Ensure session is started on any page that uses this guard
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    // Store a message to display on the login page
    $_SESSION['error_message'] = "You must be logged in to access that page.";
    
    // Redirect them to the login page
    header("Location: login.php");
    exit(); // Stop script execution immediately
}
?>
