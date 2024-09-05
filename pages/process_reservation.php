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

// available table numbers
$table_numbers_result = $conn->query("SELECT table_number FROM tables");
$table_numbers = [];
if ($table_numbers_result && $table_numbers_result->num_rows > 0) {
    while ($row = $table_numbers_result->fetch_assoc()) {
        $table_numbers[] = $row['table_number'];
    }
}

// confirm, modify, cancel, update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action']) && isset($_POST['reservation_id'])) {
        $action = $_POST['action'];
        $reservation_id = $_POST['reservation_id'];

        switch ($action) {
            case 'confirm':
                $sql = "UPDATE reservations SET status = 'Confirmed' WHERE reservation_id = $reservation_id";
                if ($conn->query($sql) === TRUE) {
                    $success_message = "Reservation successfully confirmed!";
                } else {
                    $error_message = "Error updating record: " . $conn->error;
                }
                break;
            case 'modify':
                // Display modification form
                $sql_select = "SELECT * FROM reservations WHERE reservation_id = $reservation_id";
                $result = $conn->query($sql_select);
                if ($result && $result->num_rows > 0) {
                    $reservation = $result->fetch_assoc();
                    $modify_mode = true; // Flag to indicate modification mode
                } else {
                    $error_message = "Reservation not found!";
                }
                break;
            case 'cancel':
                $sql = "UPDATE reservations SET status = 'Cancelled' WHERE reservation_id = $reservation_id";
                if ($conn->query($sql) === TRUE) {
                    $success_message = "Reservation successfully cancelled!";
                } else {
                    $error_message = "Error updating record: " . $conn->error;
                }
                break;
            case 'update':
                // Handle form submission to update reservation details
                $new_table_number = $_POST['table_number'];
                $new_reservation_date = $_POST['reservation_date'];
                $new_reservation_time = $_POST['reservation_time'];
                $new_parking_needed = isset($_POST['parking_needed']) ? $_POST['parking_needed'] : 'No';

                $sql_update = "UPDATE reservations SET 
                               table_number = '$new_table_number', 
                               reservation_date = '$new_reservation_date', 
                               reservation_time = '$new_reservation_time', 
                               parking_needed = '$new_parking_needed',
                               status = 'Modified'
                               WHERE reservation_id = $reservation_id";

                if ($conn->query($sql_update) === TRUE) {
                    $success_message = "Reservation details updated successfully!";
                    $modify_mode = false; // Exit modify mode after update
                } else {
                    $error_message = "Error updating record: " . $conn->error;
                }
                break;
            default:
                // Handle invalid action
                $error_message = "Invalid action!";
                break;
        }
    }
}

