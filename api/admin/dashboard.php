<?php
require_once __DIR__ . '/../../core/admin_guard.php';  // Line 2
require_once __DIR__ . '/../../config/db.php';     // Line 4
$db = getDbConnection();

// =================================================================
// SECTION 1: HANDLE ALL FORM SUBMISSIONS AT THE TOP
// =================================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'edit_user') {
    $user_id = $_POST['user_id'];
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password']; // Do not trim passwords
    $role = $_POST['role'];

    // Base query
    $sql = "UPDATE users SET username = ?, email = ?, role = ?";
    $params = [$username, $email, $role];

    // Check if a new password was provided
    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql .= ", password = ?";
        $params[] = $hashed_password;
    }

    $sql .= " WHERE id = ?";
    $params[] = $user_id;
    
    $stmt = $db->prepare($sql);
    if ($stmt->execute($params)) {
        $_SESSION['success_message'] = 'User updated successfully.';
    } else {
        $_SESSION['error_message'] = 'Failed to update user. Username or email may already be in use.';
    }
    // Redirect back to the users section
    header('Location: dashboard.php#users-section');
    exit();
}
    // --- Handle Add User Submission ---
    if (isset($_POST['action']) && $_POST['action'] === 'add_user') {
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $role = $_POST['role'];

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $db->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$username, $email, $hashed_password, $role])) {
            $_SESSION['success_message'] = 'User created successfully.';
        } else {
            $_SESSION['error_message'] = 'Failed to create user. The username or email might already exist.';
        }
        // Redirect to the same page but with an anchor to the users section
        header('Location: dashboard.php#users-section');
        exit();
    }
    if (isset($_POST['action']) && $_POST['action'] === 'delete_review') {
    $review_id = $_POST['review_id'];
    
    $stmt = $db->prepare("DELETE FROM reviews WHERE id = ?");
    if ($stmt->execute([$review_id])) {
        $_SESSION['success_message'] = 'Review has been deleted successfully.';
    } else {
        $_SESSION['error_message'] = 'Failed to delete the review.';
    }
    // Redirect back to the reviews section
    header('Location: dashboard.php#reviews-section');
    exit();
}
    // --- Handle Product Validation Submission ---
    if (isset($_POST['action']) && $_POST['action'] === 'validate_product') {
        $product_id = $_POST['product_id'];
        $new_status = $_POST['new_status']; // will be 'approved' or 'rejected'

        $stmt = $db->prepare("UPDATE products SET status = ? WHERE id = ?");
        if ($stmt->execute([$new_status, $product_id])) {
            $_SESSION['success_message'] = 'Product status updated successfully.';
        } else {
            $_SESSION['error_message'] = 'Failed to update product status.';
        }
        header('Location: dashboard.php#products-section');
        exit();
    }
}


// =================================================================
// SECTION 2: FETCH ALL DATA NEEDED FOR THE ENTIRE PAGE
// =================================================================
$all_users = $db->query("SELECT id, username, email, role FROM users ORDER BY created_at DESC")->fetchAll();
$pending_products = $db->query(
    "SELECT p.id, p.name, p.price, u.username as artisan_name 
     FROM products p 
     JOIN users u ON p.user_id = u.id 
     WHERE p.status = 'pending' 
     ORDER BY p.created_at ASC"
)->fetchAll();

$stmt_reviews = $db->query(
    "SELECT r.id, r.comment, r.rating, r.created_at, u.username AS user_name, p.name as product_name
     FROM reviews r
     JOIN users u ON r.user_id = u.id
     JOIN products p ON r.product_id = p.id
     ORDER BY r.created_at DESC"
);
$all_reviews = $stmt_reviews->fetchAll();

$stmt_transactions = $db->query(
    "SELECT t.*, o.user_id, u.username
     FROM transactions t
     JOIN orders o ON t.order_id = o.id
     JOIN users u ON o.user_id = u.id
     ORDER BY t.created_at DESC"
);
$all_transactions = $stmt_transactions->fetchAll();

// =================================================================
// SECTION 3: RENDER THE PAGE
// =================================================================
require_once 'templates/admin_header.php';
?>

