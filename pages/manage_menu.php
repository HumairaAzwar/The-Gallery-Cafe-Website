<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gallery_cafe";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all menu items
$sql = "SELECT * FROM meals";
$result = $conn->query($sql);

// Handle actions (add item, delete item)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        switch ($action) {
            case 'add':
                // Validate and process item addition
                $meal_name = $_POST['meal_name'];
                $description = $_POST['description'];
                $cuisine_type = $_POST['cuisine_type'];
                $price = $_POST['price'];
                $image_url = $_POST['image_url'];

                $stmt = $conn->prepare("INSERT INTO meals (meal_name, description, cuisine_type, price, image_url) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("sssis", $meal_name, $description, $cuisine_type, $price, $image_url);

                if ($stmt->execute()) {
                    $success_message = "Meal added successfully!";
                } else {
                    $error_message = "Error adding meal: " . $conn->error;
                }

                $stmt->close();
                break;

            case 'delete':
                // Process item deletion
                if (isset($_POST['meal_id'])) {
                    $meal_id = $_POST['meal_id'];
                    $sql_delete = "DELETE FROM meals WHERE meal_id = $meal_id";

                    if ($conn->query($sql_delete) === TRUE) {
                        $success_message = "Meal deleted successfully!";
                    } else {
                        $error_message = "Error deleting meal: " . $conn->error;
                    }
                }
                break;

            default:
                $error_message = "Invalid action!";
                break;
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Menu - The Gallery Café</title>
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
            <li><a href="admin_dashboard.php">Dashboard</a></li>
            <li><a href="manage_users.php">Manage Users</a></li>
            <li><a href="manage_menu.php">Manage Menu</a></li>
            <li><a href="process_reservation.php">Manage Reservations</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <main>
        <div class="container">
            <section id="menu-actions">
                <h2>Add New Meal</h2>
                <?php if (isset($error_message)): ?>
                    <div class="error-message"><?php echo $error_message; ?></div>
                <?php elseif (isset($success_message)): ?>
                    <div class="success-message"><?php echo $success_message; ?></div>
                <?php endif; ?>

                <form action="manage_menu.php" method="POST">
                    <input type="hidden" name="action" value="add">
                    <label for="meal_name">Meal Name:</label>
                    <input type="text" id="meal_name" name="meal_name" required>
                    
                    <label for="description">Description:</label>
                    <textarea id="description" name="description" rows="4" required></textarea>
                    
                    <label for="cuisine_type">Cuisine Type:</label>
                    <input type="text" id="cuisine_type" name="cuisine_type" required>
                    
                    <label for="price">Price:</label>
                    <input type="number" id="price" name="price" min="0" step="0.01" required>

                    <label for="image_url">Image URL:</label>
                    <input type="text" id="image_url" name="image_url" required>

                    <button type="submit">Add Meal</button>
                </form>
            </section>

            <section id="menu-list">
                <h2>Meals</h2>

                <?php if ($result && $result->num_rows > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Meal ID</th>
                                <th>Meal Name</th>
                                <th>Description</th>
                                <th>Cuisine Type</th>
                                <th>Price</th>
                                <th>Image URL</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $row['meal_id']; ?></td>
                                    <td><?php echo $row['meal_name']; ?></td>
                                    <td><?php echo $row['description']; ?></td>
                                    <td><?php echo $row['cuisine_type']; ?></td>
                                    <td>$<?php echo number_format($row['price'], 2); ?></td>
                                    <td><?php echo $row['image_url']; ?></td>
                                    <td>
                                        <form action="manage_menu.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this meal?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="meal_id" value="<?php echo $row['meal_id']; ?>">
                                            <button type="submit">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No meals found.</p>
                <?php endif; ?>
            </section>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> The Gallery Café. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