// Fetch reservations
$reservations = $conn->query("SELECT * FROM reservations");

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Process Reservations - The Gallery Café</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
        }

        header {
            background-image: url("../images/header1.jpg");
            background-size: cover;
            background-position: center;
            height: 74px;
            width: 100%;
        }

        nav, footer {
            background-color: #8B4513; /* Wood-like color */
            padding: 10px 0;
            position: relative;
            top: 0;
            width: 100%;
        }

        nav ul, footer p {
            list-style-type: none;
            margin: 0;
            padding: 0;
            text-align: center;
            color: white;
        }

        nav ul li {
            display: inline;
            margin: 0 15px;
        }

        nav ul li a {
            color: white;
            text-decoration: none;
            font-size: 1.2em;
            padding: 5px 10px; /* Padding for better hover effect */
        }

        nav ul li a:hover {
            background-color: #D2B48C; /* Highlight color on hover */
            color: #333; /* Text color change on hover */
            border-radius: 5px; /* Rounded corners on hover */
        }

        main {
            padding: 20px;
            max-width: 1200px;
            margin: 20px auto;
            background-color: #FFF7E1;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        section {
            margin-bottom: 20px;
			background-color: #FFF7E1;
        }

        .error-message {
            background-color: #f44336; /* Red background for error */
            color: white;
            padding: 10px;
            border-radius: 3px;
            margin-bottom: 10px;
        }

        .success-message {
            background-color: #4CAF50; /* Green background for success */
            color: white;
            padding: 10px;
            border-radius: 3px;
            margin-bottom: 10px;
        }

        form {
            display: grid;
            grid-template-columns: 1fr 3fr;
            gap: 10px;
            align-items: center;
            padding: 20px;
            border-radius: 5px;
        }

        form label {
            font-weight: bold;
            margin-bottom: 10px;
        }

        form input, form select, form textarea {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 3px;
        }

        form button {
            padding: 10px 20px;
            background-color: #4CAF50; /* Green button */
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 3px;
        }

        form button:hover {
            background-color: #45a049; /* Darker green on hover */
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff; /* White table background */
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        table th, table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        table th {
            background-color: #8B4513; /* Wood-like color */
            color: white;
        }

        table td button {
            padding: 6px 12px;
            background-color: #f44336; /* Red button */
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 3px;
        }

        table td button:hover {
            background-color: #da190b; /* Darker red on hover */
        }
    </style>
</head>
<body>
    <header></header>
    <nav>
        <ul>
            <li><a href="staff.html">Dashboard</a></li>
            <li><a href="process_reservation.php">Process Reservations</a></li>
            <li><a href="process_preorders.php">Process Pre-Orders</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <main>
        <?php if (isset($success_message)): ?>
            <div class="success-message"><?php echo $success_message; ?></div>
        <?php elseif (isset($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <table>
            <tr>
                <th>Reservation ID</th>
                <th>User ID</th>
                <th>Table Number</th>
                <th>Date</th>
                <th>Time</th>
                <th>Parking Needed</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php if ($reservations && $reservations->num_rows > 0): ?>
                <?php while ($row = $reservations->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['reservation_id']; ?></td>
                        <td><?php echo $row['user_id']; ?></td>
                        <td><?php echo $row['table_number']; ?></td>
                        <td><?php echo $row['reservation_date']; ?></td>
                        <td><?php echo $row['reservation_time']; ?></td>
                        <td><?php echo $row['parking_needed']; ?></td>
                        <td><?php echo $row['status']; ?></td>
                        <td>
                            <?php if (isset($modify_mode) && $modify_mode && $row['reservation_id'] == $reservation_id): ?>
                                <!-- Modification form -->
                                <form action="process_reservation.php" method="POST">
                                    <input type="hidden" name="reservation_id" value="<?php echo $reservation_id; ?>">
                                    <input type="hidden" name="action" value="update">
                                    <label for="table_number">Table Number:</label>
                                    <select id="table_number" name="table_number" required>
                                        <?php foreach ($table_numbers as $table_number): ?>
                                            <option value="<?php echo $table_number; ?>" <?php if ($row['table_number'] == $table_number) echo 'selected'; ?>>
                                                Table <?php echo $table_number; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>

                                    <label for="reservation_date">Reservation Date:</label>
                                    <input type="date" id="reservation_date" name="reservation_date" value="<?php echo $row['reservation_date']; ?>" required>

                                    <label for="reservation_time">Reservation Time:</label>
                                    <input type="time" id="reservation_time" name="reservation_time" value="<?php echo $row['reservation_time']; ?>" required>

                                    <label for="parking_needed">Parking Needed:</label>
                                    <select id="parking_needed" name="parking_needed" required>
                                        <option value="Yes" <?php if ($row['parking_needed'] == 'Yes') echo 'selected'; ?>>Yes</option>
                                        <option value="No" <?php if ($row['parking_needed'] == 'No') echo 'selected'; ?>>No</option>
                                    </select>

                                    <button type="submit">Update Reservation</button>
                                </form>
                            <?php else: ?>
                                <!-- Action dropdown -->
                                <form action="process_reservation.php" method="POST">
                                    <input type="hidden" name="reservation_id" value="<?php echo $row['reservation_id']; ?>">
                                    <select name="action">
                                        <option value="confirm">Confirm</option>
                                        <option value="modify">Modify</option>
                                        <option value="cancel">Cancel</option>
                                    </select>
                                    <button type="submit">Apply</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8">No reservations found.</td>
                </tr>
            <?php endif; ?>
        </table>
    </main>

    <footer>
        <p>&copy; 2024 The Gallery Café. All rights reserved.</p>
    </footer>
</body>
</html>