<!-- Display any success or error messages at the top -->
<?php if (isset($_SESSION['success_message'])): ?>
    <div class="alert alert-success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
<?php endif; ?>
<?php if (isset($_SESSION['error_message'])): ?>
    <div class="alert alert-danger"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
<?php endif; ?>


<!-- SECTION: Welcome -->
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom" id="dashboard-top">
    <h1 class="h2">Dashboard</h1>
</div>
<p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>! All management tools are available on this page.</p>


<!-- SECTION: User Management -->
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 my-4 border-bottom" id="users-section">
    <h1 class="h2">Manage Users</h1>
    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addUserModal">
        Add New User
    </button>
</div>
<div class="table-responsive">
    <table class="table table-striped">
        <thead><tr><th>ID</th><th>Username</th><th>Email</th><th>Role</th><th>Actions</th></tr></thead>
        <tbody>
            <?php foreach ($all_users as $user): ?>
            <tr>
                <td><?php echo $user['id']; ?></td>
                <td><?php echo htmlspecialchars($user['username']); ?></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td><?php echo htmlspecialchars(ucfirst($user['role'])); ?></td>
                <td>
                    <!-- Edit will become a modal later -->
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editUserModal"data-bs-id="<?php echo $user['id']; ?>"data-bs-username="<?php echo htmlspecialchars($user['username']); ?>"data-bs-email="<?php echo htmlspecialchars($user['email']); ?>"data-bs-role="<?php echo htmlspecialchars($user['role']); ?>">Edit</button> 
                    <form action="delete_user.php" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>


<!-- SECTION: Product Validation -->
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 my-4 border-bottom" id="products-section">
    <h1 class="h2">Validate Products Added by Artisans</h1>
</div>
<div class="table-responsive">
    <?php if (empty($pending_products)): ?>
        <p>There are no pending products to validate.</p>
    <?php else: ?>
        <table class="table table-striped">
            <thead><tr><th>Product Name</th><th>Artisan</th><th>Price</th><th>Actions</th></tr></thead>
            <tbody>
                <?php foreach ($pending_products as $product): ?>
                <tr>
                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                    <td><?php echo htmlspecialchars($product['artisan_name']); ?></td>
                    <td>$<?php echo htmlspecialchars($product['price']); ?></td>
                    <td>
                        <form action="dashboard.php" method="POST" class="d-inline">
                            <input type="hidden" name="action" value="validate_product">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <input type="hidden" name="new_status" value="approved">
                            <button type="submit" class="btn btn-success btn-sm">Approve</button>
                        </form>
                        <form action="dashboard.php" method="POST" class="d-inline">
                            <input type="hidden" name="action" value="validate_product">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <input type="hidden" name="new_status" value="rejected">
                            <button type="submit" class="btn btn-warning btn-sm">Reject</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>


<!-- MODAL FOR ADDING A NEW USER (this is hidden by default) -->
<div class="modal fade" id="addUserModal" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Add New User</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <form action="dashboard.php" method="POST">
            <input type="hidden" name="action" value="add_user">
            <!-- Username, Email, Password, Role fields... -->
            <div class="mb-3"><label>Username</label><input type="text" class="form-control" name="username" required></div>
            <div class="mb-3"><label>Email</label><input type="email" class="form-control" name="email" required></div>
            <div class="mb-3"><label>Password</label><input type="password" class="form-control" name="password" required></div>
            <div class="mb-3"><label>Role</label><select class="form-select" name="role" required><option value="client">Client</option><option value="artisan">Artisan</option><option value="livreur">Livreur</option><option value="admin">Admin</option></select></div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Create User</button></div>
        </form>
      </div>
  </div></div>
</div>


