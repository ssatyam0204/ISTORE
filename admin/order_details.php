<?php
session_start();
require '../includes/db_connect.php';

if (!isset($_SESSION['admin_id'])) {
    header('location: index.php');
    exit();
}
if (!isset($_GET['id'])) {
    header('location: manage_orders.php');
    exit();
}

$admin_username = $_SESSION['admin_username'];
$order_id = intval($_GET['id']);

if (isset($_POST['update_status'])) {
    $new_status = mysqli_real_escape_string($conn, $_POST['status']);
    $update_query = "UPDATE orders SET order_status='$new_status' WHERE id=$order_id";
    mysqli_query($conn, $update_query);
    header("Location: order_details.php?id=$order_id");
    exit();
}

$order_query = "SELECT o.*, u.name as customer_name, u.email as customer_email, a.address_line, a.city, a.pincode, a.state 
                FROM orders o 
                JOIN users u ON o.user_id = u.id 
                JOIN addresses a ON o.address_id = a.id 
                WHERE o.id = $order_id";
$order_result = mysqli_query($conn, $order_query);
$order = mysqli_fetch_assoc($order_result);

$items_query = "SELECT oi.quantity, oi.price, p.name as product_name, p.image as product_image 
                FROM order_items oi 
                JOIN products p ON oi.product_id = p.id 
                WHERE oi.order_id = $order_id";
$items_result = mysqli_query($conn, $items_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details - iStore Admin</title>
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>
    <div class="header">
        <h1>iStore Admin Panel</h1>
        <p>Welcome, <?php echo htmlspecialchars($admin_username); ?>! <a href="logout.php">Logout</a></p>
    </div>
    <div class="container">
        <div class="sidebar">
            <h2>Menu</h2>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="manage_products.php">Manage Products</a></li>
                <li><a href="manage_categories.php">Manage Categories</a></li>
                <li><a href="manage_orders.php">Manage Orders</a></li>
                <li><a href="manage_users.php">Manage Users</a></li>
            </ul>
        </div>
        <div class="main-content">
            <div class="details-header">
                <h2>Order Details: #<?php echo $order['id']; ?></h2>
                <form action="order_details.php?id=<?php echo $order_id; ?>" method="POST" class="status-form">
                    <select name="status">
                        <option value="Pending" <?php if($order['order_status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                        <option value="Shipped" <?php if($order['order_status'] == 'Shipped') echo 'selected'; ?>>Shipped</option>
                        <option value="Delivered" <?php if($order['order_status'] == 'Delivered') echo 'selected'; ?>>Delivered</option>
                        <option value="Cancelled" <?php if($order['order_status'] == 'Cancelled') echo 'selected'; ?>>Cancelled</option>
                    </select>
                    <button type="submit" name="update_status">Update Status</button>
                </form>
            </div>

            <div class="details-grid">
                <div class="details-card">
                    <h3>Customer Information</h3>
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($order['customer_email']); ?></p>
                </div>
                <div class="details-card">
                    <h3>Shipping Address</h3>
                    <p><?php echo htmlspecialchars($order['address_line']); ?></p>
                    <p><?php echo htmlspecialchars($order['city']); ?>, <?php echo htmlspecialchars($order['state']); ?> - <?php echo htmlspecialchars($order['pincode']); ?></p>
                </div>
                <div class="details-card">
                    <h3>Order Summary</h3>
                    <p><strong>Total Amount:</strong> ₹<?php echo number_format($order['total_amount'], 2); ?></p>
                    <p><strong>Status:</strong> <?php echo htmlspecialchars($order['order_status']); ?></p>
                    <p><strong>Date:</strong> <?php echo date('d M Y, h:i A', strtotime($order['order_date'])); ?></p>
                </div>
            </div>

            <h3>Items in this Order</h3>
            <table>
                <thead>
                    <tr>
                        <th colspan="2">Product</th>
                        <th>Quantity</th>
                        <th>Price (at time of order)</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($item = mysqli_fetch_assoc($items_result)): ?>
                    <tr>
                        <td><img src="../images/products/<?php echo htmlspecialchars($item['product_image']); ?>" width="50" alt=""></td>
                        <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td>₹<?php echo number_format($item['price'], 2); ?></td>
                        <td>₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>