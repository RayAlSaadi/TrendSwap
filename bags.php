<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "trendswap_local";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Fetch accessories (category_id = 5)
$sql = "SELECT name, description, price, image FROM products WHERE category_id = 5";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bags - TrendSwap</title>
    <link rel="stylesheet" href="styles.css">
    <script type="text/javascript" src="js/darkmode.js" defer></script>
</head>
<body>

<!-- ✅ Dark Mode Toggle Button -->
<button id="theme-switch">
    <!-- Light Mode Icon -->
    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px">
        <path d="M480-280q-83 0-141.5-58.5T280-480q0-83 58.5-141.5T480-680q83 0 141.5 58.5T680-480q0 83-58.5 141.5T480-280ZM200-440H40v-80h160v80Zm720 0H760v-80h160v80ZM440-760v-160h80v160h-80Zm0 720v-160h80v160h-80ZM256-650l-101-97 57-59 96 100-52 56Zm492 496-97-101 53-55 101 97-57 59Zm-98-550 97-101 59 57-100 96-56-52ZM154-212l101-97 55 53-97 101-59-57Z"/>
    </svg>

    <!-- Dark Mode Icon -->
    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px">
        <path d="M480-120q-150 0-255-105T120-480q0-150 105-255t255-105q14 0 27.5 1t26.5 3q-41 29-65.5 75.5T444-660q0 90 63 153t153 63q55 0 101-24.5t75-65.5q2 13 3 26.5t1 27.5q0 150-105 255T480-120Z"/>
    </svg>
</button>

<div class="product-container">
    <h2>Accessories - Bags Collection</h2>

    <div class="products-grid">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $images = explode(',', $row["image"]); // Handle multiple images
                $firstImage = trim($images[0]); // Use first image
                
                echo '<div class="product-card">';
                echo '<img src="Images/' . htmlspecialchars($firstImage) . '" alt="Product Image">';
                echo '<h3>' . htmlspecialchars($row["name"]) . '</h3>';
                echo '<p>' . htmlspecialchars($row["description"]) . '</p>';
                echo '<p class="price">£' . number_format($row["price"], 2) . '</p>';
                echo '<button class="add-to-cart">Add to Cart</button>';
                echo '</div>';
            }
        } else {
            echo "<p>No products found.</p>";
        }
        ?>
    </div>
</div>

</body>
</html>
