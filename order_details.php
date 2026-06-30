<?php 
require 'includes/header.php'; 

if (!isset($_SESSION['user_id'])) {
    header('location: login.php');
    exit();
}
if (!isset($_GET['id'])) {
    header('location: my_orders.php');
    exit();
}

$order_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

$order_query = "SELECT o.*, a.address_line, a.city, a.pincode, a.state 
                FROM orders o
                JOIN addresses a ON o.address_id = a.id 
                WHERE o.id = $order_id AND o.user_id = $user_id";
$order_result = mysqli_query($conn, $order_query);

if(mysqli_num_rows($order_result) == 0) {
    header('location: my_orders.php');
    exit();
}
$order = mysqli_fetch_assoc($order_result);

$items_query = "SELECT oi.quantity, oi.price, p.id as product_id, p.name as product_name, p.image as product_image 
                FROM order_items oi 
                JOIN products p ON oi.product_id = p.id 
                WHERE oi.order_id = $order_id";
$items_result = mysqli_query($conn, $items_query);
?>
<div class="page-container">
    <div class="order-details-header">
        <h1>Order Details #<?php echo $order['id']; ?></h1>
        <?php if (trim(strtolower($order['order_status'])) == 'delivered'): ?>
            <a href="generate_invoice.php?id=<?php echo $order['id']; ?>" class="checkout-btn">Download Invoice</a>
        <?php endif; ?>
    </div>

    <div class="order-details-grid">
        <div class="details-card">
            <h3>Order Information</h3>
            <p><strong>Status:</strong> <?php echo htmlspecialchars($order['order_status']); ?></p>
            <p><strong>Date:</strong> <?php echo date('d M Y', strtotime($order['order_date'])); ?></p>
            <p><strong>Total:</strong> ₹<?php echo number_format($order['total_amount'], 2); ?> </p>
        </div>
        <div class="details-card">
            <h3>Shipping Address</h3>
            <p><?php echo htmlspecialchars($order['address_line']); ?></p>
            <p><?php echo htmlspecialchars($order['city']); ?>, <?php echo htmlspecialchars($order['state']); ?> - <?php echo htmlspecialchars($order['pincode']); ?></p>
        </div>
    </div>

    <h3>Items in this Order</h3>
    <table class="cart-table">
        <thead>
            <tr>
                <th colspan="2">Product</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while($item = mysqli_fetch_assoc($items_result)): ?>
            <tr>
                <td><img src="images/products/<?php echo htmlspecialchars($item['product_image']); ?>" width="50" alt=""></td>
                <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                <td>₹<?php echo number_format($item['price'], 2); ?></td>
                <td><?php echo $item['quantity']; ?></td>
                <td>
                    <?php if (trim(strtolower($order['order_status'])) == 'delivered'): ?>
                        <a href="write_review.php?product_id=<?php echo $item['product_id']; ?>" class="view-details-btn">Write a Review</a>
                    <?php else: ?>
                        <span>-</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
<?php require 'includes/footer.php'; ?>