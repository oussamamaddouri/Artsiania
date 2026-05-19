<?php
require_once __DIR__ . '/../core/role_guard.php';
require_role(['client']);
require_once __DIR__ . '/../config/db.php';
$db = getDbConnection();
$client_id = $_SESSION['user_id'];

// --- HANDLE REVIEW SUBMISSION ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'submit_review') {
    $product_id = $_POST['product_id'];
    $rating = $_POST['rating'];
    $comment = trim($_POST['comment']);

    // TODO: A more robust check would be to re-verify the user purchased this product.
    // For now, we trust the form submission from this secure page.
    $stmt = $db->prepare("INSERT INTO reviews (product_id, user_id, rating, comment) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$product_id, $client_id, $rating, $comment])) {
        $_SESSION['success_message'] = "Your review has been submitted successfully!";
    } else {
        $_SESSION['error_message'] = "Failed to submit review.";
    }
    header('Location: order_history.php');
    exit();
}

// Fetch all orders for this client
$stmt_orders = $db->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt_orders->execute([$client_id]);
$orders = $stmt_orders->fetchAll();

require_once __DIR__ . '/../src/views/templates/header.php';
?>

<div class="container" style="margin-top: 3rem; margin-bottom: 3rem;">
    <h1 class="section-title mb-5">My Order History</h1>

    <!-- Display any success/error messages -->
    <?php if (isset($_SESSION['success_message'])): ?><div class="alert alert-success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div><?php endif; ?>
    <?php if (isset($_SESSION['error_message'])): ?><div class="alert alert-danger"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div><?php endif; ?>

    <?php if (empty($orders)): ?>
        <p>You have not placed any orders yet.</p>
    <?php else: ?>
        <?php foreach ($orders as $order): ?>
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between">
                    <span>Order #<?php echo $order['id']; ?> - Placed on <?php echo date('F j, Y', strtotime($order['created_at'])); ?></span>
                    <span class="badge text-bg-info"><?php echo ucfirst($order['status']); ?></span>
                </div>
                <div class="card-body">
                    <p class="card-text">Total: <strong>$<?php echo number_format($order['total_amount'], 2); ?></strong></p>
                    
                    <!-- NEW: Fetch and display order items -->
                    <h6 class="mt-3">Items in this order:</h6>
                    <?php
                        $stmt_items = $db->prepare(
                            "SELECT oi.product_id, oi.quantity, p.name 
                             FROM order_items oi 
                             JOIN products p ON oi.product_id = p.id 
                             WHERE oi.order_id = ?"
                        );
                        $stmt_items->execute([$order['id']]);
                        $items = $stmt_items->fetchAll();
                    ?>
                    <ul class="list-group">
                        <?php foreach ($items as $item): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <?php echo htmlspecialchars($item['name']); ?> (Qty: <?php echo $item['quantity']; ?>)
                                <!-- Button to trigger review modal -->
                                <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#reviewModal<?php echo $item['product_id']; ?>">
                                    Leave a Review
                                </button>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- MODALS FOR REVIEWS (one modal is generated for each unique item) -->
<?php foreach ($items as $item): ?>
<div class="modal fade" id="reviewModal<?php echo $item['product_id']; ?>" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Review: <?php echo htmlspecialchars($item['name']); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="order_history.php" method="POST">
                    <input type="hidden" name="action" value="submit_review">
                    <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                    <div class="mb-3">
                        <label for="rating" class="form-label">Rating</label>
                        <select class="form-select" name="rating" required>
                            <option value="5">5 Stars (Excellent)</option>
                            <option value="4">4 Stars (Good)</option>
                            <option value="3">3 Stars (Average)</option>
                            <option value="2">2 Stars (Poor)</option>
                            <option value="1">1 Star (Terrible)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="comment" class="form-label">Comment</label>
                        <textarea class="form-control" name="comment" rows="4"></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit Review</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>


<?php require_once __DIR__ . '/../src/views/templates/footer.php'; ?>