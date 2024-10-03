<?php
session_start();

if (!isset($_SESSION['account'])) {
    header('Location: login.php');
    exit();
}

// Redirect customers to index.php
if ($_SESSION['account']['role'] === 'customer') {
    header('Location: index.php');
    exit();
}

require_once('functions.php');
require_once('product.class.php');
require_once('stocks.class.php');

$name = $quantity = $status = $reason = '';
$quantityErr = $statusErr = $reasonErr = '';
$productObj = new Product(); 
$stocksObj = new Stocks();

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $record = $productObj->fetchRecord($id);
        if (!empty($record)) {
            $name = $record['name'];
        } else {
            echo 'No product found';
            exit;
        }
    } else {
        echo 'No product found';
        exit;
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_GET['id'];
    $record = $productObj->fetchRecord($id);
    if (!empty($record)) {
        $name = $record['name'];
    } else {
        echo 'No product found';
        exit;
    }
    $product_id = clean_input($_GET['id']);
    $quantity = clean_input($_POST['quantity']);
    $status = isset($_POST['status']) ? clean_input($_POST['status']) : '';
    $reason = clean_input($_POST['reason']);

    if (empty($quantity)) {
        $quantityErr = 'Quantity is required';
    } elseif (!is_numeric($quantity)) {
        $quantityErr = 'Quantity should be a number';
    } elseif ($quantity < 1) {
        $quantityErr = 'Quantity must be greater than 0';
    } elseif ($status == 'out' && $quantity > $stocksObj->getAvailableStocks($product_id)) {
        $rem = ($stocksObj->getAvailableStocks($product_id)) ? $stocksObj->getAvailableStocks($product_id) : 0;
        $quantityErr = "Quantity must be less than the Available Stocks: $rem";
    }

    if (empty($status)) {
        $statusErr = 'Status is required';
    }

    if (empty($reason) && $status == 'out') {
        $reasonErr = 'Reason is required';
    }

    if (empty($quantityErr) && empty($statusErr) && empty($reasonErr)) {
        $stocksObj->product_id = $product_id;
        $stocksObj->quantity = $quantity;
        $stocksObj->status = $status;
        $stocksObj->reason = $reason;

        if ($stocksObj->add()) {
            header('Location: product.php');
        } else {
            echo 'Something went wrong when stocking the product';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock In/Out Product <?= htmlspecialchars($name) ?></title>
    <style>
        .error {
            color: red;
        }
        .d-none {
            display: none;
        }
        .d-block {
            display: block;
        }
    </style>
</head>
<body>
    <form action="?id=<?= htmlspecialchars($id) ?>" method="post">
        <h3>Stock In/Out for Product <?= htmlspecialchars($name) ?></h3>
        <!-- Display a note indicating required fields -->
        <span class="error">* are required fields</span>
        <br>

        <label for="quantity">Quantity</label><span class="error">*</span>
        <br>
        <input type="number" name="quantity" id="quantity" value="<?= htmlspecialchars($quantity) ?>">
        <br>
        <?php if (!empty($quantityErr)): ?>
            <span class="error"><?= htmlspecialchars($quantityErr) ?></span>
            <br>
        <?php endif; ?>

        <label for="status">Status</label><span class="error">*</span>
        <input type="radio" class="stocks" value="in" name="status" id="stockin" <?= $status == 'in' ? 'checked' : '' ?>><label for="stockin">Stock In</label>
        <?php if ($_SESSION['account']['role'] === 'admin') { ?>
            <input type="radio" class="stocks" value="out" name="status" id="stockout" <?= $status == 'out' ? 'checked' : '' ?>><label for="stockout">Stock Out</label>
        <?php } ?>
        <br>
        <?php if (!empty($statusErr)): ?>
            <span class="error"><?= htmlspecialchars($statusErr) ?></span>
            <br>
        <?php endif; ?>

        <div id="reason" class="<?= $status == 'out' ? '' : 'd-none' ?>">
            <label for="reason">Reason</label><span class="error">*</span>
            <br>
            <textarea name="reason" id="reason" cols="30"><?= htmlspecialchars($reason) ?></textarea>
            <br>
            <?php if (!empty($reasonErr)): ?>
                <span class="error"><?= htmlspecialchars($reasonErr) ?></span>
                <br>
            <?php endif; ?>
        </div>

        <input type="submit" value="Save Stocks">
    </form>

    <script>
        let stocksRDB = document.querySelectorAll('.stocks');
        let reason = document.querySelector('#reason');

        stocksRDB.forEach(rdb => {
            rdb.addEventListener('click', function() {
                if (this.value == 'in') {
                    reason.classList.add('d-none');
                } else {
                    reason.classList.remove('d-none');
                }
            });
        });
    </script>
</body>
</html>