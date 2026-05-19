<?php
// === SECURITY AND INITIALIZATION ===
require_once __DIR__ . '/../core/role_guard.php';
require_role(['artisan']);

require_once __DIR__ . '/../config/db.php';
$db = getDbConnection();
$artisan_id = $_SESSION['user_id'];


// =============================================================
// THE FIX: Get the Order ID from the URL at the very top.
// This makes it available for both POST handling and GET display.
// =============================================================
if (!isset($_GET['order_id'])) {
    // If no order ID is provided in the URL, we can't do anything.
    $_SESSION['error_message'] = "No order specified.";
    header('Location: dashboard_artisan.php');
    exit();
}
$order_id = (int)$_GET['order_id'];


// =============================================================
// HANDLE THE FORM SUBMISSION
// =============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['livreur_id'])) {
    $livreur_id = (int)$_POST['livreur_id'];
    
    // Now the $order_id variable is correctly defined and available here.
    $stmt = $db->prepare("UPDATE orders SET livreur_id = ?, status = 'ready_for_delivery' WHERE id = ?");
    if ($stmt->execute([$livreur_id, $order_id])) {
        $_SESSION['success_message'] = "Order #" . $order_id . " has been assigned and is ready for pickup.";
    } else {
        $_SESSION['error_message'] = "Failed to assign the order.";
    }
    header('Location: dashboard_artisan.php');
    exit();
}


// =============================================================
// FETCH DATA FOR DISPLAYING THE PAGE
// =============================================================
$stmt_order = $db->prepare("SELECT * FROM orders WHERE id = ?");
$stmt_order->execute([$order_id]);
$order = $stmt_order->fetch();

if (!$order) { die("Order not found."); }

// Fetch available livreurs
$livreurs = $db->query("SELECT id, username FROM users WHERE role = 'livreur'")->fetchAll();

require_once __DIR__ . '/../src/views/templates/header.php';
?>

<div class="container my-4">
    <h1 class="h2">Prepare Delivery for Order #<?php echo htmlspecialchars($order_id); ?></h1>
    <p class="lead">Assign a delivery person for this order.</p>

    <div class="card">
        <div class="card-header">Order Details</div>
        <div class="card-body">
            <p><strong>Order ID:</strong> <?php echo htmlspecialchars($order['id']); ?></p>
            <p><strong>Total Amount:</strong> $<?php echo number_format($order['total_amount'], 2); ?></p>
            <p><strong>Date Placed:</strong> <?php echo date('F j, Y', strtotime($order['created_at'])); ?></p>
        </div>
    </div>
    
    <div class="card mt-4">
        <div class="card-body">
            <h5 class="card-title">Assign to a Delivery Person</h5>
            <form action="prepare_delivery.php?order_id=<?php echo $order_id; ?>" method="POST">
                <div class="mb-3">
                    <label for="livreur_id" class="form-label">Select a Livreur:</label>
                    <select class="form-select" id="livreur_id" name="livreur_id" required>
                        <option value="">-- Choose a delivery person --</option>
                        <?php foreach ($livreurs as $livreur): ?>
                            <option value="<?php echo $livreur['id']; ?>">
                                <?php echo htmlspecialchars($livreur['username']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Confirm and Assign</button>
                <a href="dashboard_artisan.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../src/views/templates/footer.php'; ?>