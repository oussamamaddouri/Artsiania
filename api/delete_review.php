<?php
require_once __DIR__ . '/../core/role_guard.php';
require_role(['client']);
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['review_id'])) {
    $review_id = $_POST['review_id'];
    $client_id = $_SESSION['user_id'];
    
    $db = getDbConnection();
    // Security Check: only delete where the ID and user_id both match
    $stmt = $db->prepare("DELETE FROM reviews WHERE id = ? AND user_id = ?");
    $stmt->execute([$review_id, $client_id]);
    
    // Check if a row was actually deleted
    if ($stmt->rowCount() > 0) {
        $_SESSION['success_message'] = "Review deleted successfully.";
    } else {
        $_SESSION['error_message'] = "Failed to delete review or you do not have permission.";
    }
} else {
    $_SESSION['error_message'] = "Invalid request.";
}

header('Location: dashboard_client.php#reviews-section');
exit();
?>
