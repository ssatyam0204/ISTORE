<?php
// Force local Indian Standard Time to eliminate timezone mismatches on localhost
date_default_timezone_set('Asia/Kolkata');

require '../includes/db_connect.php';
$message = '';
$email = $_GET['email'] ?? '';

if (isset($_POST['reset_password'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $otp = mysqli_real_escape_string($conn, $_POST['otp']);
    $new_pass = $_POST['new_password'];

    // Select the admin record by email and OTP token string first
    $query = "SELECT * FROM admins WHERE email = '$email' AND reset_token = '$otp'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $admin = mysqli_fetch_assoc($result);
        
        // Dual-verification check using both PHP runtime time and MySQL server string formats
        $current_time = time();
        $expiry_time = strtotime($admin['token_expiry']);

        // Check if the token expiry time is greater than the current instant
        if ($expiry_time > $current_time) {
            $hashed_password = password_hash($new_pass, PASSWORD_DEFAULT);
            
            // Update admin record and wipe the OTP tokens to prevent form re-submission loops
            $update_query = "UPDATE admins SET password = '$hashed_password', reset_token = NULL, token_expiry = NULL WHERE email = '$email'";
            mysqli_query($conn, $update_query);
            
            $message = "Password reset successfully! <a href='index.php' style='color: #2563eb; font-weight: bold;'>Login now</a>.";
        } else {
            $message = "Invalid or expired OTP.";
        }
    } else {
        $message = "Invalid or expired OTP.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Reset Password</title>
    <link rel="stylesheet" href="../css/admin_login_style.css">
</head>
<body>
    <div class="form-container">
        <form action="reset_password.php" method="POST">
            <h2>Enter OTP and New Password</h2>
            
            <?php if ($message): ?>
                <p class="form-message"><?php echo $message; ?></p>
            <?php endif; ?>
            
            <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
            
            <label for="otp">OTP</label>
            <input type="text" name="otp" id="otp" placeholder="Enter your OTP" required>
            
            <label for="new_password">New Password</label>
            <input type="password" name="new_password" id="new_password" placeholder="Enter new password" required>
            
            <button type="submit" name="reset_password">Reset Password</button>
        </form>
    </div>
</body>
</html>