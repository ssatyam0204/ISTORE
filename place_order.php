<?php
session_start();
require 'includes/db_connect.php';
require 'includes/config.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['place_order'])) {
    header('location: login.php');
    exit();
}
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header('location: index.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$address_line = mysqli_real_escape_string($conn, $_POST['address_line']);
$city = mysqli_real_escape_string($conn, $_POST['city']);
$pincode = mysqli_real_escape_string($conn, $_POST['pincode']);
$state = mysqli_real_escape_string($conn, $_POST['state']);
$payment_method = mysqli_real_escape_string($conn, $_POST['payment_method']);
$payment_id = NULL;

if ($payment_method == 'Razorpay') {
    if (!isset($_POST['razorpay_payment_id']) || empty($_POST['razorpay_payment_id'])) {
        header('location: checkout.php?error=payment_failed');
        exit();
    }
    $payment_id = mysqli_real_escape_string($conn, $_POST['razorpay_payment_id']);
} elseif ($payment_method == 'COD') {
    $payment_id = 'COD_' . uniqid();
} else {
    header('location: checkout.php?error=invalid_method');
    exit();
}

mysqli_begin_transaction($conn);

try {
    $addr_query = "INSERT INTO addresses (user_id, address_line, city, pincode, state) VALUES ('$user_id', '$address_line', '$city', '$pincode', '$state')";
    mysqli_query($conn, $addr_query);
    $address_id = mysqli_insert_id($conn);

    $cart_total = 0;
    $product_ids = array_keys($_SESSION['cart']);
    $ids_string = implode(',', $product_ids);
    $query = "SELECT * FROM products WHERE id IN ($ids_string)";
    $result = mysqli_query($conn, $query);
    $products_in_cart = [];
    while($row = mysqli_fetch_assoc($result)) {
        $products_in_cart[$row['id']] = $row;
    }

    foreach ($_SESSION['cart'] as $product_id => $details) {
        $product = $products_in_cart[$product_id];
        $quantity = $details['quantity'];
        $cart_total += $product['price'] * $quantity;
    }

    $discount_amount = 0;
    if (isset($_SESSION['coupon'])) {
        $coupon = $_SESSION['coupon'];
        if ($coupon['discount_type'] == 'percentage') {
            $discount_amount = ($cart_total * $coupon['discount_value']) / 100;
        } else {
            $discount_amount = $coupon['discount_value'];
        }
    }
    
    $subtotal_after_discount = $cart_total - $discount_amount;
    $grand_total = $subtotal_after_discount + DELIVERY_CHARGE;

    $order_query = "INSERT INTO orders (user_id, address_id, total_amount, payment_method, payment_id) VALUES ('$user_id', '$address_id', '$grand_total', '$payment_method', '$payment_id')";
    mysqli_query($conn, $order_query);
    $order_id = mysqli_insert_id($conn);

    foreach ($_SESSION['cart'] as $product_id => $details) {
        $product = $products_in_cart[$product_id];
        $quantity = $details['quantity'];
        $price = $product['price'];
        $order_item_query = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES ('$order_id', '$product_id', '$quantity', '$price')";
        mysqli_query($conn, $order_item_query);

        $new_stock = $product['stock'] - $quantity;
        $update_stock_query = "UPDATE products SET stock=$new_stock WHERE id=$product_id";
        mysqli_query($conn, $update_stock_query);
    }
    
    mysqli_commit($conn);
    unset($_SESSION['cart']);
    unset($_SESSION['coupon']); // Clear the coupon after use

    $_SESSION['last_order_id'] = $order_id;
    header('Location: order_success.php');
    exit();

} catch (mysqli_sql_exception $exception) {
    mysqli_rollback($conn);
    // You can uncomment the line below for debugging to see the actual SQL error
    // die("SQL Error: " . $exception->getMessage());
    header('Location: checkout.php?error=order_failed');
    exit();
}
?>