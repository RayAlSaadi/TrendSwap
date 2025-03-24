<?php 
session_start();
include 'db_connect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kids Section</title>
    <link rel="stylesheet" href="css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
</head>

<body>
    <!-- Include header.php -->
    <?php include 'phpLogic/header.php'; ?>

    <?php include 'moonoverlay.php'; ?>

    <div class="watches-header">
        <h1>KIDS' SECTION</h1>
        <p>Our kids' collection brings together playful designs, durability, and comfort. Perfect for little explorers aged 5-16, we offer trendy yet practical outfits that keep up with their adventures while keeping them stylish. <a href="#">Read more</a></p>
    </div>

    <section class="hero-section">
        <img src="Images/kids.jpg" alt="Kids Fashion">
        <div class="caption">
            <h2>Playful & Stylish</h2>
            <p>Explore our vibrant kids' clothing collection, designed to keep up with their active lifestyles while making them look their best.</p>
        </div>
    </section>

    <section class="hero-section">
        <div class="caption">
            <h2>Comfort Meets Durability</h2>
            <p>From comfortable everyday wear to trendy outfits for special occasions, our collection combines quality and style for kids aged 5-16 years.</p>
        </div>
        <img src="Images/boys1.avif" alt="Stylish Boys Outfit">
    </section>

    <section class="product-grid">
        <?php
        $sql = "SELECT * FROM products WHERE category_id = 3";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $image = explode(',', $row['image'])[0] ?? 'default.jpg';
                echo "<div class='product'>";
                echo "<a href='product_details.php?id=" . $row['product_id'] . "'>";
                echo "<img src='Images/" . htmlspecialchars($image) . "' alt='" . htmlspecialchars($row['name']) . "'>";
                echo "</a>";
                echo "<h3>" . htmlspecialchars($row['name']) . "</h3>";
                echo "<p>£" . htmlspecialchars($row['price']) . "</p>";
                echo "<div class='buttons'>";
                echo "<button class='add-to-wishlist' data-id='" . htmlspecialchars($row['product_id']) . "'>&#x2764;</button>";
                echo "</div>";
                echo "</div>";
            }
        } else {
            echo "<p>No products found.</p>"; 
        }

        $conn->close();
        ?>
    </section>

    <!-- Include footer.php -->
    <?php include 'phpLogic/footer.php'; ?>

    <!-- Notification element -->
    <div id="notification" class="notification"></div>

    <!-- External JS File (cart.js) -->
    <script src="js/cart.js"></script>
</body>
</html>