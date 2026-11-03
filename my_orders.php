<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = mysqli_prepare(
    $conn,
    "SELECT id, total_amount, order_date, status
     FROM orders
     WHERE user_id = ?
     ORDER BY id DESC"
);

mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $id, $amount, $date, $status);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Orders</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header><h1>My Orders</h1></header>

<div class="container">

<?php
$found = false;

while (mysqli_stmt_fetch($stmt)) {
    $found = true;
?>

    <div class="card">
        <h3>Order #<?php echo $id; ?></h3>
        <p>Total: &#8377;<?php echo number_format($amount, 2); ?></p>
        <p>Date: <?php echo htmlspecialchars($date); ?></p>
        <p>Status: <?php echo htmlspecialchars($status); ?></p>
    </div>

<?php
}

if (!$found) {
    echo "<p>You have not placed any orders yet.</p>";
}

mysqli_stmt_close($stmt);
?>

<br>
<a href="menu.php">Back to Food Menu</a>

</div>

</body>
</html>