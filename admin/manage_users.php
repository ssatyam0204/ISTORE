<?php
require 'includes/header.php';

// Handle Block User
if (isset($_GET['block_id'])) {
    $user_id = intval($_GET['block_id']);
    $query = "UPDATE users SET is_blocked = 1 WHERE id=$user_id";
    mysqli_query($conn, $query);
    header("Location: manage_users.php");
    exit();
}

// Handle Unblock User
if (isset($_GET['unblock_id'])) {
    $user_id = intval($_GET['unblock_id']);
    $query = "UPDATE users SET is_blocked = 0 WHERE id=$user_id";
    mysqli_query($conn, $query);
    header("Location: manage_users.php");
    exit();
}

$users_result = mysqli_query($conn, "SELECT * FROM users ORDER BY created_at DESC");
?>

<h2>Manage Users</h2>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Registered On</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php while($user = mysqli_fetch_assoc($users_result)): ?>
        <tr>
            <td><?php echo $user['id']; ?></td>
            <td><?php echo htmlspecialchars($user['name']); ?></td>
            <td><?php echo htmlspecialchars($user['email']); ?></td>
            <td><?php echo date('d M Y', strtotime($user['created_at'])); ?></td>
            <td><?php echo ($user['is_blocked'] == 1) ? 'Blocked' : 'Active'; ?></td>
            <td>
                <?php if ($user['is_blocked'] == 1): ?>
                    <a href="manage_users.php?unblock_id=<?php echo $user['id']; ?>">Unblock</a>
                <?php else: ?>
                    <a href="manage_users.php?block_id=<?php echo $user['id']; ?>" onclick="return confirm('Are you sure you want to block this user?');">Block</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php require 'includes/footer.php'; ?>