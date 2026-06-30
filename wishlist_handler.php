<?php
session_start();
require 'includes/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header('location: login.php');
    exit();
}
if (!isset($_POST['product_id'])) {
    header('location: index.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$product_id = intval($_POST['product_id']);

if (isset($_POST['add_to_wishlist'])) {
    $check_query = "SELECT * FROM wishlist WHERE user_id = $user_id AND product_id = $product_id";
    $check_result = mysqli_query($conn, $check_query);
    if (mysqli_num_rows($check_result) == 0) {
        $query = "INSERT INTO wishlist (user_id, product_id) VALUES ($user_id, $product_id)";
        mysqli_query($conn, $query);
    }
}

if (isset($_POST['remove_from_wishlist'])) {
    $query = "DELETE FROM wishlist WHERE user_id = $user_id AND product_id = $product_id";
    mysqli_query($conn, $query);
}

if (isset($_POST['redirect_to'])) {
    header('Location: ' . $_POST['redirect_to']);
} else {
    header('Location: index.php');
}
exit();
?>