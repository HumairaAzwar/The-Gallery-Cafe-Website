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
    $table_number = $_POST['table_number'];
    $reservation_date = $_POST['reservation_date'];
    $reservation_time = $_POST['reservation_time'];
    $parking_needed = isset($_POST['parking_needed']) ? $_POST['parking_needed'] : 'No'; // Set default to 'No' if not set

    $sql = "INSERT INTO reservations (user_id, table_number, reservation_date, reservation_time, parking_needed)
            VALUES ('$user_id', '$table_number', '$reservation_date', '$reservation_time', '$parking_needed')";

    if ($conn->query($sql) === TRUE) {
        $success = true;
    } else {
        $error = "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Fetch table capacities
$tables = $conn->query("SELECT table_number, capacity FROM tables");

// Fetch parking availability
$parking_spots = $conn->query("SELECT COUNT(*) AS available_spots FROM parking WHERE status = 'available'");
$available_spots = $parking_spots ? $parking_spots->fetch_assoc()['available_spots'] : 0;

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Table Reservation - The Gallery Café</title>
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
        <form action="reservation.php" method="POST">
            <?php if ($_SERVER['REQUEST_METHOD'] == 'POST'): ?>
                <?php if ($success): ?>
                    <div class="message success-message">Reservation successfully added!</div>
                <?php elseif ($error): ?>
                    <div class="message error-message"><?php echo $error; ?></div>
                <?php endif; ?>
            <?php endif; ?>

            <input type="hidden" name="user_id" value="1"> <!-- Use session to dynamically set user ID -->

            <label for="table_number">Table Number:</label>
            <select id="table_number" name="table_number" required>
                <?php if ($tables): ?>
                    <?php while ($table = $tables->fetch_assoc()): ?>
                        <option value="<?= $table['table_number'] ?>">Table <?= $table['table_number'] ?> (Seats <?= $table['capacity'] ?>)</option>
                    <?php endwhile; ?>
                <?php else: ?>
                    <option value="">No tables available</option>
                <?php endif; ?>
            </select>

            <label for="reservation_date">Reservation Date:</label>
            <input type="date" id="reservation_date" name="reservation_date" required>

            <label for="reservation_time">Reservation Time:</label>
            <input type="time" id="reservation_time" name="reservation_time" required>

            <label for="parking_needed">Parking Needed:</label>
            <select id="parking_needed" name="parking_needed" required>
                <option value="Yes">Yes (<?= $available_spots ?> spots available)</option>
                <option value="No">No</option>
            </select>

            <button type="submit">Reserve Table</button>
        </form>
    </main>

    <footer>
        <p>&copy; 2024 The Gallery Café. All rights reserved.</p>
    </footer>
</body>
</html>
