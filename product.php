<?php
require 'includes/header.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit();
}
$product_id = intval($_GET['id']);

// Fetch Product Details
$query = "SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.id = $product_id";
$result = mysqli_query($conn, $query);
if (mysqli_num_rows($result) == 0) {
    header("Location: index.php");
    exit();
}
$product = mysqli_fetch_assoc($result);

// Check if item is in wishlist
$in_wishlist = false;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $wishlist_check_query = "SELECT id FROM wishlist WHERE user_id = $user_id AND product_id = $product_id";
    $wishlist_check_result = mysqli_query($conn, $wishlist_check_query);
    if (mysqli_num_rows($wishlist_check_result) > 0) {
        $in_wishlist = true;
    }
}

// Fetch Approved Reviews
$reviews_query = "SELECT r.*, u.name as user_name, r.image as review_image FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.product_id = $product_id AND r.is_approved = 1 ORDER BY r.created_at DESC";
$reviews_result = mysqli_query($conn, $reviews_query);
?>

<div class="page-container">
    <div class="product-details-container">
        <div class="product-image-section">
            <img src="images/products/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
        </div>
        <div class="product-info-section">
            <h1 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h1>
            <p class="product-category">Category: <?php echo htmlspecialchars($product['category_name']); ?></p>
            <p class="product-price-large">₹<?php echo number_format($product['price'], 2); ?> <span class="tax-note">(incl. of all taxes)</span></p>
            <p class="product-stock"><?php echo ($product['stock'] > 0) ? 'In Stock' : 'Out of Stock'; ?></p>
            
            <div class="product-actions">
                <form action="cart_handler.php" method="POST">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <button type="submit" name="add_to_cart" class="add-to-cart-btn" <?php echo ($product['stock'] <= 0) ? 'disabled' : ''; ?>>Add to Cart</button>
                </form>
                <?php if (isset($_SESSION['user_id'])): ?>
                <form action="wishlist_handler.php" method="POST">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <input type="hidden" name="redirect_to" value="product.php?id=<?php echo $product_id; ?>">
                    <?php if ($in_wishlist): ?>
                        <button type="submit" name="remove_from_wishlist" class="wishlist-heart-btn active" title="Remove from Wishlist">❤️</button>
                    <?php else: ?>
                        <button type="submit" name="add_to_wishlist" class="wishlist-heart-btn" title="Add to Wishlist">♡</button>
                    <?php endif; ?>
                </form>
                <?php endif; ?>
            </div>

            <div class="product-description">
                <h3>Description</h3>
                <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
            </div>
        </div>
    </div>
    
    <div class="reviews-section">
        <h2>Customer Reviews</h2>
        <div class="existing-reviews">
            <?php if (mysqli_num_rows($reviews_result) > 0): ?>
                <?php while($review = mysqli_fetch_assoc($reviews_result)): ?>
                    <div class="review-card">
                        <p class="review-rating"><?php echo str_repeat('★', $review['rating']) . str_repeat('☆', 5 - $review['rating']); ?></p>
                        <p class="review-author">by <?php echo htmlspecialchars($review['user_name']); ?> on <?php echo date('d M Y', strtotime($review['created_at'])); ?></p>
                        <?php if (!empty($review['image'])): ?>
                            <img src="images/reviews/<?php echo htmlspecialchars($review['image']); ?>" alt="Review image" class="review-image">
                        <?php endif; ?>
                        <p class="review-text"><?php echo nl2br(htmlspecialchars($review['review_text'])); ?></p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No reviews yet for this product.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require 'includes/footer.php'; ?>