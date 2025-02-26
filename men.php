<?php
include 'db_connect.php'; // Ensure you have a database connection file

// Fetch Men's products from the database
$category_id = 1; // Assuming 'Men' category has ID = 1
$sql = "SELECT * FROM products WHERE category_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $category_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Men's Collection</title>
    <link rel="stylesheet" href="css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
</head>

<body>
    <div class="delivery-info">
        Free Delivery Available | Find out more
    </div>

    <div class="navbar">
        <div class="navbar-left">
            <div class="navbar-logo">
                <img src="Images/Logo.jpg" alt="Logo">
            </div>
            <div class="navbar-links">
                <a href="index.php">HOME</a>
                <a href="men.php" class="active">MEN</a>
                <a href="women.php">WOMAN</a>
                <a href="kids.php">KIDS</a>
                <a href="accessories.php">ACCESSORIES</a>
                <a href="babies.php">BABIES</a>
                <a href="aboutus.html">ABOUT US</a>
            </div>
        </div>
        <div class="navbar-right">
            <div class="search-icon">
                <img src="Images/Search.png" alt="Search" id="search-icon">
            </div>
            <div class="search-bar" id="search-bar">
                <input type="text" placeholder="Search...">
            </div>
            <div class="bag-icon">
    <a href="cart.php">
        <img src="Images/Bag.png" alt="Bag">
        <span id="cart-count" class="cart-count">0</span> 
    </a>
</div>

            <div class="person-icon">
                <a href="signup.html">
                    <img src="Images/Person.png" alt="Person">
                </a>
            </div>
        </div>
    </div>

    <div class="watches-header">
    <h1>MEN'S SECTION</h1>
    <p>Elevate your style with our exclusive men's collection. From timeless classics to modern essentials, our carefully curated selection ensures you stay sharp, confident, and comfortable. <a href="#">Read more</a></p>
</div>
    <!-- ✅ Hero Section (Banner) -->
    <section class="hero-section">
        <img src="Images/home.webp" alt="Winter Outfit">
        <div class="caption">
            <h2>Wrapping Up for Winter</h2>
            <p>We've got an outerwear style to match every one of this season's dress codes – whether you're heading out on a country walk or hitting the slopes.</p>
        </div>
    </section>

    <section class="hero-section">
        <div class="caption">
            <h2>Into the Outdoors</h2>
            <p>From lightweight and warm to heavy and enveloping, these outerwear styles offer something for every outdoor adventurer.</p>
        </div>
        <img src="Images/home2.webp" alt="Outdoor Outfit">
    </section>

    <!-- ✅ Product Grid Section -->
    <section class="product-grid">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $product_id = $row['product_id']; // Fix product ID
                $product_name = $row['name'];
                $product_desc = $row['description'];
                $product_price = $row['price'];
                $product_images = explode(",", $row['image']);
                $product_image = !empty($product_images[0]) ? $product_images[0] : "default.jpg"; // Fallback image
                ?>
                <div class="product">
                    <img src="Images/<?php echo htmlspecialchars($product_image); ?>" alt="<?php echo htmlspecialchars($product_name); ?>">
                    <h3><?php echo htmlspecialchars($product_name); ?></h3>
                    <p>£<?php echo number_format($product_price, 2); ?></p>

                    <!-- ✅ Add to Cart Button -->
                    <button class="add-to-cart" data-id="<?php echo $product_id; ?>">Add to Cart</button>

                    <!-- ✅ Wishlist Button (Heart Symbol) -->
                    <button class="add-to-wishlist" data-id="<?php echo $product_id; ?>">❤️</button>
                </div>
                <?php
            }
        } else {
            echo "<p>No products found in this category.</p>";
        }
        ?>
    </section>

    <!-- ✅ Footer -->
    <footer>
        <div class="footer-container">
            <div class="footer-section about">
                <h3>About Us</h3>
                <p>We are a leading online store providing the latest fashion trends for men, women, and kids. Our mission is to deliver quality products at affordable prices.</p>
            </div>
            <div class="footer-section links">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="aboutus.php">About</a></li>
                    <li><a href="contact.php">Contact</a></li>
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
   
     <!-- ✅ Notification Element (Fix for Error) -->
     <div id="notification" class="notification"></div>
    <!-- ✅ External JS File (cart.js) -->
    <script src="js/cart.js"></script>

</body>
</html>
