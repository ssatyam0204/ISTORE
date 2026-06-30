<?php
// 1. Force local Indian Standard Time to eliminate timezone mismatches on localhost
date_default_timezone_set('Asia/Kolkata');

// 2. Pull in your database connection AND your secret credentials file
require 'includes/db_connect.php';
require 'includes/credentials.php'; 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'lib/PHPMailer/Exception.php';
require 'lib/PHPMailer/PHPMailer.php';
require 'lib/PHPMailer/SMTP.php';

$message = '';

if (isset($_POST['send_reset_link'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $token = bin2hex(random_bytes(50));
        
        // Generates the 1-hour expiry strictly in Asia/Kolkata context
        $expiry_time = date("Y-m-d H:i:s", strtotime('+1 hour'));

        $update_query = "UPDATE users SET reset_token='$token', token_expiry='$expiry_time' WHERE email='$email'";
        mysqli_query($conn, $update_query);

        $reset_link = "http://localhost/istore/reset_password.php?token=$token";

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            
            // USING THE MASTER CONSTANTS FROM CREDENTIALS.PHP HERE:
            $mail->Username = SMTP_EMAIL; 
            $mail->Password = SMTP_PASSWORD; 
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom(SMTP_EMAIL, 'iStore');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request for iStore';
            $mail->Body    = "Hello,<br><br>Please click on the link below to reset your password:<br><a href='$reset_link'>$reset_link</a><br><br>This link is valid for 1 hour.<br><br>Thanks,<br>The iStore Team";

            $mail->send();
        } catch (Exception $e) {
            // Suppress mail errors silently for clean user interface updates
        }
    }
    $message = "If an account with that email exists, a password reset link has been sent.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - iStore</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="form-container">
        <form action="forgot_password.php" method="POST">
            <h2>Forgot Password</h2>
            <p>Enter your email address and we will send you a link to reset your password.</p>
            
            <?php if ($message): ?>
                <p class="form-message"><?php echo $message; ?></p>
            <?php endif; ?>

            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" required>
            
            <button type="submit" name="send_reset_link">Send Reset Link</button>
            
            <p class="form-link">
                Remember your password? <a href="login.php">Login here</a>
            </p>
        </form>
    </div>
</body>
</html>