<?php
session_start();

if (!isset($_SESSION['account'])) {
    header('Location: login.php');
    exit();
}
if ($_SESSION['account']['role'] === 'Customer') {
    header('Location: index.php');
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
</head>
<body>
    <h2><?= 'Welcome ' . htmlspecialchars($_SESSION['account']['first_name']) ?></h2>

    <a href="product.php">Product</a>

    <br>

    <a href="logout.php">Logout</a>
</body>
</html>