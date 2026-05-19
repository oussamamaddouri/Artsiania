<?php
require_once __DIR__ . '/../core/role_guard.php';
require_role(['client']);
require_once __DIR__ . '/../config/db.php';
$db = getDbConnection();
$client_id = $_SESSION['user_id'];

if (!isset($_GET['id'])) { header('Location: dashboard_client.php'); exit(); }
$review_id = (int)$_GET['id'];

// --- HANDLE FORM SUBMISSION ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating = $_POST['rating'];
    $comment = trim($_POST['comment']);
    
    // Security check on update: only update if the ID and user_id match
    $stmt = $db->prepare("UPDATE reviews SET rating = ?, comment = ? WHERE id = ? AND user_id = ?");
    if ($stmt->execute([$rating, $comment, $review_id, $client_id])) {
        $_SESSION['success_message'] = "Review updated successfully.";
    } else { $_SESSION['error_message'] = "Failed to update review."; }
    header('Location: dashboard_client.php#reviews-section');
    exit();
}

// --- FETCH DATA FOR THE FORM ---
// Security check on fetch: only fetch the review if it belongs to this user
$stmt_review = $db->prepare("SELECT * FROM reviews WHERE id = ? AND user_id = ?");
$stmt_review->execute([$review_id, $client_id]);
$review = $stmt_review->fetch();

if (!$review) { die("Review not found or you do not have permission to edit it."); }

require_once __DIR__ . '/../src/views/templates/header.php';
?>

<div class="container my-4">
    <h1 class="h2">Edit Your Review</h1>
    <form action="edit_review.php?id=<?php echo $review_id; ?>" method="POST">
        <div class="mb-3">
            <label for="rating" class="form-label">Rating</label>
            <select class="form-select" name="rating" required>
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <option value="<?php echo $i; ?>" <?php if ($i == $review['rating']) echo 'selected'; ?>>
                        <?php echo $i; ?> Stars
                    </option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="comment" class="form-label">Comment</label>
            <textarea class="form-control" name="comment" rows="4"><?php echo htmlspecialchars($review['comment']); ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Save Changes</button>
        <a href="dashboard_client.php#reviews-section" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php require_once __DIR__ . '/../src/views/templates/footer.php'; ?>
