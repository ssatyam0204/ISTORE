<?php
require 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header('location: login.php');
    exit();
}

$cart_items = [];
$subtotal = 0;
$coupon_message = '';
$discount_amount = 0;

if (isset($_POST['apply_coupon'])) {
    $coupon_code = mysqli_real_escape_string($conn, $_POST['coupon_code']);
    $query = "SELECT * FROM coupons WHERE coupon_code = '$coupon_code' AND is_active = 1 AND (expiry_date >= CURDATE() OR expiry_date IS NULL)";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) > 0) {
        $_SESSION['coupon'] = mysqli_fetch_assoc($result);
        $coupon_message = "Coupon applied successfully!";
    } else {
        unset($_SESSION['coupon']);
        $coupon_message = "Invalid or expired coupon code.";
    }
}

if (isset($_GET['remove_coupon'])) {
    unset($_SESSION['coupon']);
    header('Location: cart.php');
    exit();
}

if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $product_ids = array_keys($_SESSION['cart']);
    if (!empty($product_ids)) {
        $ids_string = implode(',', $product_ids);
        $query = "SELECT * FROM products WHERE id IN ($ids_string)";
        $result = mysqli_query($conn, $query);

        while ($row = mysqli_fetch_assoc($result)) {
            $product_id = $row['id'];
            $quantity = $_SESSION['cart'][$product_id]['quantity'];
            $row['quantity'] = $quantity;
            $row['total_price'] = $row['price'] * $quantity;
            $cart_items[] = $row;
            $subtotal += $row['total_price'];
        }
    }
}

if (isset($_SESSION['coupon'])) {
    $coupon = $_SESSION['coupon'];
    if ($coupon['discount_type'] == 'percentage') {
        $discount_amount = ($subtotal * $coupon['discount_value']) / 100;
    } else {
        $discount_amount = $coupon['discount_value'];
    }
}
$grand_total = $subtotal - $discount_amount;
?>
<div class="page-container">
    <h1>Shopping Cart</h1>
    <?php if (empty($cart_items)): ?>
        <p>Your cart is empty. <a href="index.php">Continue shopping</a>.</p>
    <?php else: ?>
        <table class="cart-table">
            <thead>
                <tr>
                    <th colspan="2">Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cart_items as $item): ?>
                <tr>
                    <td><img src="images/products/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" width="80"></td>
                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                    <td>₹<?php echo number_format($item['price'], 2); ?></td>
                    <td>
                        <form action="cart_handler.php" method="POST" class="quantity-form">
                            <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                            <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="0" class="quantity-input">
                            <button type="submit" name="update_cart" class="update-btn">Update</button>
                        </form>
                    </td>
                    <td>₹<?php echo number_format($item['total_price'], 2); ?></td>
                    <td>
                        <form action="cart_handler.php" method="POST">
                            <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                            <button type="submit" name="remove_from_cart" class="remove-btn">Remove</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="cart-totals-container">
            <div class="coupon-form-container">
                <form action="cart.php" method="POST">
                    <input type="text" name="coupon_code" placeholder="Enter Coupon Code">
                    <button type="submit" name="apply_coupon">Apply Coupon</button>
                </form>
                <?php if ($coupon_message): ?><p class="form-message"><?php echo $coupon_message; ?></p><?php endif; ?>
            </div>

            <div class="cart-summary">
                <div class="summary-line"><span>Subtotal:</span> <span>₹<?php echo number_format($subtotal, 2); ?></span></div>
                <?php if ($discount_amount > 0): ?>
                <div class="summary-line discount"><span>Discount (<?php echo htmlspecialchars($_SESSION['coupon']['coupon_code']); ?>):</span> <span>- ₹<?php echo number_format($discount_amount, 2); ?></span></div>
                <div class="summary-line"><a href="cart.php?remove_coupon=1">Remove Coupon</a></div>
                <?php endif; ?>
                <hr>
                <div class="summary-line total"><h3>Grand Total:</h3> <h3>₹<?php echo number_format($grand_total, 2); ?></h3></div>
                <a href="checkout.php" class="checkout-btn">Proceed to Checkout</a>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php require 'includes/footer.php'; ?>