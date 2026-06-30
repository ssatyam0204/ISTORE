<?php 
require 'includes/header.php'; 

if (!isset($_SESSION['user_id'])) {
    header('location: login.php');
    exit();
}
?>
<div class="page-container">
    <div class="order-details-header">
        <h1>My Orders</h1>
        <a href="raise_complaint.php" class="checkout-btn">Raise a Complaint</a>
    </div>
    
    <?php
    $user_id = $_SESSION['user_id'];
    $query = "SELECT * FROM orders WHERE user_id = '$user_id' ORDER BY order_date DESC";
    $orders_result = mysqli_query($conn, $query);
    ?>
    <?php if(mysqli_num_rows($orders_result) == 0): ?>
        <p>You have not placed any orders yet. <a href="index.php">Start shopping</a>!</p>
    <?php else: ?>
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Date</th>
                    <th>Total Amount</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while($order = mysqli_fetch_assoc($orders_result)): ?>
                <tr>
                    <td>#<?php echo $order['id']; ?></td>
                    <td><?php echo date('d M Y', strtotime($order['order_date'])); ?></td>
                    <td>₹<?php echo number_format($order['total_amount'], 2); ?></td>
                    <td><?php echo htmlspecialchars($order['order_status']); ?></td>
                    <td><a href="order_details.php?id=<?php echo $order['id']; ?>" class="view-details-btn">View Details</a></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
<?php require 'includes/footer.php'; ?>