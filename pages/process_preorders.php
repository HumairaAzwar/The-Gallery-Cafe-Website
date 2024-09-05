<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gallery_cafe";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Update order status or details if a POST request is made
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $order_id = $_POST['order_id'];
    $action = $_POST['action'];

    if ($action == 'confirm') {
        $status = 'Confirmed';
        $update_sql = "UPDATE preorders SET status='$status' WHERE order_id='$order_id'";
        if (!$conn->query($update_sql)) {
            echo "Error updating record: " . $conn->error;
        }
    } elseif ($action == 'cancel') {
        $status = 'Cancelled';
        $update_sql = "UPDATE preorders SET status='$status' WHERE order_id='$order_id'";
        if (!$conn->query($update_sql)) {
            echo "Error updating record: " . $conn->error;
        }
    } elseif ($action == 'modify') {
        $meal_id = $_POST['meal_id'];
        $quantity = $_POST['quantity'];
        $order_date = $_POST['order_date'];
        $status = 'Modified';

        $update_sql = "UPDATE preorders SET meal_id='$meal_id', quantity='$quantity', order_date='$order_date', status='$status' WHERE order_id='$order_id'";
        if (!$conn->query($update_sql)) {
            echo "Error updating record: " . $conn->error;
        }
    }
}

// Fetch pre-orders with meal names
$sql = "SELECT preorders.order_id, preorders.user_id, meals.meal_name, preorders.meal_id, preorders.quantity, preorders.order_date, preorders.status
        FROM preorders
        JOIN meals ON preorders.meal_id = meals.meal_id";
$preorders = $conn->query($sql);

// Fetch meals for the form dropdown
$meals_result = $conn->query("SELECT meal_id, meal_name FROM meals");
$meals = [];
if ($meals_result && $meals_result->num_rows > 0) {
    while ($meal = $meals_result->fetch_assoc()) {
        $meals[] = $meal;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Process Pre-orders - The Gallery Café</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            background-color: #DDBA86;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        header {
            background-image: url("../images/header1.jpg");
            background-size: cover;
            background-position: center;
            height: 74px;
            width: 100%;
            padding: 15px 0;
            text-align: center;
            color: white;
        }

        nav {
            background-color: #8B4513; /* Wood-like color */
            padding: 10px 0;
            text-align: center;
        }

        nav ul {
            list-style-type: none;
            margin: 0;
            padding: 0;
        }

        nav ul li {
            display: inline;
            margin: 0 15px;
        }

        nav ul li a {
            color: white;
            text-decoration: none;
            font-size: 1.2em;
            padding: 5px 10px;
        }

        nav ul li a:hover {
            background-color: #D2B48C; /* Highlight color on hover */
            color: #333;
            border-radius: 5px;
        }

        main {
            display: flex;
            background-color: #FFF7E1;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            padding: 20px;
            min-height: calc(100vh - 144px - 40px); /* Adjust height to fit content */
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background-color: white;
        }

        table, th, td {
            border: none;
        }

        th, td {
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #8B4513;
            color: white;
        }

        form {
            display: inline;
            background-color: #FFF7E1;
        }

        button {
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            color: white;
        }

        .confirm-button {
            background-color: #4CAF50;
        }

        .cancel-button {
            background-color: red;
        }

        .modify-button {
            background-color: blue;
        }

        button:hover {
            opacity: 0.8;
        }

        .edit-form {
            margin-bottom: 10px;
        }

        .hidden-form {
            display: none;
        }

        footer {
            background-color: #8B4513;
            color: white;
            text-align: center;
            padding: 10px 0;
            width: 100%;
            position: fixed;
            bottom: 0;
        }

        footer p {
            margin: 0;
            color: white;
        }
    </style>
</head>

<body>
    <header></header>
    <nav>
        <ul>
            <li><a href="staff_dashboard.php">Dashboard</a></li>
            <li><a href="process_reservation.php">Process Reservations</a></li>
            <li><a href="process_preorders.php">Process Pre-Orders</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <main>
        <h1>Process Pre-orders</h1>
        <?php if ($preorders && $preorders->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Order ID</th>
                    <th>User ID</th>
                    <th>Meal Name</th>
                    <th>Quantity</th>
                    <th>Order Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                <?php while ($preorder = $preorders->fetch_assoc()): ?>
                    <tr>
                        <td><?= $preorder['order_id'] ?></td>
                        <td><?= $preorder['user_id'] ?></td>
                        <td><?= $preorder['meal_name'] ?></td>
                        <td><?= $preorder['quantity'] ?></td>
                        <td><?= $preorder['order_date'] ?></td>
                        <td><?= $preorder['status'] ?></td>
                        <td>
                            <form class="edit-form" action="process_preorders.php" method="POST">
                                <input type="hidden" name="order_id" value="<?= $preorder['order_id'] ?>">
                                <button class="confirm-button" type="submit" name="action" value="confirm">Confirm</button>
                            </form>
                            <form class="edit-form" action="process_preorders.php" method="POST">
                                <input type="hidden" name="order_id" value="<?= $preorder['order_id'] ?>">
                                <button class="cancel-button" type="submit" name="action" value="cancel">Cancel</button>
                            </form>
                            <button class="modify-button" onclick="document.getElementById('modify-form-<?= $preorder['order_id'] ?>').classList.toggle('hidden-form')">Modify</button>
                            <form id="modify-form-<?= $preorder['order_id'] ?>" class="hidden-form" action="process_preorders.php" method="POST">
                                <input type="hidden" name="order_id" value="<?= $preorder['order_id'] ?>">
                                <input type="number" name="quantity" value="<?= $preorder['quantity'] ?>" required>
                                <input type="date" name="order_date" value="<?= $preorder['order_date'] ?>" required>
                                <select name="meal_id" required>
                                    <?php foreach ($meals as $meal): ?>
                                        <option value="<?= $meal['meal_id'] ?>" <?= $meal['meal_id'] == $preorder['meal_id'] ? 'selected' : '' ?>>
                                            <?= $meal['meal_name'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <button class="modify-button" type="submit" name="action" value="modify">Update</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>No pre-orders available.</p>
        <?php endif; ?>
    </main>


    <footer>
        <p>&copy; 2024 The Gallery Café. All rights reserved.</p>
    </footer>
</body>
</html>
