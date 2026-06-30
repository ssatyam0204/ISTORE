<?php 
require 'includes/header.php'; 

if (!isset($_SESSION['user_id'])) {
    header('location: login.php');
    exit();
}
if (!isset($_GET['product_id'])) {
    header('location: my_orders.php');
    exit();
}

$product_id = intval($_GET['product_id']);
$user_id = $_SESSION['user_id'];

// Purchase Verification Check
$verify_purchase_query = "SELECT o.id FROM orders o JOIN order_items oi ON o.id = oi.order_id WHERE o.user_id = $user_id AND oi.product_id = $product_id AND o.order_status = 'Delivered'";
$verify_result = mysqli_query($conn, $verify_purchase_query);
if (mysqli_num_rows($verify_result) == 0) {
    // Redirect or show error if user hasn't purchased this item
    echo "You can only review products you have purchased and received.";
    exit();
}

// Handle Review Submission
if (isset($_POST['submit_review'])) {
    $rating = intval($_POST['rating']);
    $review_text = mysqli_real_escape_string($conn, $_POST['review_text']);
    
    $review_image = NULL;
    if (isset($_FILES['review_image']) && !empty($_FILES['review_image']['name']) && $_FILES['review_image']['error'] == 0) {
        $image_name = time() . '_' . basename($_FILES['review_image']['name']);
        $target_dir = "images/reviews/";
        $target_file = $target_dir . $image_name;
        if (move_uploaded_file($_FILES['review_image']['tmp_name'], $target_file)) {
            $review_image = $image_name;
        }
    }

    if ($rating >= 1 && $rating <= 5) {
        $insert_query = "INSERT INTO reviews (product_id, user_id, rating, review_text, image) VALUES ('$product_id', '$user_id', '$rating', '$review_text', '$review_image')";
        mysqli_query($conn, $insert_query);
        header("Location: my_orders.php?review_submitted=true");
        exit();
    }
}

// Fetch the product details to display its name
$product_query = "SELECT * FROM products WHERE id = $product_id";
$product_result = mysqli_query($conn, $product_query);

// This is the fix: Check if the product was found
if (mysqli_num_rows($product_result) > 0) {
    $product = mysqli_fetch_assoc($product_result);
} else {
    echo "Product not found.";
    exit();
}
?>
<div class="page-container">
    <h2>Write a Review for <?php echo htmlspecialchars($product['name']); ?></h2>
    <div class="review-form-container" style="border-bottom:none;">
        <form action="write_review.php?product_id=<?php echo $product_id; ?>" method="POST" enctype="multipart/form-data">
            <label>Rating:</label>
            <div class="star-rating">
                <input type="radio" id="star5" name="rating" value="5" required/><label for="star5" title="5 stars">★</label>
                <input type="radio" id="star4" name="rating" value="4" /><label for="star4" title="4 stars">★</label>
                <input type="radio" id="star3" name="rating" value="3" /><label for="star3" title="3 stars">★</label>
                <input type="radio" id="star2" name="rating" value="2" /><label for="star2" title="2 stars">★</label>
                <input type="radio" id="star1" name="rating" value="1" /><label for="star1" title="1 star">★</label>
            </div>
            <label for="review_text">Review:</label>
            <textarea name="review_text" id="review_text" rows="4" placeholder="Share your thoughts on the product..."></textarea>
            <label for="review_image">Upload a photo (optional):</label>
            <input type="file" name="review_image" id="review_image">
            <button type="submit" name="submit_review">Submit Review</button>
        </form>
    </div>
</div>
<?php require 'includes/footer.php'; ?>