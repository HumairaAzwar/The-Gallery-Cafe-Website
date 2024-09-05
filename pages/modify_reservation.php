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

// Initialize variables
$reservation_id = isset($_GET['id']) ? $_GET['id'] : null;
$reservation = null;
$error_message = "";

// Fetch reservation details
if ($reservation_id) {
    $sql = "SELECT * FROM reservations WHERE reservation_id = $reservation_id";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $reservation = $result->fetch_assoc();
    } else {
        $error_message = "Reservation not found!";
    }
}

// Handle form submission for modification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_table_number = $_POST['table_number'];
    $new_reservation_date = $_POST['reservation_date'];
    $new_reservation_time = $_POST['reservation_time'];
    $new_parking_needed = isset($_POST['parking_needed']) ? $_POST['parking_needed'] : 'No';

    $sql_update = "UPDATE reservations SET 
                   table_number = '$new_table_number', 
                   reservation_date = '$new_reservation_date', 
                   reservation_time = '$new_reservation_time', 
                   parking_needed = '$new_parking_needed'
                   WHERE reservation_id = $reservation_id";

    if ($conn->query($sql_update) === TRUE) {
        $success_message = "Reservation details updated successfully!";
        // Redirect back to process_reservations.php or any other page
        header("Location: process_reservations.php");
        exit();
    } else {
        $error_message = "Error updating record: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modify Reservation - The Gallery Café</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            background-color: #DDBA86;
            font-family: Arial, sans-serif;
        }

        header {
            text-align: center;
            padding: 20px;
        }

        nav ul {
            display: flex;
            justify-content: center;
            list-style-type: none;
            padding: 0;
        }

        nav ul li {
            margin: 0 10px;
        }

        nav ul li a {
            text-decoration: none;
            color: white;
        }

        nav ul li a:hover {
            text-decoration: underline;
        }

        main {
            display: flex;
            justify-content: center;
            align-items: center;
            height: calc(100vh - 100px);
        }

        form {
            position: relative;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            box-sizing: border-box;
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
            background-color: #333;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #555;
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
    text-align: center;
    padding: 20px;
    background-color: #333;
    color: white;
    position: absolute;
    width: 100%;
    bottom: -308px;
    left: -11px;
        }
    </style>
</head>
<body>
<header>
        <h1>Modify Reservation</h1>
        <nav>
            <ul>
                <li><a href="index.html">Home</a></li>
                <li><a href="menu.php">Menu</a></li>
                <li><a href="events.html">Events</a></li>
                <li><a href="promotions.html">Promotions</a></li>
                <li><a href="contact.html">Contact</a></li>
                <li><a href="login.html">Login</a></li>
            </ul>
        </nav>
    </header>

<main>
        <form action="modify_reservation.php?id=<?php echo $reservation_id; ?>" method="POST">
            <h2>Current Reservation Details</h2>
            <?php if ($reservation): ?>
                <label for="table_number">Table Number:</label>
                <select id="table_number" name="table_number" required>
                    <!-- Populate with table numbers from your database or static options -->
                    <option value="1" <?php if ($reservation['table_number'] == '1') echo 'selected'; ?>>Table 1</option>
                    <option value="2" <?php if ($reservation['table_number'] == '2') echo 'selected'; ?>>Table 2</option>
                    <!-- Add more options as needed -->
                </select>

                <label for="reservation_date">Reservation Date:</label>
                <input type="date" id="reservation_date" name="reservation_date" value="<?php echo $reservation['reservation_date']; ?>" required>

                <label for="reservation_time">Reservation Time:</label>
                <input type="time" id="reservation_time" name="reservation_time" value="<?php echo $reservation['reservation_time']; ?>" required>

                <label for="parking_needed">Parking Needed:</label>
                <select id="parking_needed" name="parking_needed" required>
                    <option value="Yes" <?php if ($reservation['parking_needed'] == 'Yes') echo 'selected'; ?>>Yes</option>
                    <option value="No" <?php if ($reservation['parking_needed'] == 'No') echo 'selected'; ?>>No</option>
                </select>

                <button type="submit">Update Reservation</button>
            <?php else: ?>
                <p>No reservation found with ID: <?php echo $reservation_id; ?></p>
            <?php endif; ?>

            <?php if (isset($success_message)): ?>
                <div class="success-message"><?php echo $success_message; ?></div>
            <?php elseif (isset($error_message)): ?>
                <div class="error-message"><?php echo $error_message; ?></div>
            <?php endif; ?>
        </form>
    </main>

    <footer>
        <p>&copy; 2024 The Gallery Café. All rights reserved.</p>
    </footer>
</body>
</html>
