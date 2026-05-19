<?php
require_once __DIR__ . '/../core/role_guard.php';
require_role(['client']);
require_once __DIR__ . '/../config/db.php';
$db = getDbConnection();
$client_id = $_SESSION['user_id'];

// --- Get Cart Contents (This part is unchanged) ---
$cart_items_details = [];
$grand_total = 0;
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    // ... [The code to fetch cart items and check stock remains here] ...
    $product_ids = array_keys($_SESSION['cart']);
    $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
    $stmt = $db->prepare("SELECT id, name, price, stock FROM products WHERE id IN ($placeholders)");
    $stmt->execute($product_ids);
    $products_from_db = $stmt->fetchAll();
    foreach ($products_from_db as $product) {
        $quantity = $_SESSION['cart'][$product['id']];
        if ($quantity > $product['stock']) { $_SESSION['error_message'] = "Not enough stock for " . htmlspecialchars($product['name']); header('Location: cart.php'); exit(); }
        $subtotal = $product['price'] * $quantity;
        $grand_total += $subtotal;
        $cart_items_details[] = [ 'id' => $product['id'], 'name' => $product['name'], 'price' => $product['price'], 'quantity' => $quantity, 'subtotal' => $subtotal ];
    }
}

// --- HANDLE THE "PLACE ORDER" FORM SUBMISSION ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($cart_items_details)) {
    try {
        $db->beginTransaction(); // Start a transaction

        // 1. Create the main order record (unchanged)
        $stmt_order = $db->prepare("INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, 'pending')");
        $stmt_order->execute([$client_id, $grand_total]);
        $order_id = $db->lastInsertId();

        // 2. Insert order items and update stock (unchanged)
        $stmt_item = $db->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt_stock = $db->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
        foreach ($cart_items_details as $item) {
            $stmt_item->execute([$order_id, $item['id'], $item['quantity'], $item['price']]);
            $stmt_stock->execute([$item['quantity'], $item['id']]);
        }

        // ========================================================
        // 3. THE NEW, CRITICAL FIX: Insert into transactions table
        // ========================================================
        $payment_method = 'Cash on Delivery'; // For our simulation
        $stmt_trans = $db->prepare(
            "INSERT INTO transactions (order_id, amount, payment_method, status) VALUES (?, ?, ?, 'completed')"
        );
        $stmt_trans->execute([$order_id, $grand_total, $payment_method]);

        $db->commit(); // All queries were successful, save all changes

        // 4. Clear the cart and redirect (unchanged)
        unset($_SESSION['cart']);
        $_SESSION['success_message'] = "Your order (#" . $order_id . ") has been placed successfully!";
        header('Location: order_history.php');
        exit();

    } catch (Exception $e) {
        $db->rollBack(); // Something went wrong, undo everything
        $error_message = "Your order could not be placed. Error: " . $e->getMessage();
    }
}

// --- HTML Section (The rest of the file is unchanged) ---
require_once __DIR__ . '/../src/views/templates/header.php';
?>
<!-- The HTML for the checkout summary and "Place Order" button is here -->
<div class="container" style="margin-top: 3rem; margin-bottom: 3rem;">
    <h1 class="section-title text-center mb-5">Order Checkout</h1>
    <?php if (empty($cart_items_details)): ?>
        <div class="alert alert-warning text-center">Your cart is empty. You cannot proceed to checkout.</div>
    <?php else: ?>
        <div class="row">
            <div class="col-md-8">
                <h4>Order Summary</h4>
                <table class="table">
                    <thead><tr><th>Product</th><th>Quantity</th><th>Subtotal</th></tr></thead><tbody>
                    <?php foreach ($cart_items_details as $item): ?>
                    <tr><td><?php echo htmlspecialchars($item['name']); ?></td><td><?php echo $item['quantity']; ?></td><td>$<?php echo number_format($item['subtotal'], 2); ?></td></tr>
                    <?php endforeach; ?>
                    </tbody><tfoot><tr class="fw-bold"><td colspan="2" class="text-end">Grand Total:</td><td>$<?php echo number_format($grand_total, 2); ?></td></tr></tfoot>
                </table>
            </div>
            <div class="col-md-4">
                <div class="card"><div class="card-body">
                    <h5 class="card-title">Confirm & Pay</h5>
                    <p>Payment Method: Cash on Delivery</p>
                    <form action="checkout.php" method="POST"><div class="d-grid"><button type="submit" class="btn btn-primary btn-lg">Place Order</button></div></form>
                </div></div>
            </div>
        </div>
        <?php if (isset($error_message)): ?><div class="alert alert-danger mt-3"><?php echo $error_message; ?></div><?php endif; ?>
    <?php endif; ?>
</div>
<?php require_once __DIR__ . '/../src/views/templates/footer.php'; ?>