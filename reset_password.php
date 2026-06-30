<?php
// Force local Indian Standard Time to eliminate timezone mismatches on localhost
date_default_timezone_set('Asia/Kolkata');

require 'includes/db_connect.php';
$message = '';
$token_valid = false;
$token = '';

// Check if token is present in the URL (GET) or form submission (POST)
if (isset($_GET['token']) && !empty($_GET['token'])) {
    $token = mysqli_real_escape_string($conn, $_GET['token']);
} elseif (isset($_POST['token']) && !empty($_POST['token'])) {
    $token = mysqli_real_escape_string($conn, $_POST['token']);
}

if (!empty($token)) {
    // Select the user record based on the token
    $query = "SELECT * FROM users WHERE reset_token = '$token'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        
        // Secondary backup verification check using timestamps
        $current_time = time();
        $expiry_time = strtotime($user['token_expiry']);

        // Check if token has expired
        if ($expiry_time > $current_time) {
            $token_valid = true;
        } else {
            $message = "This password reset link has expired. Please request a new one.";
        }
    } else {
        // This is where it was hitting before because $token was losing its value
        $message = "This password reset link is invalid.";
    }
} else {
    header("Location: login.php");
    exit();
}

// Processing the new password form update submission
if (isset($_POST['reset_password']) && $token_valid) {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password === $confirm_password) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        // Update user record and clear active tokens
        $update_query = "UPDATE users SET password = '$hashed_password', reset_token = NULL, token_expiry = NULL WHERE reset_token = '$token'";
        
        if (mysqli_query($conn, $update_query)) {
            $message = "Your password has been reset successfully! You can now <a href='login.php' style='color: #2563eb; font-weight: bold;'>log in</a>.";
            $token_valid = false;
        } else {
            $message = "An internal database error occurred. Please try again.";
        }
    } else {
        $message = "The two passwords do not match.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - iStore</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="form-container">
        <h2>Reset Your Password</h2>
        
        <?php if (!empty($message)): ?>
            <p class="form-message"><?php echo $message; ?></p>
        <?php endif; ?>

        <?php if ($token_valid): ?>
        <form action="reset_password.php" method="POST">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
            
            <label for="new_password">New Password</label>
            <input type="password" name="new_password" required>
            
            <label for="confirm_password">Confirm New Password</label>
            <input type="password" name="confirm_password" required>
            
            <button type="submit" name="reset_password">Reset Password</button>
        </form>
        <?php endif; ?>
    </div>
</body>
</html>