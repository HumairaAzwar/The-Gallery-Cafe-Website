<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu - The Gallery Café</title>
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
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            padding: 20px;
            max-width: 1200px;
            margin: 20px auto;
        }

        .menu-section {
            display: flex;
			flex-wrap: wrap;
            gap: 20px; /* Adjust the gap between items */
        }

        .menu-item {
            background-color: #FFF7E1;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px; /* Adjust margin as needed */
            padding: 20px;
            flex: 1 1 calc(33.333% - 20px); /* Ensure each item takes up one-third of the space */
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            align-items: center;
            height: 450px;/* Set a fixed height for menu items */
        }

        .menu-item img {
            width: 100%;
            max-width: 300px; /* Set a max-width to ensure consistent sizing */
            height: 200px; /* Set a fixed height */
            object-fit: cover; /* Ensures the image covers the area without distortion */
            border-radius: 8px;
            margin-bottom: 10px;
        }

        #menu-search {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        #menu-search form {
            display: flex;
            flex-direction: row;
            align-items: center;
        }

        #menu-search input, #menu-search select {
            padding: 10px;
            margin-right: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        #menu-search button {
            padding: 10px 20px;
            background-color:#8B4513 ;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        #menu-search button:hover {
            background-color: #D2B48C;
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
            <li><a href="login.php">Login</a></li>
        </ul>
    </nav>

    <main>
        <section id="menu-search">
            <h2>Search Our Menu</h2>
            <form method="GET" action="menu.php">
                <input type="text" name="query" placeholder="Search for a dish...">
                <select name="cuisine">
                    <option value="">All Cuisines</option>
                    <option value="Italian">Italian</option>
                    <option value="Chinese">Chinese</option>
                    <option value="Indian">Indian</option>
                    <option value="Sri Lankan">Sri Lankan</option>
                </select>
                <button type="submit">Search</button>
            </form>
        </section>

        <section id="menu-items">
            <h2>Our Delicious Meals</h2>
            <div id="menu-results" class="menu-section">
                <?php
                
                error_reporting(E_ALL);
                ini_set('display_errors', 1);

                // database connection
                $servername = "localhost";
                $username = "root";
                $password = "";
                $dbname = "gallery_cafe";

                // Create connection
                $conn = new mysqli($servername, $username, $password, $dbname);

                // Check connection
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // SQL query
                $sql = "SELECT * FROM Meals WHERE 1";

                
                if (isset($_GET['query']) || isset($_GET['cuisine'])) {
                    $query = isset($_GET['query']) ? $_GET['query'] : '';
                    $cuisine = isset($_GET['cuisine']) ? $_GET['cuisine'] : '';

                    // Modify SQL query based on search criteria
                    if (!empty($query)) {
                        $sql .= " AND (meal_name LIKE '%$query%' OR description LIKE '%$query%')";
                    }
                    if (!empty($cuisine)) {
                        $sql .= " AND cuisine_type = '$cuisine'";
                    }
                }

                // Execute SQL query
                $result = $conn->query($sql);

                // Display menu items
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                         $imageUrl = $row['image_url'];
                        echo "<div class='menu-item'>
                                <h3>{$row['meal_name']}</h3>
                                <img src='{$imageUrl}' alt='{$row['meal_name']}'>
                                <p>{$row['description']}</p>
                                <p>Cuisine: {$row['cuisine_type']}</p>
                                <p>Price: Rs. {$row['price']}</p>
                              </div>";
                    }
                } else {
                    echo "<p>No results found.</p>";
                }

                // Close database connection
                $conn->close();
                ?>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 The Gallery Café. All rights reserved.</p>
    </footer>
</body>
</html>
