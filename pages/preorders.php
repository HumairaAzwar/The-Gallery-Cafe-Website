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

$success = false;
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id']; // Normally, you would get this from the session
    $meal_id = $_POST['meal_id'];
    $quantity = $_POST['quantity'];
    $order_date = date('Y-m-d'); // Get the current date
    $status = 'Pending';

    $sql = "INSERT INTO preorders (user_id, meal_id, quantity, order_date, status)
            VALUES ('$user_id', '$meal_id', '$quantity', '$order_date', '$status')";

    if ($conn->query($sql) === TRUE) {
        $success = true;
    } else {
        $error = "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Fetch meals
$meals = $conn->query("SELECT meal_id, meal_name FROM meals");

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pre-order Meal - The Gallery Café</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            background-color: #f4f4f4;
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
            justify-content: center;
            align-items: center;
            height: calc(100vh - 74px - 40px);
            padding: 20px;
        }

        form {
            background-color: #FFF7E1;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            box-sizing: border-box;
            position: relative;
        }

        label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
        }

        input, select {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #8B4513;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #D2B48C;
            color: #333;
        }

        .message {
            position: absolute;
            top: -50px;
            left: 0;
            right: 0;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            z-index: 10;
        }

        .success-message {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
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
            <li><a href="index.html">Home</a></li>
            <li><a href="menu.php">View Menu</a></li>
            <li><a href="reservation.php">Make Reservation</a></li>
            <li><a href="preorders.php">Place Pre-Order</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <main>
        <form action="preorders.php" method="POST">
            <?php if ($_SERVER['REQUEST_METHOD'] == 'POST'): ?>
                <?php if ($success): ?>
                    <div class="message success-message">Meal successfully pre-ordered!</div>
                <?php elseif ($error): ?>
                    <div class="message error-message"><?php echo $error; ?></div>
                <?php endif; ?>
            <?php endif; ?>

            <input type="hidden" name="user_id" value="1"> <!-- Use session to dynamically set user ID -->

            <label for="meal_id">Meal:</label>
            <select id="meal_id" name="meal_id" required>
                <?php if ($meals && $meals->num_rows > 0): ?>
                    <?php while ($meal = $meals->fetch_assoc()): ?>
                        <option value="<?= $meal['meal_id'] ?>"><?= $meal['meal_name'] ?></option>
                    <?php endwhile; ?>
                <?php else: ?>
                    <option value="">No meals available</option>
                <?php endif; ?>
            </select>

            <label for="quantity">Quantity:</label>
            <input type="number" id="quantity" name="quantity" min="1" required>

            <button type="submit">Pre-order Meal</button>
        </form>
    </main>

    <footer>
        <p>&copy; 2024 The Gallery Café. All rights reserved.</p>
    </footer>
</body>
</html>
