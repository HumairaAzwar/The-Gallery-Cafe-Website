<?php
// Include your database connection script
include 'db.php';

// Define the SQL query
$sql = "SELECT * FROM Meals";

// Check if search parameters are set
if (isset($_GET['query']) || isset($_GET['cuisine'])) {
    $query = $_GET['query'];
    $cuisine = $_GET['cuisine'];

    // Modify the SQL query to include search conditions
    $sql .= " WHERE meal_name LIKE '%$query%' OR description LIKE '%$query%'";
    if (!empty($cuisine)) {
        $sql .= " AND cuisine_type = '$cuisine'";
    }
}

// Execute the SQL query
$result = $conn->query($sql);

// Display menu items
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<div class='menu-item'>
                <h3>{$row['meal_name']}</h3>
                <img src='{$row['image_url']}' alt='{$row['meal_name']}'>
                <p>{$row['description']}</p>
                <p>Cuisine: {$row['cuisine_type']}</p>
                <p>Price: \${$row['price']}</p>
              </div>";
    }
} else {
    echo "<p>No results found.</p>";
}

// Close the database connection
$conn->close();
?>