<!-- ============================================== -->
<!-- MODAL FOR EDITING A USER -->
<!-- ============================================== -->
<div class="modal fade" id="editUserModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="editUserForm" action="dashboard.php#users-section" method="POST">
            <input type="hidden" name="action" value="edit_user">
            <input type="hidden" name="user_id" id="edit_user_id">
            
            <div class="mb-3">
                <label for="edit_username" class="form-label">Username</label>
                <input type="text" class="form-control" id="edit_username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="edit_email" class="form-label">Email</label>
                <input type="email" class="form-control" id="edit_email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="edit_password" class="form-label">New Password</label>
                <input type="password" class="form-control" id="edit_password" name="password">
                <small class="form-text text-muted">Leave blank to keep the current password.</small>
            </div>
            <div class="mb-3">
                <label for="edit_role" class="form-label">Role</label>
                <select class="form-select" id="edit_role" name="role" required>
                    <option value="client">Client</option>
                    <option value="artisan">Artisan</option>
                    <option value="livreur">Livreur</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>



<!-- ============================================== -->
<!-- NEW SECTION: MODERATE REVIEWS -->
<!-- ============================================== -->
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 my-4 border-bottom" id="reviews-section">
    <h1 class="h2">Moderate Reviews</h1>
</div>
<div class="table-responsive">
    <?php if (empty($all_reviews)): ?>
        <p>There are no reviews to moderate.</p>
    <?php else: ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Product</th>
                    <th>Rating</th>
                    <th>Comment</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($all_reviews as $review): ?>
                <tr>
                    <td><?php echo htmlspecialchars($review['user_name']); ?></td>
                    <td><?php echo htmlspecialchars($review['product_name']); ?></td>
                    <td><?php echo str_repeat('★', $review['rating']); ?></td>
                    <td><?php echo htmlspecialchars($review['comment']); ?></td>
                    <td><?php echo date('F j, Y', strtotime($review['created_at'])); ?></td>
                    <td>
                        <form action="dashboard.php#reviews-section" method="POST" onsubmit="return confirm('Are you sure you want to delete this review permanently?');">
                            <input type="hidden" name="action" value="delete_review">
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


<!-- ============================================== -->
<!-- NEW SECTION: TRACK TRANSACTIONS -->
<!-- ============================================== -->
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 my-4 border-bottom" id="transactions-section">
    <h1 class="h2">Track Transactions</h1>
</div>
<div class="table-responsive">
    <?php if (empty($all_transactions)): ?>
        <p>No transactions have been recorded yet.</p>
    <?php else: ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Order ID</th>
                    <th>Client</th>
                    <th>Amount</th>
                    <th>Method</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($all_transactions as $transaction): ?>
                <tr>
                    <td><?php echo $transaction['id']; ?></td>
                    <td><?php echo $transaction['order_id']; ?></td>
                    <td><?php echo htmlspecialchars($transaction['username']); ?></td>
                    <td>$<?php echo number_format($transaction['amount'], 2); ?></td>
                    <td><?php echo htmlspecialchars($transaction['payment_method']); ?></td>
                    <td><span class="badge text-bg-success"><?php echo ucfirst($transaction['status']); ?></span></td>
                    <td><?php echo date('F j, Y, g:i a', strtotime($transaction['created_at'])); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>



<?php require_once 'templates/admin_footer.php'; ?>

<?php require_once 'templates/admin_footer.php'; ?>
<script>
// JavaScript to populate the Edit User modal with data
var editUserModal = document.getElementById('editUserModal');
editUserModal.addEventListener('show.bs.modal', function (event) {
  var button = event.relatedTarget;
  var userId = button.getAttribute('data-bs-id');
  var userName = button.getAttribute('data-bs-username');
  var userEmail = button.getAttribute('data-bs-email');
  var userRole = button.getAttribute('data-bs-role');
  
  var modalTitle = editUserModal.querySelector('.modal-title');
  var userIdInput = editUserModal.querySelector('#edit_user_id');
  var nameInput = editUserModal.querySelector('#edit_username');
  var emailInput = editUserModal.querySelector('#edit_email');
  var roleSelect = editUserModal.querySelector('#edit_role');

  modalTitle.textContent = 'Edit User: ' + userName;
  userIdInput.value = userId;
  nameInput.value = userName;
  emailInput.value = userEmail;
  roleSelect.value = userRole; // This sets the dropdown to the correct role
});
</script>
</body>
</html>
