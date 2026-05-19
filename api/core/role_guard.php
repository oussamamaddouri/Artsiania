<?php
// This file defines a REUSABLE FUNCTION to protect pages.

function require_role(array $allowed_roles) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // If user is not logged in OR their role is not in the allowed list
    if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], $allowed_roles)) {
        $_SESSION['error_message'] = "Access Denied. You do not have permission to view that page.";
        
        // Always redirect to the public login page for security
        header("Location: login.php");
        exit();
    }
}
?>
