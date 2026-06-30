<?php
require 'includes/header.php';

$edit_category = null;

if (isset($_POST['add_category'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    if (!empty($name)) {
        $check_query = "SELECT * FROM categories WHERE name='$name'";
        $check_result = mysqli_query($conn, $check_query);
        if (mysqli_num_rows($check_result) == 0) {
            $query = "INSERT INTO categories (name) VALUES ('$name')";
            mysqli_query($conn, $query);
        }
    }
    header("Location: manage_categories.php");
    exit();
}

if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    $query = "DELETE FROM categories WHERE id=$id";
    mysqli_query($conn, $query);
    header("Location: manage_categories.php");
    exit();
}

if (isset($_GET['edit_id'])) {
    $id = intval($_GET['edit_id']);
    $query = "SELECT * FROM categories WHERE id=$id";
    $result = mysqli_query($conn, $query);
    $edit_category = mysqli_fetch_assoc($result);
}

if (isset($_POST['update_category'])) {
    $id = intval($_POST['id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    if (!empty($name)) {
        $query = "UPDATE categories SET name='$name' WHERE id=$id";
        mysqli_query($conn, $query);
        header("Location: manage_categories.php");
        exit();
    }
}

$categories_result = mysqli_query($conn, "SELECT * FROM categories ORDER BY name ASC");
?>

<h2>Manage Categories</h2>
<?php if ($edit_category): ?>
<h3>Edit Category</h3>
<form action="manage_categories.php" method="POST">
    <input type="hidden" name="id" value="<?php echo $edit_category['id']; ?>">
    <input type="text" name="name" value="<?php echo htmlspecialchars($edit_category['name']); ?>" required>
    <button type="submit" name="update_category">Update Category</button>
</form>
<?php else: ?>
<h3>Add New Category</h3>
<form action="manage_categories.php" method="POST">
    <input type="text" name="name" placeholder="Enter new category name" required>
    <button type="submit" name="add_category">Add Category</button>
</form>
<?php endif; ?>
<h3>Existing Categories</h3>
<table>
    <thead>
        <tr>
            <th>Category Name</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while($row = mysqli_fetch_assoc($categories_result)): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['name']); ?></td>
            <td>
                <a href="manage_categories.php?edit_id=<?php echo $row['id']; ?>">Edit</a>
                <a href="manage_categories.php?delete_id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this category?');">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php require 'includes/footer.php'; ?>