<?php
// === SECURITY AND INITIALIZATION ===
require_once __DIR__ . '/../core/role_guard.php';
require_role(['livreur']); // Only the 'livreur' role is allowed.

require_once __DIR__ . '/../config/db.php';
$db = getDbConnection();
$livreur_id = $_SESSION['user_id']; // Get the ID of the currently logged-in livreur


// =================================================================
// SECTION 1: HANDLE FORM SUBMISSIONS (No change here)
// =================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && isset($_POST['order_id'])) {
    // ... [The existing code for handling 'mark_shipped' and 'mark_delivered' is perfect] ...
    $order_id = $_POST['order_id'];
    $new_status = ''; $success_verb = '';
    if ($_POST['action'] === 'mark_shipped') { $new_status = 'shipped'; $success_verb = 'shipped'; } 
    elseif ($_POST['action'] === 'mark_delivered') { $new_status = 'delivered'; $success_verb = 'delivered'; }

    if (!empty($new_status)) {
        // We add a security check: a livreur can only update an order that is assigned to them.
        $stmt = $db->prepare("UPDATE orders SET status = ? WHERE id = ? AND livreur_id = ?");
        if ($stmt->execute([$new_status, $order_id, $livreur_id])) {
            $_SESSION['success_message'] = "Order #" . $order_id . " marked as " . $success_verb . ".";
        } else { $_SESSION['error_message'] = "Failed to update order or you do not have permission."; }
    }
    header('Location: dashboard_livreur.php'); exit();
}


// =================================================================
// SECTION 2: FETCH PERSONALIZED DATA FOR DISPLAY
// =================================================================
// --- NEW, SMARTER QUERY for orders ready for pickup ---
$stmt_ready = $db->prepare(
    "SELECT o.id, u.username as client_name, o.created_at FROM orders o 
     JOIN users u ON o.user_id = u.id 
     WHERE o.status = 'ready_for_delivery' AND o.livreur_id = ? 
     ORDER BY o.created_at ASC"
);
$stmt_ready->execute([$livreur_id]);
$ready_orders = $stmt_ready->fetchAll();

// --- NEW, SMARTER QUERY for orders in transit ---
$stmt_shipped = $db->prepare(
    "SELECT o.id, u.username as client_name, o.created_at FROM orders o 
     JOIN users u ON o.user_id = u.id 
     WHERE o.status = 'shipped' AND o.livreur_id = ? 
     ORDER BY o.created_at ASC"
);
$stmt_shipped->execute([$livreur_id]);
$shipped_orders = $stmt_shipped->fetchAll();


// =================================================================
// SECTION 3: RENDER THE HTML PAGE (No change in the HTML structure)
// =================================================================
require_once __DIR__ . '/../src/views/templates/header.php';
?>

<div class="container my-4">
    <!-- The rest of the HTML is identical to before, it will now display the filtered data -->
    <h1 class="h2 mb-4">Delivery Dashboard</h1>
    <p class="lead">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
    <?php if (isset($_SESSION['success_message'])): ?><div class="alert alert-success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div><?php endif; ?>
    
    <h3 class="h4 mt-5">My Assigned Pickups</h3>
    <div class="table-responsive mb-5">
        <?php if(empty($ready_orders)): ?><p>You have no assigned orders to pick up.</p><?php else: ?>
        <table class="table table-striped"><thead><tr><th>Order #</th><th>Client</th><th>Order Date</th><th>Action</th></tr></thead><tbody>
        <?php foreach($ready_orders as $order): ?>
        <tr><td><?php echo $order['id']; ?></td><td><?php echo htmlspecialchars($order['client_name']); ?></td><td><?php echo date('F j, Y', strtotime($order['created_at'])); ?></td><td>
            <form action="dashboard_livreur.php" method="POST"><input type="hidden" name="action" value="mark_shipped"><input type="hidden" name="order_id" value="<?php echo $order['id']; ?>"><button type="submit" class="btn btn-primary btn-sm">Pick Up & Ship</button></form>
        </td></tr>
        <?php endforeach; ?>
        </tbody></table>
        <?php endif; ?>
    </div>

    <h3 class="h4 mt-5">My Deliveries in Transit</h3>
    <div class="table-responsive">
        <?php if(empty($shipped_orders)): ?><p>You have no deliveries in transit.</p><?php else: ?>
        <table class="table table-striped"><thead><tr><th>Order #</th><th>Client</th><th>Order Date</th><th>Action</th></tr></thead><tbody>
        <?php foreach($shipped_orders as $order): ?>
        <tr><td><?php echo $order['id']; ?></td><td><?php echo htmlspecialchars($order['client_name']); ?></td><td><?php echo date('F j, Y', strtotime($order['created_at'])); ?></td><td>
            <form action="dashboard_livreur.php" method="POST"><input type="hidden" name="action" value="mark_delivered"><input type="hidden" name="order_id" value="<?php echo $order['id']; ?>"><button type="submit" class="btn btn-success btn-sm">Mark as Delivered</button></form>
        </td></tr>
        <?php endforeach; ?>
        </tbody></table>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../src/views/templates/footer.php'; ?>