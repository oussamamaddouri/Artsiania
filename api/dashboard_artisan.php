<?php
// === SECURITY AND INITIALIZATION ===
require_once __DIR__ . '/../core/role_guard.php';
require_role(['artisan']);
require_once __DIR__ . '/../config/db.php';
$db = getDbConnection();
$artisan_id = $_SESSION['user_id'];


// =================================================================
// SECTION 1: HANDLE ALL FORM SUBMISSIONS
// =================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    // --- Handle Add Product ---
    if ($_POST['action'] === 'add_product') {

        $name = trim($_POST['name']);
        $category_id = (int)$_POST['category_id']; // ✅ NEW
        $description = trim($_POST['description']);
        $price = $_POST['price'];
        $stock = (int)$_POST['stock'];
        $image_file = $_FILES['image'];
        $image_path = null;

        // Handle image upload
        if (isset($image_file) && $image_file['error'] === UPLOAD_ERR_OK) {
            $upload_dir = __DIR__ . '/assets/images/';
            $filename = uniqid() . '-' . basename($image_file['name']);

            if (move_uploaded_file($image_file['tmp_name'], $upload_dir . $filename)) {
                $image_path = $filename;
            }
        }

        // ✅ UPDATED SQL (added category_id)
        $stmt = $db->prepare(
            "INSERT INTO products 
             (user_id, name, category_id, description, price, stock, image_path, status)
             VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')"
        );

        if ($stmt->execute([
            $artisan_id,
            $name,
            $category_id,
            $description,
            $price,
            $stock,
            $image_path
        ])) {
            $_SESSION['success_message'] = "Product added successfully!";
        } else {
            $_SESSION['error_message'] = "Failed to add product.";
        }
    }

    // --- Handle Edit Product ---
    if ($_POST['action'] === 'edit_product' && isset($_POST['product_id'])) {
        $product_id = $_POST['product_id'];
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        $price = $_POST['price'];
        $stock = (int)$_POST['stock'];
        $image_file = $_FILES['image'];

        $sql = "UPDATE products SET name = ?, description = ?, price = ?, stock = ?";
        $params = [$name, $description, $price, $stock];

        if (isset($image_file) && $image_file['error'] === UPLOAD_ERR_OK) {
            $upload_dir = __DIR__ . '/assets/images/';
            $filename = uniqid() . '-' . basename($image_file['name']);

            if (move_uploaded_file($image_file['tmp_name'], $upload_dir . $filename)) {
                $sql .= ", image_path = ?";
                $params[] = $filename;
            }
        }

        $sql .= " WHERE id = ? AND user_id = ?";
        $params[] = $product_id;
        $params[] = $artisan_id;

        $stmt = $db->prepare($sql);

        if ($stmt->execute($params)) {
            $_SESSION['success_message'] = 'Product updated successfully.';
        } else {
            $_SESSION['error_message'] = 'Failed to update product.';
        }
    }

    header('Location: dashboard_artisan.php');
    exit();
}



// =================================================================
// SECTION 2: FETCH DATA FOR DISPLAY
// =================================================================
$stmt_sales = $db->prepare("SELECT SUM(oi.price * oi.quantity) AS total_revenue, COUNT(DISTINCT o.id) AS orders_completed FROM order_items oi JOIN products p ON oi.product_id = p.id JOIN orders o ON oi.order_id = o.id WHERE p.user_id = ? AND o.status IN ('paid', 'shipped', 'delivered')");
$stmt_sales->execute([$artisan_id]);
$sales = $stmt_sales->fetch();
$stmt_pending = $db->prepare("SELECT DISTINCT o.id, u.username AS client_name, o.created_at FROM orders o JOIN order_items oi ON o.id = oi.order_id JOIN products p ON oi.product_id = p.id JOIN users u ON o.user_id = u.id WHERE p.user_id = ? AND o.status = 'pending'");
$stmt_pending->execute([$artisan_id]);
$pending_orders = $stmt_pending->fetchAll();
$stmt_products = $db->prepare("SELECT id, name, description, price, stock, status, image_path FROM products WHERE user_id = ? ORDER BY created_at DESC");
$stmt_products->execute([$artisan_id]);
$products = $stmt_products->fetchAll();
$categories = $db->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();



