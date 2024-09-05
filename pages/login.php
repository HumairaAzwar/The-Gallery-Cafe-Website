<?php
session_start();

// error message
$error_message = '';

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gallery_cafe";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare and execute query
    $sql = "SELECT * FROM users WHERE username=? AND password=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $row['role'];

        // Redirect to respective dashboard based on user type
        if ($row['role'] == 'Admin') {
            header("Location: admin.html");
            exit();
        } elseif ($row['role'] == 'Staff') {
            header("Location: staff.html");
            exit();
        } elseif ($row['role'] == 'Customer') {
            header("Location: customer.html");
            exit();
        } else {
            $error_message = "Unknown user type.";
        }
    } else {
        $error_message = "Login failed. Invalid username or password.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In - The Gallery Café</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            background-color: #f4f4f4;
        }

        header {
            background-image: url("../images/header1.jpg");
            background-size: cover;
            background-position: center;
            height: 74px;
            width: 100%;
        }

        nav, footer {
            background-color: #8B4513;
            padding: 10px 0;
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
            padding: 5px 10px;
        }

        nav ul li a:hover {
            background-color: #D2B48C;
            color: #333;
            border-radius: 5px;
        }

        main {
            display: flex;
            justify-content: center;
            align-items: center;
            height: calc(100vh - 150px); /* Adjust height to center form */
            background-color: #f4f4f4;
        }

        #login-form {
            background-color: #FFF7E1;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
            text-align: center;
        }

        #login-form h2 {
            margin-top: 2;
        }

        #login-form p {
            margin: 12px 0;
        }

        #login-form label {
            display: block;
            margin-bottom: 1px;
            text-align: left;
        }

        #login-form input {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        #login-form button {
            background-color: #8B4513;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1em;
        }

        #login-form button:hover {
            background-color: #D2B48C;
            color: #333;
        }

        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 13px;
            margin: 13px 0;
            border-radius: 5px;
            font-size: 1em;
            display: inline-block;
        }

        footer p {
            margin: 0;
            color: white;
            text-align: center;
        }
    </style>
</head>
<body>
    <header></header>
    <nav>
        <ul>
            <li><a href="index.html">Home</a></li>
            <li><a href="menu.php">Menu</a></li>
            <li><a href="events.html">Events</a></li>
            <li><a href="promotions.html">Promotions</a></li>
            <li><a href="about.html">About</a></li>
            <li><a href="contact.html">Contact</a></li>
            <li><a href="signup.html">Sign Up</a></li>
        </ul>
    </nav>

    <main>
        <section id="login-form">
            <h2>Log In</h2>

            <!-- Display error message if it exists -->
            <?php if (!empty($error_message)): ?>
                <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <p>
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
                </p>
                <p>
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </p>
                <button type="submit">Log In</button>
            </form>
            <p>New user? <a href="signup.html">Sign Up here</a></p>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 The Gallery Café. All rights reserved.</p>
    </footer>
</body>
</html>
