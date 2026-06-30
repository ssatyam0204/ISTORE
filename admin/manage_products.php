<?php
require 'includes/header.php';

$edit_product = null;

// Handle Add Product
if (isset($_POST['add_product'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = floatval($_POST['price']);
    $category_id = intval($_POST['category_id']);
    $stock = intval($_POST['stock']);

    $image_name = "placeholder.png";
    if (isset($_FILES['image']) && !empty($_FILES['image']['name']) && $_FILES['image']['error'] == 0) {
        $image_name = time() . '_' . basename($_FILES['image']['name']);
        $target_dir = "../images/products/";
        $target_file = $target_dir . $image_name;
        move_uploaded_file($_FILES['image']['tmp_name'], $target_file);
    }

    $query = "INSERT INTO products (name, description, price, category_id, stock, image) VALUES ('$name', '$description', '$price', '$category_id', '$stock', '$image_name')";
    mysqli_query($conn, $query);
    header("Location: manage_products.php");
    exit();
}

// Handle Delete Product
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    $query = "DELETE FROM products WHERE id=$id";
    mysqli_query($conn, $query);
    header("Location: manage_products.php");
    exit();
}

// Handle Edit Product (Fetch Data)
if (isset($_GET['edit_id'])) {
    $id = intval($_GET['edit_id']);
    $query = "SELECT * FROM products WHERE id=$id";
    $result = mysqli_query($conn, $query);
    $edit_product = mysqli_fetch_assoc($result);
}

// Handle Update Product
if (isset($_POST['update_product'])) {
    $id = intval($_POST['id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = floatval($_POST['price']);
    $category_id = intval($_POST['category_id']);
    $stock = intval($_POST['stock']);
    $current_image = mysqli_real_escape_string($conn, $_POST['current_image']);
    
    $image_to_update = $current_image;
    if (isset($_FILES['image']) && !empty($_FILES['image']['name']) && $_FILES['image']['error'] == 0) {
        $image_to_update = time() . '_' . basename($_FILES['image']['name']);
        $target_dir = "../images/products/";
        $target_file = $target_dir . $image_to_update;
        move_uploaded_file($_FILES['image']['tmp_name'], $target_file);
    }

    $query = "UPDATE products SET name='$name', description='$description', price='$price', category_id='$category_id', stock='$stock', image='$image_to_update' WHERE id=$id";
    mysqli_query($conn, $query);
    header("Location: manage_products.php");
    exit();
}

$products_result = mysqli_query($conn, "SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON p.category_id = c.id ORDER BY p.name ASC");
$categories_result = mysqli_query($conn, "SELECT * FROM categories ORDER BY name ASC");
?>
<h2>Manage Products</h2>
<h3><?php echo $edit_product ? 'Edit Product' : 'Add New Product'; ?></h3>
<form action="manage_products.php" method="POST" enctype="multipart/form-data">
    <?php if ($edit_product): ?>
        <input type="hidden" name="id" value="<?php echo $edit_product['id']; ?>">
        <input type="hidden" name="current_image" value="<?php echo $edit_product['image']; ?>">
    <?php endif; ?>
    <input type="text" name="name" placeholder="Product Name" required value="<?php echo $edit_product['name'] ?? ''; ?>">
    <textarea name="description" placeholder="Product Description" required><?php echo $edit_product['description'] ?? ''; ?></textarea>
    <input type="number" step="0.01" name="price" placeholder="Price" required value="<?php echo $edit_product['price'] ?? ''; ?>">
    <input type="number" name="stock" placeholder="Stock Quantity" required value="<?php echo $edit_product['stock'] ?? ''; ?>">
    <select name="category_id" required>
        <option value="">Select Category</option>
        <?php mysqli_data_seek($categories_result, 0); while($cat = mysqli_fetch_assoc($categories_result)): ?>
            <option value="<?php echo $cat['id']; ?>" <?php if(isset($edit_product) && $edit_product['category_id'] == $cat['id']) echo 'selected'; ?>>
                <?php echo htmlspecialchars($cat['name']); ?>
            </option>
        <?php endwhile; ?>
    </select>
    <input type="file" name="image">
    <?php if ($edit_product): ?>
        <button type="submit" name="update_product">Update Product</button>
    <?php else: ?>
        <button type="submit" name="add_product">Add Product</button>
    <?php endif; ?>
</form>

<h3>Existing Products</h3>
<table>
    <thead>
        <tr>
            <th>Image</th>
            <th>Name</th>
            <th>Category</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while($row = mysqli_fetch_assoc($products_result)): ?>
        <tr>
            <td><img src="../images/products/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>" width="50"></td>
            <td><?php echo htmlspecialchars($row['name']); ?></td>
            <td><?php echo htmlspecialchars($row['category_name']); ?></td>
            <td>₹<?php echo number_format($row['price'], 2); ?></td>
            <td><?php echo $row['stock']; ?></td>
            <td>
                <a href="manage_products.php?edit_id=<?php echo $row['id']; ?>">Edit</a>
                <a href="manage_products.php?delete_id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php require 'includes/footer.php'; ?>