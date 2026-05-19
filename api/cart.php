<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/db.php';
$db = getDbConnection();

$cart_items_details = [];
$grand_total = 0;

// Check if the cart exists and is not empty
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $product_ids = array_keys($_SESSION['cart']);
    
    // Create placeholders for the IN clause (?, ?, ?)
    $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
    
    // Fetch all product details from the database in one query
    $stmt = $db->prepare("SELECT id, name, price, image_path FROM products WHERE id IN ($placeholders)");
    $stmt->execute($product_ids);
    $products_from_db = $stmt->fetchAll();
    
    // Combine database details with session quantities
    foreach ($products_from_db as $product) {
        $quantity = $_SESSION['cart'][$product['id']];
        $subtotal = $product['price'] * $quantity;
        $grand_total += $subtotal;
        
        $cart_items_details[] = [
            'id' => $product['id'],
            'name' => $product['name'],
            'price' => $product['price'],
            'image_path' => $product['image_path'],
            'quantity' => $quantity,
            'subtotal' => $subtotal
        ];
    }
}

require_once __DIR__ . '/../src/views/templates/header.php';
?>

<div class="container" style="margin-top: 3rem; margin-bottom: 3rem;">
    <h1 class="section-title text-center mb-5">Your Shopping Cart</h1>

    <?php if (empty($cart_items_details)): ?>
        <div class="alert alert-info text-center">
            Your cart is currently empty. <a href="products.php">Start shopping!</a>
        </div>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 10%;">Image</th>
                    <th style="width: 35%;">Product</th>
                    <th style="width: 15%;">Price</th>
                    <th style="width: 15%;">Quantity</th>
                    <th style="width: 15%;">Subtotal</th>
                    <th style="width: 10%;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cart_items_details as $item): ?>
                <tr>
                    <td><img src="assets/images/<?php echo htmlspecialchars($item['image_path']); ?>" width="70" alt=""></td>
                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                    <td>$<?php echo number_format($item['price'], 2); ?></td>
                    <td><?php echo $item['quantity']; ?></td> <!-- For now, just display. We'll add 'update' later. -->
                    <td>$<?php echo number_format($item['subtotal'], 2); ?></td>
                    <td>
                        <form action="remove_from_cart.php" method="POST">
                            <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                            <button type="submit" class="btn btn-danger btn-sm">Remove</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr class="fw-bold">
                    <td colspan="4" class="text-end">Grand Total:</td>
                    <td colspan="2">$<?php echo number_format($grand_total, 2); ?></td>
                </tr>
            </tfoot>
        </table>

        <div class="text-end mt-4">
            <a href="products.php" class="btn btn-secondary">Continue Shopping</a>
            <a href="checkout.php" class="btn btn-primary">Proceed to Checkout</a>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../src/views/templates/footer.php'; ?>
