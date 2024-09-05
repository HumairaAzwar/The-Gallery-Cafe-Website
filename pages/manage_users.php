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

// Fetch all users
$sql = "SELECT * FROM users";
$result = $conn->query($sql);

// Handle actions (create user, delete user)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        switch ($action) {
            case 'create':
                // Validate and process user creation
                $username = $_POST['username'];
                $password = $_POST['password'];
                $email = $_POST['email'];
                $role = $_POST['role']; // Assuming role is 'admin', 'staff', or 'customer'
                $stmt = $conn->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $username, $password, $email, $role);

                if ($stmt->execute()) {
                    $success_message = "User created successfully!";
                } else {
                    $error_message = "Error creating user: " . $conn->error;
                }

                $stmt->close();
                break;

            case 'delete':
                // Process user deletion
                if (isset($_POST['user_id'])) {
                    $user_id = $_POST['user_id'];
                    $sql_delete = "DELETE FROM users WHERE user_id = $user_id";

                    if ($conn->query($sql_delete) === TRUE) {
                        $success_message = "User deleted successfully!";
                    } else {
                        $error_message = "Error deleting user: " . $conn->error;
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
    <title>Manage Users - The Gallery Café</title>
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
			background-color: #FFF7E1;
            margin-bottom: 20px;
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

        form input, form select {
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
			border-color: #333;
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
            border: thin;
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
            <li><a href="admin.html">Dashboard</a></li>
            <li><a href="manage_users.php">Manage Users</a></li>
            <li><a href="manage_menu.php">Manage Menu</a></li>
            <li><a href="process_reservation.php">Manage Reservations</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    

    <main>
        <div class="container">
            <section id="user-actions">
                <h2>Create New User</h2>
                <?php if (isset($error_message)): ?>
                    <div class="error-message"><?php echo $error_message; ?></div>
                <?php elseif (isset($success_message)): ?>
                    <div class="success-message"><?php echo $success_message; ?></div>
                <?php endif; ?>

                <form action="manage_users.php" method="POST">
                    <input type="hidden" name="action" value="create">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
                    
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                    
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                    
                    <label for="role">Role:</label>
                    <select id="role" name="role">
                        <option value="admin">Admin</option>
                        <option value="staff">Staff</option>
                        <option value="customer">Customer</option>
                    </select>

                    <button type="submit">Create User</button>
                </form>
            </section>

            <section id="user-list">
                <h2>Existing Users</h2>

                <?php if ($result && $result->num_rows > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>User ID</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $row['user_id']; ?></td>
                                    <td><?php echo $row['username']; ?></td>
                                    <td><?php echo $row['email']; ?></td>
                                    <td><?php echo $row['role']; ?></td>
                                    <td>
                                        <form action="manage_users.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="user_id" value="<?php echo $row['user_id']; ?>">
                                            <button type="submit">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No users found.</p>
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
