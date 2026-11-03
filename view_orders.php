
<?php
session_start();
include 'db.php';

if (empty($_SESSION['is_admin'])) {
    header("Location: admin_login.php");
    exit;
}

// Update order status
if ($_SERVER["REQUEST_METHOD"] == "POST"
    && isset($_POST['order_id'], $_POST['status'])) {

    $id = filter_input(
        INPUT_POST,
        'order_id',
        FILTER_VALIDATE_INT
    );

    $status = $_POST['status'];

    $allowed = [
        'Pending',
        'Preparing',
        'Delivered',
        'Cancelled'
    ];

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

// Get all customer orders
$sql = "
    SELECT
        o.id AS order_id,
        u.name AS customer_name,
        o.total_amount,
        o.order_date,
        o.status,
        oi.quantity,
        oi.price AS item_price,
        f.name AS food_name
    FROM orders o
    JOIN users u ON o.user_id = u.id
    JOIN order_items oi ON o.id = oi.order_id
    JOIN foods f ON oi.food_id = f.id
    ORDER BY o.id DESC, oi.id ASC
";

$result = mysqli_query($conn, $sql);

// Group food items belonging to the same order
$orders = [];

while ($row = mysqli_fetch_assoc($result)) {
    $id = $row['order_id'];

    if (!isset($orders[$id])) {
        $orders[$id] = [
            'customer' => $row['customer_name'],
            'total' => $row['total_amount'],
            'date' => $row['order_date'],
            'status' => $row['status'],
            'items' => []
        ];
    }

    $orders[$id]['items'][] = [
        'name' => $row['food_name'],
        'quantity' => $row['quantity'],
        'price' => $row['item_price']
    ];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin - Customer Orders</title>
    <link rel="stylesheet" href="../style.css">

    <style>
        .order-box {
            background: white;
            padding: 20px;
            margin: 20px auto;
            border-radius: 10px;
            box-shadow: 0 2px 8px #ccc;
            max-width: 750px;
            text-align: left;
        }

        .order-box table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        .order-box th,
        .order-box td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        .order-box th {
            background: #e85d04;
            color: white;
        }

        .order-box select {
            padding: 8px;
        }
    </style>
</head>

<body>

<header>
    <h1>Admin - Customer Orders</h1>
</header>

<div class="container">

<?php if (empty($orders)) { ?>

    <h3>No orders found.</h3>

<?php } else { ?>

    <?php foreach ($orders as $id => $order) { ?>

        <div class="order-box">

            <h2>Order #<?php echo $id; ?></h2>

            <p>
                <strong>Customer:</strong>
                <?php echo htmlspecialchars($order['customer']); ?>
            </p>

            <p>
                <strong>Order Date:</strong>
                <?php echo htmlspecialchars($order['date']); ?>
            </p>

            <h3>Food Items Ordered</h3>

            <table>
                <tr>
                    <th>Food Item</th>
                    <th>Quantity</th>
                    <th>Price per Item</th>
                    <th>Subtotal</th>
                </tr>

                <?php foreach ($order['items'] as $item) { ?>

                    <tr>
                        <td>
                            <?php echo htmlspecialchars($item['name']); ?>
                        </td>

                        <td>
                            <?php echo $item['quantity']; ?>

                        </td>

                        <td>
                            &#8377;<?php echo number_format(
                                $item['price'], 2
                            ); ?>
                        </td>

                        <td>
                            &#8377;<?php echo number_format(
                                $item['price'] * $item['quantity'], 2
                            ); ?>
                        </td>
                    </tr>

                <?php } ?>
            </table>

            <h3>
                Total Amount:
                &#8377;<?php echo number_format($order['total'], 2); ?>
            </h3>

            <p>
                <strong>Current Status:</strong>
                <?php echo htmlspecialchars($order['status']); ?>
            </p>

            <form method="POST">

                <input type="hidden" name="order_id"
                       value="<?php echo $id; ?>">

                <label>Update Order Status:</label>

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

<?php } ?>

<a class="button" href="dashboard.php">
    Back to Dashboard
</a>

</div>

</body>
</html>
