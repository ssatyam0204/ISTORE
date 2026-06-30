<?php
// 1. Force local Indian Standard Time to eliminate timezone mismatches on localhost
date_default_timezone_set('Asia/Kolkata');

// 2. Require files. Notice we use "../" to back out of the admin folder and find includes
require '../includes/db_connect.php';
require '../includes/mailer_config.php';
require '../includes/credentials.php'; 

$message = '';

if (isset($_POST['send_otp'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $query = "SELECT * FROM admins WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $otp = rand(100000, 999999);
        
        // Generates the 10-minute expiry strictly in Asia/Kolkata time context
        $expiry = date("Y-m-d H:i:s", strtotime('+10 minutes'));
        
        mysqli_query($conn, "UPDATE admins SET reset_token='$otp', token_expiry='$expiry' WHERE email='$email'");

        $mail = get_mailer();
        try {
            // USING THE MASTER CONSTANTS FROM CREDENTIALS.PHP HERE:
            $mail->Username = SMTP_EMAIL;
            $mail->Password = SMTP_PASSWORD;
            $mail->setFrom(SMTP_EMAIL, 'iStore Admin Panel');
            
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Your Admin Password Reset OTP';
            $mail->Body    = "Your OTP to reset your admin password is: <strong>$otp</strong>. It is valid for 10 minutes.";
            $mail->send();
            
            header('Location: reset_password.php?email=' . urlencode($email));
            exit();
        } catch (Exception $e) { 
            $message = "Mailer Error: OTP could not be sent.";
        }
    } else {
        $message = "No admin account found with that email address.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Forgot Password</title>
    <link rel="stylesheet" href="../css/admin_login_style.css">
</head>
<body>
    <div class="form-container">
        <form action="forgot_password.php" method="POST">
            <h2>Reset Admin Password</h2>
            <p>Enter your admin email address to receive an OTP.</p>
            
            <?php if ($message): ?>
                <p class="form-message" style="color: #dc2626; background: #fee2e2; padding: 10px; border-radius: 5px; text-align: center; font-size: 14px; margin-bottom: 15px;">
                    <?php echo $message; ?>
                </p>
            <?php endif; ?>
            
            <label for="email">Email Address</label>
            <input type="email" name="email" id="email" placeholder="admin@istore.com" required>
            
            <button type="submit" name="send_otp">Send OTP</button>
            
            <p class="form-link" style="text-align: center; margin-top: 20px;">
                <a href="index.php" style="color: #2563eb; font-weight: bold; text-decoration: none;">Back to Login</a>
            </p>
        </form>
    </div>
</body>
</html>