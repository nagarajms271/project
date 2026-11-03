<?php
session_start();
include '../db.php';

if (empty($_SESSION['is_admin'])) {
    header("Location: admin_login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" &&
    isset($_POST['order_id'], $_POST['status'])) {

    $id = filter_input(INPUT_POST, 'order_id', FILTER_VALIDATE_INT);
    $status = $_POST['status'];

    $allowed = ['Pending', 'Preparing', 'Delivered', 'Cancelled'];

    if ($id && in_array($status, $allowed, true)) {
        $stmt = mysqli_prepare(
            $conn,
            "UPDATE orders SET status = ? WHERE id = ?"
        );

        mysqli_stmt_bind_param($stmt, "si", $status, $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    header("Location: view_orders.php");
    exit;
}

$sql = "SELECT orders.id, users.name, orders.total_amount,
               orders.order_date, orders.status
        FROM orders
        JOIN users ON orders.user_id = users.id
        ORDER BY orders.id DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Customer Orders</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<header><h1>Customer Orders</h1></header>

<div class="container">

<?php while ($order = mysqli_fetch_assoc($result)) { ?>

    <div class="card">

        <h3>Order #<?php echo $order['id']; ?></h3>

        <p>Customer: <?php echo htmlspecialchars($order['name']); ?></p>

        <p>Total: &#8377;<?php echo number_format($order['total_amount'], 2); ?></p>

        <p>Date: <?php echo htmlspecialchars($order['order_date']); ?></p>

        <p>Current Status:
            <?php echo htmlspecialchars($order['status']); ?>
        </p>

        <form method="POST">

            <input type="hidden" name="order_id"
                   value="<?php echo $order['id']; ?>">

            <select name="status">
                <?php
                foreach (
                    ['Pending', 'Preparing', 'Delivered', 'Cancelled']
                    as $status
                ) {
                    $selected = $order['status'] === $status
                        ? 'selected'
                        : '';

                    echo '<option value="' .
                         htmlspecialchars($status) . '" ' .
                         $selected . '>' .
                         htmlspecialchars($status) .
                         '</option>';
                }
                ?>
            </select>

            <button type="submit">Update Status</button>

        </form>

    </div>

<?php } ?>

<br>
<a href="dashboard.php">Back to Dashboard</a>

</div>

</body>
</html>