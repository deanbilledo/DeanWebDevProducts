<?php
require_once 'functions.php';
require_once 'account.class.php';

session_start();

$username = $password = '';
$accountObj = new Account();
$loginErr = '';


if (isset($_SESSION['account'])) {
    header('Location: dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = clean_input($_POST['username']);
    $password = clean_input($_POST['password']);

    if ($accountObj->login($username, $password)) {
        $data = $accountObj->fetch($username);

        if ($data) {
            $_SESSION['account'] = $data;

            // Redirect based on user role
            if ($data['is_admin']) {
                header('Location: dashboard.php');
            } elseif ($data['is_staff']) {
                header('Location: dashboard.php');
            } else {
                header('Location: index.php');
            }
            exit();
        } else {
            $loginErr = 'User data could not be fetched.';
        }
    } else {
        $loginErr = 'Invalid username/password';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        .error {
            color: red;
        }
    </style>
</head>
<body>
    <!-- Login Form -->
    <form action="login.php" method="post">
        <h2>Login</h2>
        <label for="username">Username/Email</label>
        <br>
        <input type="text" name="username" id="username" required>
        <br>
        <label for="password">Password</label>
        <br>
        <input type="password" name="password" id="password" required>
        <br>
        <input type="submit" value="Login" name="login">
        
       
        <?php if (!empty($loginErr)): ?>
            <p class="error"><?= $loginErr ?></p>
        <?php endif; ?>
    </form>

   
    <form action="register.php" method="get">
        <input type="submit" value="Register" name="register">
    </form>
</body>
</html>