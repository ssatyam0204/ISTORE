<?php
session_start();
require 'includes/db_connect.php';

$errors = [];

if (isset($_POST['login_btn'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    if (empty($email)) { array_push($errors, "Email is required"); }
    if (empty($password)) { array_push($errors, "Password is required"); }

    if (count($errors) == 0) {
        $query = "SELECT * FROM users WHERE email='$email'";
        $results = mysqli_query($conn, $query);

        if (mysqli_num_rows($results) == 1) {
            $user = mysqli_fetch_assoc($results);
            
            if (password_verify($password, $user['password'])) {
                if ($user['is_blocked'] == 1) {
                    array_push($errors, "Your account has been blocked. Please contact support.");
                } else {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['name'];
                    
                    if (isset($_SESSION['redirect_after_login'])) {
                        $redirect_url = $_SESSION['redirect_after_login'];
                        unset($_SESSION['redirect_after_login']);
                        header('location: ' . $redirect_url);
                    } else {
                        header('location: index.php');
                    }
                    exit();
                }
            } else {
                array_push($errors, "Wrong email/password combination");
            }
        } else {
            array_push($errors, "Wrong email/password combination");
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - iStore</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="form-container">
        <form action="login.php" method="POST">
            <h2>Login to continue</h2>
            <?php if (count($errors) > 0) : ?>
                <div class="error">
                    <?php foreach ($errors as $error) : ?>
                        <p><?php echo $error; ?></p>
                    <?php endforeach ?>
                </div>
            <?php endif ?>
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" required>
            
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
            
            <p class="form-link" style="text-align: right; margin-top: -10px; margin-bottom: 20px;">
                <a href="forgot_password.php">Forgot Password?</a>
            </p>

            <button type="submit" name="login_btn">Login</button>
            
            <p class="form-link">
                Don't have an account? <a href="register.php">Register here</a>
            </p>
        </form>
    </div>
</body>
</html>