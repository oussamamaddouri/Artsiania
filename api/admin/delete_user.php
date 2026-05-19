<?php
require_once __DIR__ . '/../core/admin_guard.php';
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
    
    // Prevent the admin from deleting their own account
    if ($user_id == $_SESSION['user_id']) {
        $_SESSION['error_message'] = "You cannot delete your own account.";
        header('Location: dashboard.php?page=users');
        exit();
    }
    
    try {
        $db = getDbConnection();
        $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
        if ($stmt->execute([$user_id])) {
            $_SESSION['success_message'] = "User deleted successfully.";
        } else {
            $_SESSION['error_message'] = "Failed to delete user.";
        }
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "An error occurred: " . $e->getMessage();
    }
} else {
    $_SESSION['error_message'] = "Invalid request.";
}

header('Location: dashboard.php#users-section');
exit();
