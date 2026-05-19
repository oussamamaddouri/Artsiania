<?php
// === SECURITY AND INITIALIZATION ===
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../config/db.php';
$db = getDbConnection();

if (!isset($_GET['id'])) { header('Location: products.php'); exit(); }
$product_id = (int)$_GET['id'];


// =================================================================
// SECTION 1: SECURITY HELPER AND FORM HANDLING
// =================================================================
function user_has_purchased($db, $user_id, $product_id) {
    $stmt = $db->prepare("SELECT COUNT(*) FROM order_items oi JOIN orders o ON oi.order_id = o.id WHERE o.user_id = ? AND oi.product_id = ?");
    $stmt->execute([$user_id, $product_id]);
    return $stmt->fetchColumn() > 0;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'submit_review') {
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        if (user_has_purchased($db, $user_id, $product_id)) {
            $stmt = $db->prepare("INSERT INTO reviews (product_id, user_id, rating, comment) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$product_id, $user_id, $_POST['rating'], trim($_POST['comment'])])) { 
                $_SESSION['success_message'] = "Review submitted successfully!";
            } else { $_SESSION['error_message'] = "Failed to submit review."; }
        } else { $_SESSION['error_message'] = "You can only review products you have purchased."; }
    } else { $_SESSION['error_message'] = "You must be logged in to leave a review."; }
    header("Location: product_detail.php?id=$product_id"); exit();
}


// =================================================================
// SECTION 2: FETCH ALL DATA FOR THE PAGE
// =================================================================
$stmt_product = $db->prepare("SELECT p.*, u.username AS artisan_name, u.id as artisan_id, c.name as category_name FROM products p JOIN users u ON p.user_id = u.id LEFT JOIN categories c on p.category_id = c.id WHERE p.id = ? AND p.status = 'approved'");
$stmt_product->execute([$product_id]);
$product = $stmt_product->fetch();
if (!$product) { die("Product not found or not approved."); }

$stmt_reviews = $db->prepare("SELECT r.*, u.username FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.product_id = ? ORDER BY r.created_at DESC");
$stmt_reviews->execute([$product_id]);
$reviews = $stmt_reviews->fetchAll();

$stmt_suggestions = $db->prepare("SELECT id, name, price, image_path FROM products WHERE user_id = ? AND id != ? AND status = 'approved' ORDER BY created_at DESC LIMIT 4");
$stmt_suggestions->execute([$product['artisan_id'], $product_id]);
$suggested_products = $stmt_suggestions->fetchAll();

$can_review = isset($_SESSION['user_id']) && user_has_purchased($db, $_SESSION['user_id'], $product_id);


// =================================================================
// SECTION 3: RENDER THE HTML PAGE
// =================================================================
require_once __DIR__ . '/../src/views/templates/header.php';
?>