// =================================================================
// SECTION 3: RENDER THE HTML PAGE
// =================================================================
require_once __DIR__ . '/../src/views/templates/header.php';
?>

<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
        <h1 class="h2">Artisan Dashboard</h1>
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addProductModal">+ Add New Product</button>
    </div>
    
    <?php if (isset($_SESSION['success_message'])): ?><div class="alert alert-success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div><?php endif; ?>
    <?php if (isset($_SESSION['error_message'])): ?><div class="alert alert-danger"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div><?php endif; ?>

    <h3 class="h4">Sales Summary</h3>
    <div class="row mb-5"><div class="col-md-6"><div class="card text-center"><div class="card-body"><h5 class="card-title">Total Revenue</h5><p class="card-text fs-4">$<?php echo number_format($sales['total_revenue'] ?? 0, 2); ?></p></div></div></div><div class="col-md-6"><div class="card text-center"><div class="card-body"><h5 class="card-title">Orders Completed</h5><p class="card-text fs-4"><?php echo $sales['orders_completed'] ?? 0; ?></p></div></div></div></div>
    
    <h3 class="h4">New Orders to Prepare</h3>
    <div class="table-responsive mb-5">
        <?php if(empty($pending_orders)): ?><p>You have no new orders to prepare.</p><?php else: ?>
        <table class="table table-striped"><thead><tr><th>Order #</th><th>Client</th><th>Date</th><th>Action</th></tr></thead>
        <tbody>
            <?php foreach ($pending_orders as $order): ?>
            <tr><td><?php echo $order['id']; ?></td><td><?php echo htmlspecialchars($order['client_name']); ?></td><td><?php echo date('F j, Y', strtotime($order['created_at'])); ?></td><td>
                <a href="prepare_delivery.php?order_id=<?php echo $order['id']; ?>" class="btn btn-primary btn-sm">Prepare Delivery</a>
            </td></tr>
            <?php endforeach; ?>
        </tbody></table>
        <?php endif; ?>
    </div>
    
    <h3 class="h4">My Products</h3>
    <div class="table-responsive">
        <table class="table"><thead><tr><th>Image</th><th>Name</th><th>Price</th><th>Stock</th><th>Status</th><th>Actions</th></tr></thead><tbody>
        <?php foreach ($products as $product): ?>
        <tr>
            <td><img src="assets/images/<?php echo htmlspecialchars($product['image_path'] ?? 'default.png'); ?>" width="50" alt=""></td>
            <td><?php echo htmlspecialchars($product['name']); ?></td>
            <td>$<?php echo htmlspecialchars($product['price']); ?></td>
            <td><?php echo htmlspecialchars($product['stock']); ?></td>
            <td>
                <?php $status_class = 'text-bg-warning'; if ($product['status'] === 'approved') $status_class = 'text-bg-success'; if ($product['status'] === 'rejected') $status_class = 'text-bg-danger';?>
                <span class="badge <?php echo $status_class; ?>"><?php echo ucfirst($product['status']); ?></span>
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-primary edit-btn" 
                        data-bs-toggle="modal" 
                        data-bs-target="#editProductModal"
                        data-bs-id="<?php echo $product['id']; ?>"
                        data-bs-name="<?php echo htmlspecialchars($product['name']); ?>"
                        data-bs-desc="<?php echo htmlspecialchars($product['description']); ?>"
                        data-bs-price="<?php echo htmlspecialchars($product['price']); ?>"
                        data-bs-stock="<?php echo htmlspecialchars($product['stock']); ?>"
                        data-bs-image="<?php echo htmlspecialchars($product['image_path']); ?>">
                    Edit
                </button> 
                <form action="delete_product.php" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this product?');">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody></table>
    </div>
