
<?php
session_start();
include 'db.php';

if (empty($_SESSION['is_admin'])) {
    header("Location: admin_login.php");
    exit;
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name'] ?? '');
    $price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);
    $description = trim($_POST['description'] ?? '');
    $image = trim($_POST['image'] ?? '');

    if ($name !== '' && $price !== false
        && $price !== null && $price > 0) {

        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO foods (name, price, description, image)
             VALUES (?, ?, ?, ?)"
        );

        mysqli_stmt_bind_param(
            $stmt, "sdss",
            $name, $price, $description, $image
        );

        if (mysqli_stmt_execute($stmt)) {
            $message = "Food item added successfully!";
        } else {
            $message = "Unable to add food item.";
        }

        mysqli_stmt_close($stmt);
    } else {
        $message = "Please enter a valid food name and price.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Food - Admin</title>
    <link rel="stylesheet" href="../style.css">

    <style>
        .admin-wrapper {
            max-width: 650px;
            margin: 35px auto;
            padding: 0 15px;
        }

        .admin-card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 3px 12px #ddd;
            text-align: left;
        }

        .admin-card h2 {
            color: #e85d04;
            margin-top: 0;
            text-align: center;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .form-group input,
        .form-group textarea {
            box-sizing: border-box;
            width: 100%;
            max-width: 100%;
            padding: 12px;
            margin: 0;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 15px;
        }

        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }

        .admin-btn {
            width: 100%;
            padding: 13px;
            font-size: 16px;
        }

        .message {
            background: #fff1e6;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 15px;
            text-align: center;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>

<body>

<header>
    <h1>Food Management</h1>
    <p>Add a New Food Item</p>
</header>

<div class="admin-wrapper">

    <div class="admin-card">

        <h2>Add Food</h2>

        <?php if ($message !== '') { ?>
            <div class="message">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php } ?>

        <form method="POST">

            <div class="form-group">
                <label for="name">Food Name</label>
                <input type="text" id="name" name="name"
                       placeholder="Enter food name"
                       maxlength="100" required>
            </div>

            <div class="form-group">
                <label for="price">Price (?)</label>
                <input type="number" id="price" name="price"
                       placeholder="Enter price"
                       min="0.01" step="0.01" required>
            </div>

            <div class="form-group">
                <label for="description">Food Description</label>
                <textarea id="description" name="description"
                          placeholder="Describe the food"></textarea>
            </div>

            <div class="form-group">
                <label for="image">Image Filename</label>
                <input type="text" id="image" name="image"
                       placeholder="Example: biryani.jpg"
                       maxlength="255">
                <small>Place the image inside your project's images folder.</small>
            </div>

            <button class="admin-btn" type="submit">
                + Add Food Item
            </button>

        </form>

        <a class="back-link" href="dashboard.php">
            ? Back to Dashboard
        </a>

    </div>

</div>

</body>
</html>
```