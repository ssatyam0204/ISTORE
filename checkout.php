<?php
require 'includes/header.php';
require 'includes/config.php';

if (!isset($_SESSION['user_id'])) {
    header('location: login.php');
    exit();
}
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header('location: index.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$user_result = mysqli_query($conn, "SELECT * FROM users WHERE id=$user_id");
$user_data = mysqli_fetch_assoc($user_result);

$cart_total = 0;
$product_ids = array_keys($_SESSION['cart']);
if (!empty($product_ids)) {
    $ids_string = implode(',', $product_ids);
    $query = "SELECT * FROM products WHERE id IN ($ids_string)";
    $result = mysqli_query($conn, $query);
    while ($row = mysqli_fetch_assoc($result)) {
        $product_id = $row['id'];
        $quantity = $_SESSION['cart'][$product_id]['quantity'];
        $cart_total += $row['price'] * $quantity;
    }
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
$grand_total_in_paise = $grand_total * 100;
?>

<div class="page-container">
    <h1>Checkout</h1>
    <form id="checkout-form" action="place_order.php" method="POST">
        <div class="checkout-container">
            <div class="shipping-address">
                <h3>Shipping Address</h3>
                <input type="text" name="address_line" placeholder="Address Line" required>
                <input type="text" name="city" placeholder="City" required>
                <input type="text" name="pincode" placeholder="Pincode" required>
                <input type="text" name="state" placeholder="State" required>
                
                <h3>Payment Method</h3>
                <div class="payment-options">
                    <label>
                        <input type="radio" name="payment_method" value="Razorpay" checked> Online Payment (Razorpay)
                    </label>
                    <label>
                        <input type="radio" name="payment_method" value="COD"> Cash on Delivery (COD)
                    </label>
                </div>
            </div>

            <div class="order-summary">
                <h3>Order Summary</h3>
                <div class="summary-item">
                    <span>MRP Total</span>
                    <span>₹<?php echo number_format($cart_total, 2); ?></span>
                </div>
                 <?php if ($discount_amount > 0): ?>
                <div class="summary-item discount">
                    <span>Coupon Discount</span>
                    <span>- ₹<?php echo number_format($discount_amount, 2); ?></span>
                </div>
                <?php endif; ?>
                <div class="summary-item">
                    <span>Delivery Charge</span>
                    <span>₹<?php echo number_format(DELIVERY_CHARGE, 2); ?></span>
                </div>
                <div class="summary-total">
                    <strong>Grand Total</strong>
                    <strong>₹<?php echo number_format($grand_total, 2); ?></strong>
                </div>
                <p class="tax-note" style="text-align: center; margin-top: 15px;">(Grand Total is inclusive of all taxes)</p>
                
                <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
                <input type="hidden" name="place_order" value="1">

                <button type="submit" id="checkout-btn" class="checkout-btn">Place Order</button>
            </div>
        </div>
    </form>
</div>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('checkout-form');
    const checkoutBtn = document.getElementById('checkout-btn');
    const paymentRadios = document.querySelectorAll('input[name="payment_method"]');

    function updateButtonText() {
        const selectedMethod = document.querySelector('input[name="payment_method"]:checked').value;
        if (selectedMethod === 'COD') {
            checkoutBtn.textContent = 'Place Order (COD)';
        } else {
            checkoutBtn.textContent = 'Pay ₹<?php echo number_format($grand_total, 2); ?> with Razorpay';
        }
    }

    paymentRadios.forEach(radio => radio.addEventListener('change', updateButtonText));
    updateButtonText();

    form.addEventListener('submit', function(e) {
        const selectedMethod = document.querySelector('input[name="payment_method"]:checked').value;
        if (selectedMethod === 'Razorpay') {
            e.preventDefault();
            var options = {
                "key": "<?php echo RAZORPAY_KEY_ID; ?>",
                "amount": "<?php echo $grand_total_in_paise; ?>",
                "currency": "INR",
                "name": "iStore",
                "description": "Order Payment",
                "image": "images/logo.png",
                "handler": function (response){
                    document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
                    form.submit();
                },
                "prefill": {
                    "name": "<?php echo htmlspecialchars($user_data['name']); ?>",
                    "email": "<?php echo htmlspecialchars($user_data['email']); ?>",
                    "contact": "<?php echo htmlspecialchars($user_data['mobile']); ?>"
                },
                "theme": {
                    "color": "#0071e3"
                }
            };
            var rzp1 = new Razorpay(options);
            rzp1.open();
        }
    });
});
</script>

<?php require 'includes/footer.php'; ?>