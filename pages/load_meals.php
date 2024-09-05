<?php
include 'db.php';

$sql = "SELECT * FROM Meals";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "<div class='meal'>";
        echo "<img src='" . $row['image_url'] . "' alt='" . $row['meal_name'] . "'>";
        echo "<h3>" . $row['meal_name'] . "</h3>";
        echo "<p>" . $row['description'] . "</p>";
        echo "<p>Cuisine: " . $row['cuisine_type'] . "</p>";
        echo "<p>Price: $" . $row['price'] . "</p>";
        echo "</div>";
    }
} else {
    echo "No meals available.";
}

$conn->close();
?>