</div>

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Add New Product</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <form action="dashboard_artisan.php" method="POST" enctype="multipart/form-data">
          <input type="hidden" name="action" value="add_product">

          <!-- Product Name -->
          <div class="mb-3">
            <label class="form-label">Product Name</label>
            <input type="text" class="form-control" name="name" required>
          </div>

          <!-- Category Dropdown (NEW) -->
          <div class="mb-3">
            <label for="category_id" class="form-label">Category</label>
            <select class="form-select" id="category_id" name="category_id" required>
              <option value="">-- Select a Category --</option>
              <?php foreach ($categories as $category): ?>
                <option value="<?php echo $category['id']; ?>">
                  <?php echo htmlspecialchars($category['name']); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Description -->
          <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea class="form-control" name="description" rows="3"></textarea>
          </div>

          <!-- Price -->
          <div class="mb-3">
            <label class="form-label">Price</label>
            <input type="number" step="0.01" class="form-control" name="price" required>
          </div>

          <!-- Stock -->
          <div class="mb-3">
            <label class="form-label">Stock Quantity</label>
            <input type="number" class="form-control" name="stock" required>
          </div>

          <!-- Image -->
          <div class="mb-3">
            <label class="form-label">Product Image</label>
            <input type="file" class="form-control" name="image" required>
          </div>

          <!-- Modal Footer -->
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Add Product</button>
          </div>

        </form>
      </div>

    </div>
  </div>
</div>


<!-- MODAL FOR EDITING A PRODUCT -->
<div class="modal fade" id="editProductModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editProductModalLabel">Edit Product</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="editProductForm" action="dashboard_artisan.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="edit_product">
            <input type="hidden" name="product_id" id="edit_product_id">
            <div class="mb-3"><label for="edit_name" class="form-label">Product Name</label><input type="text" class="form-control" id="edit_name" name="name" required></div>
            <div class="mb-3"><label for="edit_description" class="form-label">Description</label><textarea class="form-control" id="edit_description" name="description" rows="3"></textarea></div>
            <div class="mb-3"><label for="edit_price" class="form-label">Price</label><input type="number" step="0.01" class="form-control" id="edit_price" name="price" required></div>
            <div class="mb-3"><label for="edit_stock" class="form-label">Stock Quantity</label><input type="number" class="form-control" id="edit_stock" name="stock" required></div>
            <div class="mb-3"><label class="form-label">Current Image</label><div id="edit_current_image_display"></div><label for="edit_image" class="form-label mt-2">Upload New Image (Optional)</label><input type="file" class="form-control" id="edit_image" name="image"><small class="form-text text-muted">Leave empty to keep current image.</small></div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Save Changes</button></div>
        </form>
      </div>
    </div>
  </div>
</div>


<?php require_once __DIR__ . '/../src/views/templates/footer.php'; ?>
<script>
// This script runs when the "Edit" button is clicked to populate the modal
var editProductModal = document.getElementById('editProductModal');
editProductModal.addEventListener('show.bs.modal', function (event) {
  var button = event.relatedTarget;
  var productId = button.getAttribute('data-bs-id');
  var productName = button.getAttribute('data-bs-name');
  var productDesc = button.getAttribute('data-bs-desc');
  var productPrice = button.getAttribute('data-bs-price');
  var productStock = button.getAttribute('data-bs-stock');
  var productImage = button.getAttribute('data-bs-image');
  
  var modalTitle = editProductModal.querySelector('.modal-title');
  var productIdInput = editProductModal.querySelector('#edit_product_id');
  var nameInput = editProductModal.querySelector('#edit_name');
  var descInput = editProductModal.querySelector('#edit_description');
  var priceInput = editProductModal.querySelector('#edit_price');
  var stockInput = editProductModal.querySelector('#edit_stock');
  var imageDisplay = editProductModal.querySelector('#edit_current_image_display');

  modalTitle.textContent = 'Edit Product: ' + productName;
  productIdInput.value = productId;
  nameInput.value = productName;
  descInput.value = productDesc;
  priceInput.value = productPrice;
  stockInput.value = productStock;
  imageDisplay.innerHTML = `<img src="assets/images/${productImage || 'default.png'}" width="100" />`;
});
</script>
</body>
</html>