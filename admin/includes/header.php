<?php
session_start();
require '../includes/db_connect.php';

if (!isset($_SESSION['admin_id'])) {
    header('location: index.php');
    exit();
}
$admin_username = $_SESSION['admin_username'];
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>iStore Admin Panel</title>
    <link rel="stylesheet" href="../css/admin_style.css?v=<?php echo time(); ?>">
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
                <li><a href="dashboard.php" class="<?php if($current_page == 'dashboard.php') echo 'active'; ?>">Dashboard</a></li>
                <li><a href="manage_products.php" class="<?php if($current_page == 'manage_products.php') echo 'active'; ?>">Manage Products</a></li>
                <li><a href="manage_categories.php" class="<?php if($current_page == 'manage_categories.php') echo 'active'; ?>">Manage Categories</a></li>
                <li><a href="manage_orders.php" class="<?php if($current_page == 'manage_orders.php' || $current_page == 'order_details.php') echo 'active'; ?>">Manage Orders</a></li>
                <li><a href="manage_reviews.php" class="<?php if($current_page == 'manage_reviews.php') echo 'active'; ?>">Manage Reviews</a></li>
                <li><a href="manage_users.php" class="<?php if($current_page == 'manage_users.php') echo 'active'; ?>">Manage Users</a></li>
                <li><a href="manage_coupons.php" class="<?php if($current_page == 'manage_coupons.php') echo 'active'; ?>">Manage Coupons</a></li>
                <li><a href="manage_complaints.php" class="<?php if($current_page == 'manage_complaints.php') echo 'active'; ?>">Manage Complaints</a></li>
            </ul>
        </div>
        <div class="main-content">