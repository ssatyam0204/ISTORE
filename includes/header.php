<?php
session_start();
require 'db_connect.php';

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
    <title>iStore</title>
</head>
<body>
    <div class="navbar">
        <a href="index.php" class="logo">
            <img src="images/logo.svg" alt="iStore Logo">
        </a>
        
        <form action="search.php" method="GET" class="search-form">
            <input type="text" name="query" placeholder="Search for products..." required>
            <button type="submit">Search</button>
        </form>
        
        <div class="nav-controls">
            <div class="theme-switcher">
                <label class="switch">
                    <input type="checkbox" id="theme-toggle">
                    <span class="slider round"></span>
                </label>
            </div>
            <div class="nav-links">
                <a href="index.php" class="<?php if($current_page == 'index.php') echo 'active'; ?>">Store</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="profile.php" class="<?php if($current_page == 'profile.php') echo 'active'; ?>">Profile</a>
                    <a href="my_orders.php" class="<?php if($current_page == 'my_orders.php') echo 'active'; ?>">My Orders</a>
                    <a href="wishlist.php" class="<?php if($current_page == 'wishlist.php') echo 'active'; ?>">Wishlist</a>
                    <a href="cart.php" class="<?php if($current_page == 'cart.php') echo 'active'; ?>">Cart</a>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="cart.php" class="<?php if($current_page == 'cart.php') echo 'active'; ?>">Cart</a>
                    <a href="login.php" class="<?php if($current_page == 'login.php') echo 'active'; ?>">Login</a>
                    <a href="register.php" class="<?php if($current_page == 'register.php') echo 'active'; ?>">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </div>