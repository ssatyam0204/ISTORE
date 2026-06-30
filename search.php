<?php 
require 'includes/header.php'; 
?>

<div class="page-container">
    <?php
    if (!isset($_GET['query']) || empty($_GET['query'])) {
        echo "<h1>Please enter a search term.</h1>";
    } else {
        $search_query = mysqli_real_escape_string($conn, $_GET['query']);
        echo "<h1>Search Results for: '" . htmlspecialchars($search_query) . "'</h1>";

        $sql = "SELECT * FROM products WHERE name LIKE '%$search_query%' OR description LIKE '%$search_query%'";
        
        $products_result = mysqli_query($conn, $sql);
        // This block will tell us the real database error
        if (!$products_result) {
            die("Database query for search failed: " . mysqli_error($conn));
        }

        $num_rows = mysqli_num_rows($products_result);
    ?>
    <p><?php echo $num_rows; ?> products found.</p>
    <div class="product-grid">
        <?php if ($num_rows > 0): ?>
            <?php while($product = mysqli_fetch_assoc($products_result)): ?>
            <div class="product-card">
                <a href="product.php?id=<?php echo $product['id']; ?>">
                    <img src="images/products/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                    <p class="product-price">₹<?php echo number_format($product['price'], 2); ?></p>
                </a>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No products found matching your criteria.</p>
        <?php endif; ?>
    </div>
    <?php } ?>
</div>

<?php require 'includes/footer.php'; ?>