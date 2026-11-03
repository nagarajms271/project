<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_POST['add_to_cart'])) {
    $food_id = filter_input(INPUT_POST, 'food_id', FILTER_VALIDATE_INT);
    $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);

    if ($food_id && $quantity && $quantity >= 1 && $quantity <= 20) {
        $stmt = mysqli_prepare($conn, "SELECT id FROM foods WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $food_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            $current = $_SESSION['cart'][$food_id] ?? 0;
            $_SESSION['cart'][$food_id] = min(20, $current + $quantity);
        }

        mysqli_stmt_close($stmt);
    }

    header("Location: cart.php");
    exit;
}

if (isset($_POST['remove'])) {
    $food_id = filter_input(INPUT_POST, 'remove', FILTER_VALIDATE_INT);

    if ($food_id) {
        unset($_SESSION['cart'][$food_id]);
    }

    header("Location: cart.php");
    exit;
}

$total = 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header><h1>Your Food Cart</h1></header>

<div class="container">

<?php
if (empty($_SESSION['cart'])) {
    echo "<p>Your cart is empty.</p>";
} else {
    foreach ($_SESSION['cart'] as $food_id => $quantity) {
        $stmt = mysqli_prepare(
            $conn,
            "SELECT name, price FROM foods WHERE id = ?"
        );

        mysqli_stmt_bind_param($stmt, "i", $food_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $name, $price);

        if (mysqli_stmt_fetch($stmt)) {
            $subtotal = $price * $quantity;
            $total += $subtotal;
?>

            <div class="card">
                <h3><?php echo htmlspecialchars($name); ?></h3>
                <p>Price: &#8377;<?php echo number_format($price, 2); ?></p>
                <p>Quantity: <?php echo $quantity; ?></p>
                <p>Subtotal: &#8377;<?php echo number_format($subtotal, 2); ?></p>

                <form method="POST">
                    <button type="submit" name="remove"
                            value="<?php echo $food_id; ?>">
                        Remove
                    </button>
                </form>
            </div>

<?php
        }

        mysqli_stmt_close($stmt);
    }
?>

    <h2>Total: &#8377;<?php echo number_format($total, 2); ?></h2>

    <a class="button" href="place_order.php">Place Order</a>

<?php } ?>

<br><br>
<a href="menu.php">Continue Shopping</a>

</div>

</body>
</html>