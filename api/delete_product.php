<?php
require_once __DIR__ . '/../core/role_guard.php';
require_role(['artisan']);
require_once __DIR__ . '/../config/db.php';
$db = getDbConnection();

$artisan_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];
    
    $stmt = $db->prepare("DELETE FROM products WHERE id = ? AND user_id = ?");
    
    if ($stmt->execute([$product_id, $artisan_id])) {
        if ($stmt->rowCount() > 0) {
            $_SESSION['success_message'] = "Product deleted successfully.";
        } else {
            $_SESSION['error_message'] = "You do not have permission to delete this product.";
        }
    } else {
        $_SESSION['error_message'] = "Failed to delete product.";
    }
} else {
    $_SESSION['error_message'] = "Invalid request.";
}

header('Location: dashboard_artisan.php');
exit();
?>