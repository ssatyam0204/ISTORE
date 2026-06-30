<?php 
require 'includes/header.php'; 

if (!isset($_SESSION['user_id'])) {
    header('location: login.php');
    exit();
}
$user_id = $_SESSION['user_id'];
$profile_message = '';
$password_message = '';

// Handle Profile Update
if (isset($_POST['update_profile'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $mobile = mysqli_real_escape_string($conn, $_POST['mobile']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);

    $query = "UPDATE users SET name='$name', email='$email', mobile='$mobile', address='$address' WHERE id=$user_id";
    if (mysqli_query($conn, $query)) {
        $_SESSION['user_name'] = $name;
        $profile_message = "Profile updated successfully!";
    } else {
        $profile_message = "Error updating profile.";
    }
}

// Handle Password Change
if (isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    $result = mysqli_query($conn, "SELECT password FROM users WHERE id=$user_id");
    $user = mysqli_fetch_assoc($result);
    
    if (password_verify($current_password, $user['password'])) {
        if ($new_password == $confirm_password) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $query = "UPDATE users SET password='$hashed_password' WHERE id=$user_id";
            if(mysqli_query($conn, $query)) {
                $password_message = "Password changed successfully!";
            } else {
                $password_message = "Error changing password.";
            }
        } else {
            $password_message = "New passwords do not match.";
        }
    } else {
        $password_message = "Incorrect current password.";
    }
}

$user_result = mysqli_query($conn, "SELECT * FROM users WHERE id=$user_id");
$user_data = mysqli_fetch_assoc($user_result);
?>
<div class="page-container">
    <h1>My Profile</h1>
    <div class="profile-container">
        <div class="details-card">
            <h3>My Details</h3>
            <?php if ($profile_message): ?>
                <p class="form-message"><?php echo $profile_message; ?></p>
            <?php endif; ?>
            <form action="profile.php" method="POST" class="profile-form">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user_data['name']); ?>" required>
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" required>
                <label for="mobile">Mobile Number</label>
                <input type="text" id="mobile" name="mobile" value="<?php echo htmlspecialchars($user_data['mobile']); ?>">
                <label for="address">Address</label>
                <textarea id="address" name="address" rows="4"><?php echo htmlspecialchars($user_data['address']); ?></textarea>
                <button type="submit" name="update_profile">Update Profile</button>
            </form>
        </div>
        <div class="details-card">
            <h3>Change Password</h3>
            <?php if ($password_message): ?>
                <p class="form-message"><?php echo $password_message; ?></p>
            <?php endif; ?>
            <form action="profile.php" method="POST" class="profile-form">
                <label for="current_password">Current Password</label>
                <input type="password" id="current_password" name="current_password" required>
                <label for="new_password">New Password</label>
                <input type="password" id="new_password" name="new_password" required>
                <label for="confirm_password">Confirm New Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
                <button type="submit" name="change_password">Change Password</button>
            </form>
        </div>
    </div>
</div>
<?php require 'includes/footer.php'; ?>