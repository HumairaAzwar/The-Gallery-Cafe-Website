<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'staff') {
    header("Location: login.html");
    exit();
}

echo "
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Staff Dashboard - The Gallery Café</title>
    <link rel='stylesheet' href='styles.css'>
</head>
<body>
    <header>
        <h1>Staff Dashboard</h1>
        <nav>
            <ul>
                <li><a href='index.html'>Home</a></li>
                <li><a href='menu.html'>Menu</a></li>
                <li><a href='view_reservations.php'>View Reservations</a></li>
                <li><a href='process_preorders.php'>Process Pre-Orders</a></li>
                <li><a href='logout.php'>Logout</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section id='welcome'>
            <h2>Welcome, " . $_SESSION['username'] . "!</h2>
            <p>View and manage reservations and pre-orders using the options from the menu.</p>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 The Gallery Café. All rights reserved.</p>
    </footer>
</body>
</html>
";
?>
