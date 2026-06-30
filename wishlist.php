<?php 
require 'includes/header.php'; 

if (!isset($_SESSION['user_id'])) {
    header('location: login.php');
    exit();
}
$user_id = $_SESSION['user_id'];

$query = "SELECT p.* FROM products p JOIN wishlist w ON p.id = w.product_id WHERE w.user_id = $user_id";
$wishlist_items = mysqli_query($conn, $query);
?>
<div class="page-container">
    <h1>My Wishlist</h1>
    <?php if(mysqli_num_rows($wishlist_items) == 0): ?>
        <p>Your wishlist is empty.</p>
    <?php else: ?>
        <div class="product-grid">
            <?php while($product = mysqli_fetch_assoc($wishlist_items)): ?>
            <div class="product-card">
                <a href="product.php?id=<?php echo $product['id']; ?>">
                    <img src="images/products/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                    <p class="product-price">₹<?php echo number_format($product['price'], 2); ?></p>
                </a>
                <div class="wishlist-actions">
                    <form action="cart_handler.php" method="POST" style="margin-bottom: 0;">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        <button type="submit" name="add_to_cart">Add to Cart</button>
                    </form>
                    <form action="wishlist_handler.php" method="POST" style="margin-bottom: 0;">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        <input type="hidden" name="redirect_to" value="wishlist.php">
                        <button type="submit" name="remove_from_wishlist" class="remove-btn">Remove</button>
                    </form>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>
</div>
<?php require 'includes/footer.php'; ?>