<div class="container my-5 product-detail-page">
    <?php if (isset($_SESSION['success_message'])): ?><div class="alert alert-success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div><?php endif; ?>
    <?php if (isset($_SESSION['error_message'])): ?><div class="alert alert-danger"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div><?php endif; ?>

    <!-- Main Product Section -->
    <div class="row">
        <div class="col-lg-7"><img src="assets/images/<?php echo htmlspecialchars($product['image_path']); ?>" class="img-fluid" alt="<?php echo htmlspecialchars($product['name']); ?>"></div>
        <div class="col-lg-5">
            <p class="text-muted mb-2">Shop / <?php echo htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?></p>
            <h1 class="display-4 font-playfair"><?php echo htmlspecialchars($product['name']); ?></h1>
            <p class="text-secondary"><?php echo htmlspecialchars($product['description']); ?></p>
            <hr>
            <p class="display-6 font-playfair mb-4">$<?php echo number_format($product['price'], 2); ?> USD</p>
            <form action="add_to_cart.php" method="POST"><input type="hidden" name="product_id" value="<?php echo $product['id']; ?>"><div class="d-flex"><input type="number" name="quantity" class="form-control" value="1" min="1" style="max-width: 80px; text-align: center;"><button type="submit" class="btn btn-dark btn-lg ms-3 flex-grow-1">ADD TO CART</button></div></form>
        </div>
    </div>
    
    <!-- Detailed Info Section -->
    <div class="row mt-5"><div class="col-12">
        <h3 class="font-playfair border-bottom pb-2">Handcrafted Ceramic Piece</h3>
        <ul class="list-unstyled mt-3" style="font-size: 1.1rem;"><li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i> Handmade & One-of-a-Kind</li><li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i> Durable & High-Quality Materials</li></ul>
        <?php if(!empty($product['dimensions'])): ?><p><strong>Dimensions:</strong> <?php echo htmlspecialchars($product['dimensions']); ?></p><?php endif; ?>
        <?php if(!empty($product['care_instructions'])): ?><p><strong>Care Instructions:</strong> <?php echo htmlspecialchars($product['care_instructions']); ?></p><?php endif; ?>
        <p class="mt-4"><span class="border p-2"><?php echo strtoupper(htmlspecialchars($product['category_name'] ?? 'SETS')); ?></span></p>
    </div></div>
    
    <hr class="my-5">

    <!-- === THIS IS THE COMPLETE, WORKING REVIEWS SECTION === -->
    <div class="row">
        <div class="col-md-7">
            <h3>Reviews</h3>
            <?php if(empty($reviews)): ?>
                <p>No reviews yet for this product.</p>
            <?php else: ?>
                <?php foreach($reviews as $review): ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <strong><?php echo htmlspecialchars($review['username']); ?></strong>
                            <span class="text-muted ms-2"><?php echo str_repeat('★', $review['rating']) . str_repeat('☆', 5 - $review['rating']); ?></span>
                            <p class="card-text mt-2"><?php echo nl2br(htmlspecialchars($review['comment'])); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div class="col-md-5">
            <?php if ($can_review): ?>
            <h3>Leave a Review</h3>
            <form action="product_detail.php?id=<?php echo $product_id; ?>" method="POST">
                <input type="hidden" name="action" value="submit_review">
                <div class="mb-3"><label for="rating" class="form-label">Rating</label><select class="form-select" name="rating" required><option value="5">5 Stars</option><option value="4">4 Stars</option><option value="3">3 Stars</option><option value="2">2 Stars</option><option value="1">1 Star</option></select></div>
                <div class="mb-3"><label for="comment" class="form-label">Comment</label><textarea class="form-control" name="comment" rows="4"></textarea></div>
                <button type="submit" class="btn btn-primary">Submit Review</button>
            </form>
            <?php else: ?>
                <div class="alert alert-info">You must be logged in and have purchased this product to leave a review.</div>
            <?php endif; ?>
        </div>
    </div>
    <!-- === END OF REVIEWS SECTION === -->


    <!-- NEW SUGGESTED PRODUCTS SECTION -->
    <?php if (!empty($suggested_products)): ?>
    <hr class="my-5">
    <div class="row mt-5">
        <div class="col-lg-7"><p class="section-supertitle">(01)</p><h2 class="section-title">Recent Products <em>Made with Love</em></h2></div>
        <div class="col-lg-5"><p class="text-secondary">Browse more curated selections from <?php echo htmlspecialchars($product['artisan_name']); ?>.</p></div>
    </div>
    <div class="row g-5 mt-1">
        <?php foreach ($suggested_products as $suggested_product): ?>
        <div class="col-md-6 col-lg-3">
            <div class="suggested-product-item text-center">
                <a href="product_detail.php?id=<?php echo $suggested_product['id']; ?>">
                    <div class="product-image-wrapper"><img src="assets/images/<?php echo htmlspecialchars($suggested_product['image_path']); ?>" alt="<?php echo htmlspecialchars($suggested_product['name']); ?>" class="img-fluid"></div>
                </a>
                <hr>
                <div class="product-info"><span class="product-name"><?php echo htmlspecialchars($suggested_product['name']); ?></span><span class="product-price">$<?php echo number_format($suggested_product['price'], 2); ?> USD</span></div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../src/views/templates/footer.php'; ?>