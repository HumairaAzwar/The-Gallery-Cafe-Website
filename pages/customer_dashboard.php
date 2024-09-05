<?php
session_start();

// Check if user is logged in and is a customer
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'customer') {
    header("Location: login.html");
    exit();
}

include 'db.php';
$conn = new mysqli($servername, $username, $password, $dbname);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard - The Gallery Café</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Welcome, <?php echo $_SESSION['username']; ?> (Customer)</h1>
        <nav>
            <ul>
                <li><a href="customer_dashboard.php">Dashboard</a></li>
                <li><a href="menu.php">View Menu</a></li>
				<li><a href="reservation.html">Reservation></a></li>
                <li><a href="preorder.php">Place Pre-Order</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section id="customer-functions">
            <h2>Customer Dashboard</h2>
            <p>Implement your customer dashboard functionalities here...</p>
            <!-- Example: Display options like viewing menu, making reservations, etc. -->
        </section>
    </main>

    <footer>
        <p>&copy; 2024 The Gallery Café. All rights reserved.</p>
    </footer>
</body>
</html>
