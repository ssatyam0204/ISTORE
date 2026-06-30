<?php
require 'includes/header.php';

if (isset($_GET['approve_id'])) {
    $id = intval($_GET['approve_id']);
    $query = "UPDATE reviews SET is_approved = 1 WHERE id=$id";
    mysqli_query($conn, $query);
    header("Location: manage_reviews.php");
    exit();
}

if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    $query = "DELETE FROM reviews WHERE id=$id";
    mysqli_query($conn, $query);
    header("Location: manage_reviews.php");
    exit();
}

$reviews_query = "SELECT r.*, u.name as user_name, p.name as product_name 
                  FROM reviews r 
                  JOIN users u ON r.user_id = u.id 
                  JOIN products p ON r.product_id = p.id 
                  WHERE r.is_approved = 0 
                  ORDER BY r.created_at DESC";
$reviews_result = mysqli_query($conn, $reviews_query);
?>

<h2>Manage Pending Reviews</h2>
<?php if(mysqli_num_rows($reviews_result) == 0): ?>
    <p>No pending reviews.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>User</th>
                <th>Rating</th>
                <th>Review</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while($review = mysqli_fetch_assoc($reviews_result)): ?>
            <tr>
                <td><?php echo htmlspecialchars($review['product_name']); ?></td>
                <td><?php echo htmlspecialchars($review['user_name']); ?></td>
                <td><?php echo str_repeat('★', $review['rating']); ?></td>
                <td><?php echo htmlspecialchars($review['review_text']); ?></td>
                <td>
                    <?php if (!empty($review['image'])): ?>
                        <img src="../images/reviews/<?php echo htmlspecialchars($review['image']); ?>" width="100" alt="Review image">
                    <?php else: ?>
                        <span>No Image</span>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="manage_reviews.php?approve_id=<?php echo $review['id']; ?>">Approve</a>
                    <a href="manage_reviews.php?delete_id=<?php echo $review['id']; ?>" onclick="return confirm('Are you sure?');">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php require 'includes/footer.php'; ?>