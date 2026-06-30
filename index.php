<?php 
require 'includes/header.php'; 

$categories_result = mysqli_query($conn, "SELECT * FROM categories ORDER BY name ASC");
if (!$categories_result) {
    die("Database query for categories failed: " . mysqli_error($conn));
}

$sql = "SELECT * FROM products";
$where_clauses = [];
$order_by = " ORDER BY created_at DESC";

$selected_category = '';
if (isset($_GET['category']) && !empty($_GET['category'])) {
    $selected_category = intval($_GET['category']);
    $where_clauses[] = "category_id = $selected_category";
}

$selected_sort = 'latest';
if (isset($_GET['sort'])) {
    $selected_sort = $_GET['sort'];
    switch ($selected_sort) {
        case 'price_asc':
            $order_by = " ORDER BY price ASC";
            break;
        case 'price_desc':
            $order_by = " ORDER BY price DESC";
            break;
        default:
            $order_by = " ORDER BY created_at DESC";
            break;
    }
}

if (count($where_clauses) > 0) {
    $sql .= " WHERE " . implode(' AND ', $where_clauses);
}
$sql .= $order_by;

$products_result = mysqli_query($conn, $sql);
if (!$products_result) {
    die("Database query for products failed: " . mysqli_error($conn));
}
?>

<div class="page-container">

    <!-- Heading + Filter Section moved just below the logo -->
    <div class="products-header">
        <h1>All Products</h1>
        <div class="filter-sort-controls">
            <form action="index.php" method="GET">
                <select name="category" onchange="this.form.submit()">
                    <option value="">All Categories</option>
                    <?php while($cat = mysqli_fetch_assoc($categories_result)): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php if($selected_category == $cat['id']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                
                <select name="sort" onchange="this.form.submit()">
                    <option value="latest" <?php if($selected_sort == 'latest') echo 'selected'; ?>>Sort by Latest</option>
                    <option value="price_asc" <?php if($selected_sort == 'price_asc') echo 'selected'; ?>>Sort by Price: Low to High</option>
                    <option value="price_desc" <?php if($selected_sort == 'price_desc') echo 'selected'; ?>>Sort by Price: High to Low</option>
                </select>
            </form>
        </div>
    </div>

    <!-- Product Grid -->
    <div class="product-grid">
        <?php if(mysqli_num_rows($products_result) > 0): ?>
            <?php while($product = mysqli_fetch_assoc($products_result)): ?>
            <div class="product-card">
                <a href="product.php?id=<?php echo $product['id']; ?>">
                    <img src="images/products/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                    <p class="product-price">₹<?php echo number_format($product['price'], 2); ?> <span class="tax-note">(incl. of all taxes)</span></p>
                </a>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No products found matching your criteria.</p>
        <?php endif; ?>
    </div>
</div>

<?php require 'includes/footer.php'; ?>
