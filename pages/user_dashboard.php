<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$user_type = $_SESSION['user_type'];

if ($user_type == 'admin') {
    header("Location: admin_dashboard.php");
} elseif ($user_type == 'staff') {
    header("Location: staff_dashboard.php");
} else {
    // Customer Dashboard
    echo "
    <!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Customer Dashboard - The Gallery Café</title>
        <link rel='stylesheet' href='styles.css'>
    </head>
    <body>
        <header>
            <h1>Customer Dashboard</h1>
            <nav>
                <ul>
                    <li><a href='index.html'>Home</a></li>
                    <li><a href='menu.html'>Menu</a></li>
                    <li><a href='reservation.html'>Reservation</a></li>
                    <li><a href='preorder.html'>Pre-Order</a></li>
                    <li><a href='about.html'>About</a></li>
                    <li><a href='contact.html'>Contact</a></li>
                    <li><a href='logout.php'>Logout</a></li>
                </ul>
            </nav>
        </header>

        <main>
            <section id='welcome'>
                <h2>Welcome, " . $_SESSION['username'] . "!</h2>
                <p>Choose an option from the menu to get started.</p>
            </section>
        </main>

        <footer>
            <p>&copy; 2024 The Gallery Café. All rights reserved.</p>
        </footer>
    </body>
    </html>
    ";
}
?>

