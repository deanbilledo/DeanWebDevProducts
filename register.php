<?php
require_once 'functions.php';
require_once 'account.class.php';

$first_name = $last_name = $username = $password = $confirm_password = '';
$registerErr = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $accountObj = new Account();

    $first_name = clean_input($_POST['first_name']);
    $last_name = clean_input($_POST['last_name']);
    $username = clean_input($_POST['username']);
    $password = clean_input($_POST['password']);
    $confirm_password = clean_input($_POST['confirm_password']);
    $role = "Customer";
    $is_staff = false;
    $is_admin = false;


    if ($password !== $confirm_password) {
        $registerErr = 'Passwords do not match.';
    } elseif (strtolower($password) === strtolower($first_name) || strtolower($password) === strtolower($last_name)) {
        $registerErr = 'Weak password. Password should not be your first name or last name.';
    } elseif (strlen($password) < 8) {
        $registerErr = 'Password needs to be at least 8 characters long.';
    } elseif (!preg_match('/[A-Z]/', $password)) {
        $registerErr = 'Password needs to contain at least one capital letter.';
    } elseif (!preg_match('/[a-z]/', $password)) {
        $registerErr = 'Password needs to contain at least one lowercase letter.';
    } elseif (!preg_match('/[0-9]/', $password)) {
        $registerErr = 'Password needs to contain at least one number.';
    } elseif (!preg_match('/[\W_]/', $password)) { 
        $registerErr = 'Password needs to contain at least one special character.';
    } else {
        // Set the account details
        $accountObj->first_name = $first_name;
        $accountObj->last_name = $last_name;
        $accountObj->username = $username;
        $accountObj->password = $password;
        $accountObj->role = $role;
        $accountObj->is_staff = $is_staff;
        $accountObj->is_admin = $is_admin;

        // Attempt registration
        $registerErr = $accountObj->register();

        if ($registerErr === true) {
            // Registration successful, redirect to login page
            header('location: confirm.php');
            exit();
        } else {
            $registerErr = "Error: " . $registerErr;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        .error {
            color: red;
        }
    </style>
</head>
<body>
    <form action="register.php" method="post">
        <h2>Register</h2>
        <label for="first_name">First Name</label>
        <br>
        <input type="text" name="first_name" id="first_name" value="<?= htmlspecialchars($first_name) ?>" required>
        <br>
        <label for="last_name">Last Name</label>
        <br>
        <input type="text" name="last_name" id="last_name" value="<?= htmlspecialchars($last_name) ?>" required>
        <br>
        <label for="username">Username/Email</label>
        <br>
        <input type="text" name="username" id="username" value="<?= htmlspecialchars($username) ?>" required>
        <br>
        <label for="password">Password</label>
        <br>
        <input type="password" name="password" id="password" required>
        <br>
        <label for="confirm_password">Confirm Password</label>
        <br>
        <input type="password" name="confirm_password" id="confirm_password" required>
        <br>
        <input type="submit" value="Register" name="register">
        <?php
        if (!empty($registerErr)) {
            echo '<p class="error">' . htmlspecialchars($registerErr) . '</p>';
        }
        ?>
    </form>

        <a href="login.php">LOGIN</a>

</body>
</html>