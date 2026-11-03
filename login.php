<?php
session_start();
include 'db.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = mysqli_prepare(
        $conn,
        "SELECT id, name, password FROM users WHERE email = ?"
    );

    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $id, $name, $hashed_password);

    if (mysqli_stmt_fetch($stmt) &&
        password_verify($password, $hashed_password)) {

        session_regenerate_id(true);

        $_SESSION['user_id'] = $id;
        $_SESSION['user_name'] = $name;

        header("Location: menu.php");
        exit;
    } else {
        $message = "Invalid email or password.";
    }

    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Customer Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header><h1>Customer Login</h1></header>

<div class="container">
    <form method="POST">
        <input type="email" name="email" placeholder="Enter Email" required><br>

        <input type="password" name="password"
               placeholder="Enter Password" required><br>

        <button type="submit">Login</button>
    </form>

    <p><?php echo htmlspecialchars($message); ?></p>
    <a href="register.php">Create a new account</a>
</div>

</body>
</html>