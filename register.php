<?php
include 'db.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = mysqli_prepare(
        $conn,
        "INSERT INTO users (name, email, password) VALUES (?, ?, ?)"
    );

    mysqli_stmt_bind_param($stmt, "sss", $name, $email, $password);

    if (mysqli_stmt_execute($stmt)) {
        $message = "Registration successful! Please login.";
    } else {
        $message = "Registration failed. This email may already exist.";
    }

    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Customer Registration</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header><h1>Customer Registration</h1></header>

<div class="container">
    <form method="POST">
      <input type="text" name="name" placeholder="Enter Name" required>
      <br>
        <input type="email" name="email" placeholder="Enter Email" required>
        <br>
        <input type="password" name="password"
               placeholder="Enter Password" required minlength="8">
        <br>

        <button type="submit">Register</button>
    </form>

    <p><?php echo htmlspecialchars($message); ?></p>

    <a href="login.php">Already registered? Login here</a>
</div>

</body>
</html>