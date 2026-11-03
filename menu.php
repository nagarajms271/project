<?php
session_start();
include 'db.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Food Menu</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h1>Our Food Menu</h1>
</header>

<nav>
    <a href="index.php">Home</a>
    <a href="menu.php">Menu</a>
    <a href="cart.php">Cart</a>
    <a href="my_orders.php">My Orders</a>
    <a href="logout.php">Logout</a>
</nav>

<div class="container">

<?php
if (isset($_SESSION['user_name'])) {
    echo "<h2>Welcome, " .
         htmlspecialchars($_SESSION['user_name']) . "!</h2>";
} else {
    echo "<p><a href='login.php'>Login to place an order</a></p>";
}

$result = mysqli_query($conn, "SELECT * FROM foods ORDER BY id DESC");

while ($food = mysqli_fetch_assoc($result)) {
?>

    <div class="card">

        <?php if (!empty($food['image'])) { ?>
            <img src="images/<?php echo rawurlencode($food['image']); ?>"
                 alt="<?php echo htmlspecialchars($food['name']); ?>">
        <?php } ?>

        <h3><?php echo htmlspecialchars($food['name']); ?></h3>

        <p><?php echo htmlspecialchars($food['description']); ?></p>

        <h3>&#8377;<?php echo number_format($food['price'], 2); ?></h3>

        <?php if (isset($_SESSION['user_id'])) { ?>

            <form method="POST" action="cart.php">
                <input type="hidden" name="food_id"
                       value="<?php echo $food['id']; ?>">

                <input type="number" name="quantity"
                       value="1" min="1" max="20" required>

                <button type="submit" name="add_to_cart">
                    Add to Cart
                </button>
            </form>

        <?php } else { ?>

            <a class="button" href="login.php">Login to Order</a>

        <?php } ?>

    </div>

<?php } ?>

</div>

</body>
</html>