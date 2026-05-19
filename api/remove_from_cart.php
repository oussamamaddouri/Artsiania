<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if a product ID was sent and the cart exists
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id']) && isset($_SESSION['cart'])) {
    $product_id_to_remove = (int)$_POST['product_id'];
    
    // Unset the item from the cart array
    unset($_SESSION['cart'][$product_id_to_remove]);
    
    $_SESSION['success_message'] = "Item removed from cart.";
}

// Redirect back to the cart page to show the result
header('Location: cart.php');
exit();
?>
