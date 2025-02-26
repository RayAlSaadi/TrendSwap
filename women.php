<?php
include 'db_connect.php'; // Ensure database connection

// Fetch Women's products from the database
$category_id = 2; // Assuming 'Women' category has ID = 2
$sql = "SELECT * FROM products WHERE category_id = $category_id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Women's Collection</title>
    <link rel="stylesheet" href="css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
</head>

<body>
    <div class="delivery-info">Free Delivery Available | Find out more</div>

    <div class="navbar">
        <div class="navbar-left">
            <div class="navbar-logo">
                <img src="Images/Logo.jpg" alt="Logo">
            </div>
            <div class="navbar-links">
                <a href="index.php">HOME</a>
                <a href="men.php">MEN</a>
                <a href="women.php" class="active">WOMAN</a>
                <a href="kids.php">KIDS</a>
                <a href="accessories.php">ACCESSORIES</a>
                <a href="babies.php">BABIES</a>
                <a href="aboutus.php">ABOUT US</a>
            </div>
        </div>
        <div class="navbar-right">
            <div class="search-icon">
                <img src="Images/Search.png" alt="Search" id="search-icon">
            </div>
            <div class="bag-icon">
                <a href="checkout.php"><img src="Images/Bag.png" alt="Bag"></a>
            </div>
            <div class="person-icon">
                <img src="Images/Person.png" alt="Person" id="person-icon">
            </div>
        </div>
    </div>

    <div class="watches-header">
    <h1>WOMEN'S SECTION</h1>
    <p>Discover elegance and versatility with our women’s collection. Whether you're dressing for work, leisure, or special occasions, our handpicked styles offer the perfect blend of fashion and functionality. <a href="#">Read more</a></p>
</div>

    <section class="hero-section">
        <img src="Images/womendress1.avif" alt="Elegant Dresses">
        <div class="caption">
            <h2>Style Redefined</h2>
            <p>Explore our curated collection of women’s fashion, blending timeless elegance with modern trends for every occasion.</p>
        </div>
    </section>

    <section class="hero-section">
        <div class="caption">
            <h2>Chic and Comfortable</h2>
            <p>From casual wear to evening gowns, find the perfect outfit to express your style effortlessly.</p>
        </div>
        <img src="Images/bluedress1.avif" alt="Chic Women's Outfit">
    </section>

    <section class="product-grid">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $product_id = $row['id'];
                $product_name = $row['name'];
                $product_desc = $row['description'];
                $product_price = $row['price'];
                $product_image = explode(",", $row['image'])[0]; // Get first image
                ?>
                <div class="product">
                    <img src="Images/<?php echo $product_image; ?>" alt="<?php echo $product_name; ?>">
                    <h3><?php echo $product_name; ?></h3>
                    <p>£<?php echo number_format($product_price, 2); ?></p>
                    <button class="add-to-cart" data-id="<?php echo $product_id; ?>">Add to Cart</button>
                    <button class="wishlist">&#x2764;</button>
                </div>
                <?php
            }
        } else {
            echo "<p>No products found in this category.</p>";
        }
        ?>
    </section>

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

    <script>
        // Toggle Search Bar
        const searchIcon = document.getElementById('search-icon');
        const searchBar = document.getElementById('search-bar');
        let isSearchOpen = false;

        searchIcon.addEventListener('click', (event) => {
            event.stopPropagation();
            if (isSearchOpen) {
                searchBar.style.width = '0';
                searchBar.classList.remove('open');
            } else {
                searchBar.style.width = '200px';
                searchBar.classList.add('open');
            }
            isSearchOpen = !isSearchOpen;
        });

        document.addEventListener('click', (event) => {
            if (!searchBar.contains(event.target) && !searchIcon.contains(event.target)) {
                searchBar.style.width = '0';
                searchBar.classList.remove('open');
                isSearchOpen = false;
            }
        });
    </script>
</body>
</html>
