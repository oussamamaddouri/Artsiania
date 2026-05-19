<?php
require_once __DIR__ . '/../core/role_guard.php';
require_role(['client']);
require_once __DIR__ . '/../config/db.php';
$db = getDbConnection();
$client_id = $_SESSION['user_id'];

// --- Fetch all reviews written by this client ---
$stmt_reviews = $db->prepare(
    "SELECT r.id, r.rating, r.comment, r.created_at, p.name as product_name 
     FROM reviews r
     JOIN products p ON r.product_id = p.id
     WHERE r.user_id = ?
     ORDER BY r.created_at DESC"
);
$stmt_reviews->execute([$client_id]);
$reviews = $stmt_reviews->fetchAll();

require_once __DIR__ . '/../src/views/templates/header.php';
?>

<div class="container my-4">
    <h1 class="h2 mb-4">My Dashboard</h1>
    <p class="lead">Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
    
    <!-- Session Message Display -->
    <?php if (isset($_SESSION['success_message'])): ?><div class="alert alert-success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div><?php endif; ?>
    <?php if (isset($_SESSION['error_message'])): ?><div class="alert alert-danger"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div><?php endif; ?>
    
    <!-- Quick Actions -->
    <div class="row mb-5">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Order History</h5>
                    <p class="card-text">View the status of all your past and current orders.</p>
                    <a href="order_history.php" class="btn btn-primary">View My Orders</a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Section: Manage My Reviews -->
    <h3 class="h4 border-top pt-4" id="reviews-section">Manage My Reviews</h3>
    <div class="table-responsive">
        <?php if(empty($reviews)): ?>
            <p>You have not written any reviews yet. You can leave a review from your <a href="order_history.php">Order History</a> page after a purchase.</p>
        <?php else: ?>
        <table class="table table-striped">
            <thead><tr><th>Product</th><th>Rating</th><th>Comment</th><th>Date</th><th>Actions</th></tr></thead>
            <tbody>
                <?php foreach($reviews as $review): ?>
                <tr>
                    <td><?php echo htmlspecialchars($review['product_name']); ?></td>
                    <td><?php echo str_repeat('★', $review['rating']); ?></td>
                    <td><?php echo htmlspecialchars($review['comment']); ?></td>
                    <td><?php echo date('F j, Y', strtotime($review['created_at'])); ?></td>
                    <td>
                        <a href="edit_review.php?id=<?php echo $review['id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                        <form action="delete_review.php" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this review?');">
                            <input type="hidden" name="review_id" value="<?php echo $review['id']; ?>">
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../src/views/templates/footer.php'; ?>