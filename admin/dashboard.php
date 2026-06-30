<?php
require 'includes/header.php';

// --- Filtering Logic ---
$where_clauses = [];
$selected_start_date = $_GET['start_date'] ?? '';
$selected_end_date = $_GET['end_date'] ?? '';
$selected_user_id = $_GET['customer_id'] ?? '';
$selected_status = $_GET['order_status'] ?? '';

if (!empty($selected_start_date)) {
    $where_clauses[] = "DATE(orders.order_date) >= '$selected_start_date'";
}
if (!empty($selected_end_date)) {
    $where_clauses[] = "DATE(orders.order_date) <= '$selected_end_date'";
}
if (!empty($selected_user_id)) {
    $where_clauses[] = "orders.user_id = " . intval($selected_user_id);
}
if (!empty($selected_status)) {
    $where_clauses[] = "orders.order_status = '" . mysqli_real_escape_string($conn, $selected_status) . "'";
}

$where_sql = "";
if (count($where_clauses) > 0) {
    $where_sql = " WHERE " . implode(' AND ', $where_clauses);
}

// --- Data Fetching ---
$revenue_query = "SELECT SUM(orders.total_amount) as total_revenue FROM orders" . $where_sql;
$revenue_result = mysqli_query($conn, $revenue_query);
$total_revenue = mysqli_fetch_assoc($revenue_result)['total_revenue'];

$orders_query = "SELECT COUNT(orders.id) as total_orders FROM orders" . $where_sql;
$orders_result = mysqli_query($conn, $orders_query);
$total_orders = mysqli_fetch_assoc($orders_result)['total_orders'];

$users_result = mysqli_query($conn, "SELECT id, name FROM users");

$best_selling_query = "SELECT p.name, SUM(oi.quantity) as total_sold 
                       FROM order_items oi 
                       JOIN products p ON oi.product_id = p.id 
                       JOIN orders ON oi.order_id = orders.id " . $where_sql . "
                       GROUP BY oi.product_id 
                       ORDER BY total_sold DESC 
                       LIMIT 5";
$best_selling_result = mysqli_query($conn, $best_selling_query);

$recent_orders_query = "SELECT orders.*, users.name as customer_name 
                        FROM orders 
                        JOIN users ON orders.user_id = users.id " . $where_sql . "
                        ORDER BY orders.order_date DESC 
                        LIMIT 5";
$recent_orders_result = mysqli_query($conn, $recent_orders_query);

// Chart Data Logic (now respects filters)
$chart_labels = [];
$chart_data = [];
$start_date_for_chart = $selected_start_date ?: date('Y-m-d', strtotime('-6 days'));
$end_date_for_chart = $selected_end_date ?: date('Y-m-d');

$start = new DateTime($start_date_for_chart);
$end = new DateTime($end_date_for_chart);
$interval = new DateInterval('P1D');
$period = new DatePeriod($start, $interval, $end->modify('+1 day'));

foreach ($period as $date) {
    $d = $date->format('Y-m-d');
    $chart_labels[] = $date->format('d M');
    
    $chart_where_clauses = $where_clauses;
    $chart_where_clauses[] = "DATE(orders.order_date) = '$d'";
    $chart_where_clauses[] = "orders.order_status = 'Delivered'";
    $chart_where_sql = " WHERE " . implode(' AND ', $chart_where_clauses);

    $query = "SELECT SUM(total_amount) as daily_sales FROM orders" . $chart_where_sql;
    $result = mysqli_query($conn, $query);
    $chart_data[] = mysqli_fetch_assoc($result)['daily_sales'] ?? 0;
}
?>

<h2>Dashboard Overview</h2>
<div class="details-card filter-bar">
    <form action="dashboard.php" method="GET">
        <label>From:</label><input type="date" name="start_date" value="<?php echo htmlspecialchars($selected_start_date); ?>">
        <label>To:</label><input type="date" name="end_date" value="<?php echo htmlspecialchars($selected_end_date); ?>">
        <label>Customer:</label>
        <select name="customer_id">
            <option value="">All Customers</option>
            <?php while($user = mysqli_fetch_assoc($users_result)): ?>
            <option value="<?php echo $user['id']; ?>" <?php if($selected_user_id == $user['id']) echo 'selected'; ?>><?php echo htmlspecialchars($user['name']); ?></option>
            <?php endwhile; ?>
        </select>
        <label>Status:</label>
        <select name="order_status">
            <option value="">All Statuses</option>
            <option value="Pending" <?php if($selected_status == 'Pending') echo 'selected'; ?>>Pending</option>
            <option value="Shipped" <?php if($selected_status == 'Shipped') echo 'selected'; ?>>Shipped</option>
            <option value="Delivered" <?php if($selected_status == 'Delivered') echo 'selected'; ?>>Delivered</option>
            <option value="Cancelled" <?php if($selected_status == 'Cancelled') echo 'selected'; ?>>Cancelled</option>
        </select>
        <button type="submit">Filter</button><a href="dashboard.php" class="clear-filter-btn">Clear</a>
    </form>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <h3>Total Revenue</h3>
        <p>₹<?php echo number_format($total_revenue ?? 0, 2); ?></p>
        <span>Based on filters (Delivered Only)</span>
    </div>
    <div class="stat-card">
        <h3>Total Orders</h3>
        <p><?php echo $total_orders; ?></p>
        <span>Based on filters</span>
    </div>
</div>

<div class="dashboard-grid">
    <div class="main-panel">
        <div class="chart-container">
            <h3>Sales Activity (Delivered Orders)</h3>
            <canvas id="salesChart"></canvas>
        </div>
        <div class="details-card">
            <h3>Recent Orders</h3>
            <table>
                <thead><tr><th>Order ID</th><th>Customer</th><th>Status</th><th>Total</th></tr></thead>
                <tbody>
                    <?php mysqli_data_seek($recent_orders_result, 0); while($order = mysqli_fetch_assoc($recent_orders_result)): ?>
                    <tr>
                        <td><a href="order_details.php?id=<?php echo $order['id']; ?>">#<?php echo $order['id']; ?></a></td>
                        <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                        <td><?php echo htmlspecialchars($order['order_status']); ?></td>
                        <td>₹<?php echo number_format($order['total_amount'], 2); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="side-panel">
        <div class="details-card">
            <h3>Top 5 Best-Selling Products</h3>
            <table>
                <thead><tr><th>Product Name</th><th>Sold</th></tr></thead>
                <tbody>
                    <?php mysqli_data_seek($best_selling_result, 0); while($product = mysqli_fetch_assoc($best_selling_result)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                        <td><?php echo $product['total_sold']; ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('salesChart');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode($chart_labels); ?>,
        datasets: [{
            label: 'Daily Sales (₹)',
            data: <?php echo json_encode($chart_data); ?>,
            borderColor: 'rgba(0, 113, 227, 1)',
            backgroundColor: 'rgba(0, 113, 227, 0.1)',
            borderWidth: 2,
            fill: true,
            tension: 0.3
        }]
    },
    options: { scales: { y: { beginAtZero: true } } }
});
</script>

<?php require 'includes/footer.php'; ?>