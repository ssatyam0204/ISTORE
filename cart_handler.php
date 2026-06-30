<?php
session_start();

if (!isset($_POST['product_id'])) {
    header('Location: index.php');
    exit();
}
$product_id = intval($_POST['product_id']);

if (isset($_POST['add_to_cart'])) {
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['redirect_after_login'] = "product.php?id=" . $product_id;
        header('Location: login.php');
        exit();
    }
} else {
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit();
    }
}

require 'includes/db_connect.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_POST['add_to_cart'])) {
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]['quantity'] += $quantity;
    } else {
        $_SESSION['cart'][$product_id] = ['quantity' => $quantity];
    }
}

if (isset($_POST['update_cart'])) {
    $quantity = intval($_POST['quantity']);
    if ($quantity > 0) {
        $_SESSION['cart'][$product_id]['quantity'] = $quantity;
    } else {
        unset($_SESSION['cart'][$product_id]);
    }
}

if (isset($_POST['remove_from_cart'])) {
    unset($_SESSION['cart'][$product_id]);
}

header('Location: cart.php');
exit();
?>