<?php
require 'includes/header.php';

if (isset($_POST['add_coupon'])) {
    $code = mysqli_real_escape_string($conn, $_POST['coupon_code']);
    $type = mysqli_real_escape_string($conn, $_POST['discount_type']);
    $value = floatval($_POST['discount_value']);
    $expiry = mysqli_real_escape_string($conn, $_POST['expiry_date']);
    
    $query = "INSERT INTO coupons (coupon_code, discount_type, discount_value, expiry_date) VALUES ('$code', '$type', '$value', '$expiry')";
    mysqli_query($conn, $query);
    header("Location: manage_coupons.php");
    exit();
}

if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    mysqli_query($conn, "DELETE FROM coupons WHERE id=$id");
    header("Location: manage_coupons.php");
    exit();
}

$coupons_result = mysqli_query($conn, "SELECT * FROM coupons ORDER BY id DESC");
?>

<h2>Manage Coupons</h2>
<h3>Add New Coupon</h3>
<form action="manage_coupons.php" method="POST" class="coupon-form">
    <input type="text" name="coupon_code" placeholder="Coupon Code" required>
    <select name="discount_type" required>
        <option value="percentage">Percentage (%)</option>
        <option value="fixed">Fixed Amount (₹)</option>
    </select>
    <input type="number" step="0.01" name="discount_value" placeholder="Value" required>
    <input type="date" name="expiry_date" required>
    <button type="submit" name="add_coupon">Add Coupon</button>
</form>

<h3>Existing Coupons</h3>
<table>
    <thead>
        <tr>
            <th>Code</th>
            <th>Type</th>
            <th>Value</th>
            <th>Expiry Date</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php while($coupon = mysqli_fetch_assoc($coupons_result)): ?>
        <tr>
            <td><?php echo htmlspecialchars($coupon['coupon_code']); ?></td>
            <td><?php echo ucfirst($coupon['discount_type']); ?></td>
            <td><?php echo ($coupon['discount_type'] == 'percentage') ? $coupon['discount_value'] . '%' : '₹' . number_format($coupon['discount_value'], 2); ?></td>
            <td><?php echo date('d M Y', strtotime($coupon['expiry_date'])); ?></td>
            <td>
                <a href="manage_coupons.php?delete_id=<?php echo $coupon['id']; ?>" onclick="return confirm('Are you sure?');">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php require 'includes/footer.php'; ?>