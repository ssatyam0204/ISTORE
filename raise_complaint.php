<?php
require 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header('location: login.php');
    exit();
}
$user_id = $_SESSION['user_id'];
$message = '';

if (isset($_POST['submit_complaint'])) {
    $order_id = intval($_POST['order_id']);
    $subject = mysqli_real_escape_string($conn, $_POST['subject']);
    $complaint_message = mysqli_real_escape_string($conn, $_POST['message']);

    $image_name = NULL;
    if (isset($_FILES['complaint_image']) && !empty($_FILES['complaint_image']['name']) && $_FILES['complaint_image']['error'] == 0) {
        $image_name = time() . '_' . basename($_FILES['complaint_image']['name']);
        $target_dir = "images/reviews/"; // Can reuse the reviews folder
        $target_file = $target_dir . $image_name;
        move_uploaded_file($_FILES['complaint_image']['tmp_name'], $target_file);
    }

    $query = "INSERT INTO complaints (user_id, order_id, subject, message, image) VALUES ('$user_id', '$order_id', '$subject', '$complaint_message', '$image_name')";
    if (mysqli_query($conn, $query)) {
        $message = "Your complaint has been submitted successfully! Ticket ID: #" . mysqli_insert_id($conn);
    } else {
        $message = "There was an error submitting your complaint.";
    }
}

$orders_query = "SELECT id FROM orders WHERE user_id = $user_id AND order_status = 'Delivered'";
$orders_result = mysqli_query($conn, $orders_query);
?>
<div class="page-container">
    <h1>Raise a Complaint</h1>
    <p>Have an issue with a delivered order? Let us know.</p>
    <div class="form-container" style="margin: 20px 0;">
        <?php if ($message): ?>
            <p class="form-message"><?php echo $message; ?></p>
        <?php endif; ?>

        <?php if (mysqli_num_rows($orders_result) > 0): ?>
        <form action="raise_complaint.php" method="POST" class="profile-form" enctype="multipart/form-data">
            <label for="order_id">Select Order:</label>
            <select name="order_id" id="order_id" required>
                <option value="">-- Choose a Delivered Order --</option>
                <?php while($order = mysqli_fetch_assoc($orders_result)): ?>
                    <option value="<?php echo $order['id']; ?>">Order #<?php echo $order['id']; ?></option>
                <?php endwhile; ?>
            </select>
            <label for="subject">Reason for Complaint:</label>
            <select name="subject" id="subject" required>
                <option value="">-- Select a Reason --</option>
                <option value="Damaged Product">Damaged Product</option>
                <option value="Missing Item(s)">Missing Item(s)</option>
                <option value="Wrong Item Received">Wrong Item Received</option>
                <option value="Other">Other</option>
            </select>
            <label for="message">Message:</label>
            <textarea name="message" id="message" rows="6" required></textarea>
            <label for="complaint_image">Upload Photo (optional):</label>
            <input type="file" name="complaint_image" id="complaint_image">
            <button type="submit" name="submit_complaint">Submit Complaint</button>
        </form>
        <?php else: ?>
            <p>You have no delivered orders eligible for a complaint.</p>
        <?php endif; ?>
    </div>
</div>
<?php require 'includes/footer.php'; ?>