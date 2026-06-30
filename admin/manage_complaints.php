<?php
require 'includes/header.php';

if (isset($_GET['close_id'])) {
    $id = intval($_GET['close_id']);
    $query = "UPDATE complaints SET status = 'Closed' WHERE id=$id";
    mysqli_query($conn, $query);
    header("Location: manage_complaints.php");
    exit();
}

$complaints_query = "SELECT c.*, u.name as user_name 
                     FROM complaints c 
                     JOIN users u ON c.user_id = u.id 
                     ORDER BY c.status ASC, c.created_at DESC";
$complaints_result = mysqli_query($conn, $complaints_query);
?>
<h2>Manage Complaints</h2>
<table>
    <thead>
        <tr>
            <th>Ticket ID</th>
            <th>Order ID</th>
            <th>User</th>
            <th>Subject</th>
            <th>Image</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php while($row = mysqli_fetch_assoc($complaints_result)): ?>
        <tr>
            <td>#<?php echo $row['id']; ?></td>
            <td><a href="order_details.php?id=<?php echo $row['order_id']; ?>">#<?php echo $row['order_id']; ?></a></td>
            <td><?php echo htmlspecialchars($row['user_name']); ?></td>
            <td><?php echo htmlspecialchars($row['subject']); ?></td>
            <td>
                <?php if (!empty($row['image'])): ?>
                    <a href="../images/reviews/<?php echo htmlspecialchars($row['image']); ?>" target="_blank">View Image</a>
                <?php else: ?>
                    <span>No Image</span>
                <?php endif; ?>
            </td>
            <td><?php echo $row['status']; ?></td>
            <td>
                <?php if ($row['status'] == 'Open'): ?>
                    <a href="reply_complaint.php?id=<?php echo $row['id']; ?>">Reply</a>
                    <a href="manage_complaints.php?close_id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?');">Mark as Closed</a>
                <?php else: ?>
                    <span>-</span>
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
<?php require 'includes/footer.php'; ?>