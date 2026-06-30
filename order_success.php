<?php
require 'includes/header.php'; 

if (!isset($_SESSION['user_id'])) {
    header('location: login.php');
    exit();
}
if (!isset($_SESSION['last_order_id'])) {
    header('location: my_orders.php');
    exit();
}
$order_id = $_SESSION['last_order_id'];
unset($_SESSION['last_order_id']);
?>
<div class="page-container text-center">
    <h2>Thank You For Your Order!</h2>
    <p>Your order has been placed successfully.</p>
    <p>Your Order ID is: <strong>#<?php echo $order_id; ?></strong></p>
    <a href="index.php" class="continue-shopping-btn">Continue Shopping</a>
</div>
<?php require 'includes/footer.php'; ?>