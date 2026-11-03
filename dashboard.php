<?php
session_start();

if (empty($_SESSION['is_admin'])) {
    header("Location: admin_login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<header><h1>Admin Dashboard</h1></header>

<div class="container">

    <h2>Welcome, Administrator!</h2>

    <p><a class="button" href="add_food.php">Add Food</a></p>

    <p><a class="button" href="manage_food.php">Manage Food</a></p>

    <p><a class="button" href="view_orders.php">View Orders</a></p>

    <p><a href="admin_login.php">Admin Login Page</a></p>

</div>

</body>
</html>