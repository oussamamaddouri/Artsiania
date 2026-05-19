<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../config/db.php';
$db = getDbConnection();

// --- Fetching Categories for the filter sidebar ---
$categories = $db->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();

// --- Backend Logic: Fetching Products with Filters ---
$search_term = $_GET['search'] ?? '';
$category_filter = $_GET['category'] ?? null;

$sql = "SELECT p.id, p.name, p.price, p.image_path FROM products p WHERE p.status = 'approved'";
$params = [];

if (!empty($search_term)) {
    $sql .= " AND p.name LIKE ?";
    $params[] = '%' . $search_term . '%';
}
if (!empty($category_filter) && ctype_digit($category_filter)) {
    $sql .= " AND p.category_id = ?";
    $params[] = $category_filter;
}

$stmt = $db->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

require_once __DIR__ . '/../src/views/templates/header.php';
?>

<div class="container my-4">
    <div class="row">
        <div class="col-lg-3">
            <!-- Filter Sidebar -->
            <h4 class="mb-3">Categories</h4>
            <div class="list-group">
                <a href="products.php" class="list-group-item list-group-item-action <?php if(empty($category_filter)) echo 'active'; ?>">
                    All Products
                </a>
                <?php foreach ($categories as $category): ?>
                    <a href="products.php?category=<?php echo $category['id']; ?>" class="list-group-item list-group-item-action <?php if(isset($category_filter) && $category_filter == $category['id']) echo 'active'; ?>">
                        <?php echo htmlspecialchars($category['name']); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="col-lg-9">
            <!-- Main Content Area -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3">Our Products</h1>
                <form action="products.php" method="GET" class="d-flex" style="width: 250px;">
                    <input type="search" name="search" class="form-control me-2" placeholder="Search..." value="<?php echo htmlspecialchars($search_term); ?>">
                    <?php if (isset($category_filter)): ?><input type="hidden" name="category" value="<?php echo htmlspecialchars($category_filter); ?>"><?php endif; ?>
                    <button type="submit" class="btn btn-outline-secondary">Search</button>
                </form>
            </div>
            
            <!-- Products Grid -->
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                <?php if (empty($products)): ?>
                    <div class="col-12"><p class="text-center mt-5">No products found matching your criteria.</p></div>
                <?php else: ?>
                    <?php foreach ($products as $product): ?>
                        <div class="col">
                           <!-- === THIS IS THE MISSING PRODUCT CARD HTML === -->
                           <div class="card h-100">
                               <a href="product_detail.php?id=<?php echo $product['id']; ?>">
                                   <img src="assets/images/<?php echo htmlspecialchars($product['image_path'] ?? 'default.png'); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>" style="height: 250px; object-fit: contain; padding: 10px;">
                               </a>
                               <div class="card-body d-flex flex-column">
                                   <a href="product_detail.php?id=<?php echo $product['id']; ?>" class="text-decoration-none text-dark">
                                       <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                   </a>
                                   <p class="card-text fw-bold">$<?php echo htmlspecialchars(number_format($product['price'], 2)); ?></p>
                                   
                                   <form action="add_to_cart.php" method="POST" class="mt-auto">
                                       <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                       <div class="input-group">
                                           <input type="number" name="quantity" class="form-control" value="1" min="1" aria-label="Quantity">
                                           <button type="submit" class="btn btn-primary">Add to Cart</button>
                                       </div>
                                   </form>
                               </div>
                           </div>
                           <!-- === END OF PRODUCT CARD HTML === -->
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../src/views/templates/footer.php'; ?>