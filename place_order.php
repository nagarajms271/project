<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$cart = $_SESSION['cart'];

mysqli_begin_transaction($conn);

try {
    $items = [];
    $total = 0;

    foreach ($cart as $food_id => $quantity) {
        $food_id = (int)$food_id;
        $quantity = (int)$quantity;

        if ($food_id <= 0 || $quantity < 1 || $quantity > 20) {
            throw new Exception("Invalid cart item.");
        }

        $stmt = mysqli_prepare(
            $conn,
            "SELECT name, price FROM foods WHERE id = ?"
        );

        mysqli_stmt_bind_param($stmt, "i", $food_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $name, $price);

        if (!mysqli_stmt_fetch($stmt)) {
            mysqli_stmt_close($stmt);
            throw new Exception("A food item is no longer available.");
        }

        mysqli_stmt_close($stmt);

        $items[] = [
            'id' => $food_id,
            'quantity' => $quantity,
            'price' => $price
        ];

        $total += $price * $quantity;
    }

    $stmt = mysqli_prepare(
        $conn,
        "INSERT INTO orders (user_id, total_amount) VALUES (?, ?)"
    );

    mysqli_stmt_bind_param($stmt, "id", $user_id, $total);
    mysqli_stmt_execute($stmt);

    $order_id = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);

    $stmt = mysqli_prepare(
        $conn,
        "INSERT INTO order_items (order_id, food_id, quantity, price)
         VALUES (?, ?, ?, ?)"
    );

    foreach ($items as $item) {
        mysqli_stmt_bind_param(
            $stmt,
            "iiid",
            $order_id,
            $item['id'],
            $item['quantity'],
            $item['price']
        );

        mysqli_stmt_execute($stmt);
    }

    mysqli_stmt_close($stmt);
    mysqli_commit($conn);

    unset($_SESSION['cart']);

    echo "<h2>Order placed successfully!</h2>";
    echo "<p>Your order number is: " . (int)$order_id . "</p>";
    echo "<a href='my_orders.php'>View My Orders</a>";

} catch (Throwable $e) {
    mysqli_rollback($conn);
    http_response_code(500);
    echo "Unable to place the order. Please check your cart and try again.";
}
?>