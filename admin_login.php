<?php
session_start();

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (
        $username === 'admin' &&
        $password === 'naga12345'
    ) {
        session_regenerate_id(true);
        $_SESSION['is_admin'] = true;

        header("Location: dashboard.php");
        exit;
    } else {
        $message = "Invalid admin username or password.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<header><h1>Admin Login</h1></header>

<div class="container">

    <form method="POST">
        <input type="text" name="username"
               placeholder="Admin Username" required><br>

        <input type="password" name="password"
               placeholder="Admin Password" required><br>

        <button type="submit">Login</button>
    </form>

    <p><?php echo htmlspecialchars($message); ?></p>

</div>

</body>
</html>