<?php
session_start();
require '../includes/db_connect.php';

$errors = [];

if (isset($_POST['admin_login_btn'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    if (empty($username)) {
        array_push($errors, "Username is required");
    }
    if (empty($password)) {
        array_push($errors, "Password is required");
    }

    if (count($errors) == 0) {
        $query = "SELECT * FROM admins WHERE username='$username'";
        $results = mysqli_query($conn, $query);

        if (mysqli_num_rows($results) == 1) {
            $admin = mysqli_fetch_assoc($results);
            if (password_verify($password, $admin['password'])) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                header('location: dashboard.php');
                exit();
            } else {
                array_push($errors, "Wrong username/password combination");
            }
        } else {
            array_push($errors, "Wrong username/password combination");
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - iStore</title>
    <link rel="stylesheet" href="../css/admin_login_style.css">
</head>
<body>
    <div class="form-container">
        <form action="index.php" method="POST">
            <h2>iStore Admin Login</h2>
            <?php if (count($errors) > 0) : ?>
                <div class="error"><?php foreach ($errors as $error) : ?><p><?php echo $error; ?></p><?php endforeach ?></div>
            <?php endif; ?>
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
            <p class="form-link" style="text-align: right; margin-top: -10px; margin-bottom: 20px;">
                <a href="forgot_password.php">Forgot Password?</a>
            </p>
            <button type="submit" name="admin_login_btn">Login</button>
        </form>
    </div>
</body>
</html>