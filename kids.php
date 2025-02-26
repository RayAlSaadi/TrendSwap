<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "trendswap_local";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
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
    <div class="delivery-info">Free Delivery Available | Find out more</div>
    <div class="navbar">
        <div class="navbar-left">
            <div class="navbar-logo"><img src="Images/Logo.jpg" alt="Logo"></div>
            <div class="navbar-links">
                <a href="index.php">HOME</a>
                <a href="men.php">MEN</a>
                <a href="women.php" class="active">WOMAN</a>
                <a href="kids.php">KIDS</a>
                <a href="accessories.php">ACCESSORIES</a>
                <a href="babies.php">BABIES</a>
                <a href="aboutus.html">ABOUT US</a>
            </div>
        </div>
        <div class="navbar-right">
            <div class="search-icon"><img src="Images/Search.png" alt="Search"></div>
            <div class="bag-icon"><a href="checkout.html"><img src="Images/Bag.png" alt="Bag"></a></div>
            <div class="person-icon"><a href="signup.html"><img src="Images/Person.png" alt="Person"></a></div>
        </div>
    </div>

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
                echo "<div class='product'>";
                echo "<img src='Images/" . explode(',', $row['image'])[0] . "' alt='" . $row['name'] . "'>";
                echo "<h3>" . $row['name'] . "</h3>";
                echo "<p>£" . $row['price'] . "</p>";
                echo "<div class='buttons'>";
                echo "<button class='add-to-cart' data-id='" . $row['product_id'] . "'>Add to Cart</button>";
                echo "<button class='wishlist' data-id='" . $row['product_id'] . "'>&#x2764;</button>";
                echo "</div>";
                echo "</div>";
            }
        } else {
            echo "<p>No products found.</p>";
        }
        $conn->close();
        ?>
    </section>

    <footer>
        <div class="footer-container">
            <div class="footer-section about">
                <h3>About Us</h3>
                <p>We are a leading online store providing the latest fashion trends for men, women, and kids.</p>
            </div>
            <div class="footer-section links">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="aboutus.html">About</a></li>
                    <li><a href="contact.html">Contact</a></li>
                    <li><a href="#">FAQs</a></li>
                    <li><a href="#">Privacy Policy</a></li>
                </ul>
            </div>
            <div class="footer-section contact">
                <h3>Contact Us</h3>
                <p>Email: info@trendswap.com</p>
                <p>Phone: +44 7392 284916</p>
                <div class="social-icons">
                    <a href="#"><img src="Images/FacebookW.png" alt="Facebook"></a>
                    <a href="#"><img src="Images/InstagramW.png" alt="Instagram"></a>
                    <a href="#"><img src="Images/XW.png" alt="X"></a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2024 TrendSwap. All Rights Reserved.</p>
        </div>
    </footer>

</body>
</html>
