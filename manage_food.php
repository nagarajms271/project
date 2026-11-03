
<?php
session_start();
include 'db.php';

if (empty($_SESSION['is_admin'])) {
    header("Location: admin_login.php");
    exit;
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST"
    && isset($_POST['delete_id'])) {

    $id = filter_input(
        INPUT_POST,
        'delete_id',
        FILTER_VALIDATE_INT
    );

    if ($id) {
        $stmt = mysqli_prepare(
            $conn,
            "DELETE FROM foods WHERE id = ?"
        );

        mysqli_stmt_bind_param($stmt, "i", $id);

        if (mysqli_stmt_execute($stmt)) {
            if (mysqli_stmt_affected_rows($stmt) > 0) {
                $message = "Food item deleted successfully.";
            } else {
                $message = "Food item was not found.";
            }
        } else {
            $message = "Unable to delete this food item. It may be linked to an existing order.";
        }

        mysqli_stmt_close($stmt);
    }

    $_SESSION['food_message'] = $message;

    header("Location: manage_food.php");
    exit;
}

if (isset($_SESSION['food_message'])) {
    $message = $_SESSION['food_message'];
    unset($_SESSION['food_message']);
}

$result = mysqli_query(
    $conn,
    "SELECT * FROM foods ORDER BY id DESC"
);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Food - Admin</title>
    <link rel="stylesheet" href="../style.css">

    <style>
        .manage-wrapper {
            max-width: 1100px;
            margin: 35px auto;
            padding: 0 15px;
        }

        .manage-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 3px 12px #ddd;
        }

        .manage-heading {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .manage-heading h2 {
            color: #e85d04;
            margin: 0;
        }

        .table-wrap {
            overflow-x: auto;
        }

        .food-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 650px;
        }

        .food-table th {
            background: #e85d04;
            color: white;
            padding: 13px;
            text-align: left;
        }

        .food-table td {
            padding: 12px;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
        }

        .food-table tr:hover td {
            background: #fff8f0;
        }

        .food-image {
            width: 75px;
            height: 65px;
            object-fit: cover;
            border-radius: 8px;
        }

        .price {
            color: #e85d04;
            font-weight: bold;
            white-space: nowrap;
        }

        .delete-btn {
            background: #c62828;
            white-space: nowrap;
        }

        .delete-btn:hover {
            background: #991b1b;
        }

        .message {
            background: #fff1e6;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 18px;
        }

        .empty-message {
            text-align: center;
            padding: 25px;
        }

        .back-link {
            display: inline-block;
            margin-top: 22px;
        }
    </style>
</head>

<body>

<header>
    <h1>Food Management</h1>
    <p>View and Manage Food Items</p>
</header>

<div class="manage-wrapper">

    <div class="manage-card">

        <div class="manage-heading">
            <h2>Manage Food Items</h2>
            <a class="button" href="add_food.php">
                + Add New Food
            </a>
        </div>

        <?php if ($message !== '') { ?>
            <div class="message">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php } ?>

        <div class="table-wrap">

        <table class="food-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Food Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>

            <?php if (mysqli_num_rows($result) > 0) { ?>

                <?php while ($food = mysqli_fetch_assoc($result)) { ?>

                    <tr>
                        <td>
                            <?php echo (int)$food['id']; ?>
                        </td>

                        <td>
                            <?php if (!empty($food['image'])) { ?>
                                <img
                                    class="food-image"
                                    src="images/<?php echo rawurlencode($food['image']); ?>"
                                    alt="<?php echo htmlspecialchars($food['name']); ?>"
                                >
                            <?php } else { ?>
                                No Image
                            <?php } ?>
                        </td>

                        <td>
                            <strong>
                                <?php echo htmlspecialchars($food['name']); ?>
                            </strong>
                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $food['description'] ?? ''
                            );
                            ?>
                        </td>

                        <td class="price">
                            &#8377;<?php echo number_format($food['price'], 2); ?>
                        </td>

                        <td>
                            <form method="POST"
                                  onsubmit="return confirm('Are you sure you want to delete this food item?');">

                                <input type="hidden" name="delete_id"
                                       value="<?php echo (int)$food['id']; ?>">

                                <button class="delete-btn" type="submit">
                                    Delete
                                </button>

                            </form>
                        </td>
                    </tr>

                <?php } ?>

            <?php } else { ?>

                <tr>
                    <td colspan="6" class="empty-message">
                        No food items available. Click "Add New Food" to get started.
                    </td>
                </tr>

            <?php } ?>

            </tbody>
        </table>

        </div>

        <a class="back-link" href="dashboard.php">
            ? Back to Dashboard
        </a>

    </div>

</div>

</body>
</html>
```