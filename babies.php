<?php
include 'db_connect.php';

// Fetch Babies' products from the database
$category_id = 4; // Assuming 'Babies' category has ID = 6
$sql = "SELECT * FROM products WHERE category_id = $category_id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Babies' Collection</title>
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
                <a href="men.php">MEN</a>
                <a href="women.php" class="active">WOMAN</a>
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
            <div class="bag-icon">
                <a href="checkout.php"><img src="Images/Bag.png" alt="Bag"></a>
            </div>
            <div class="person-icon">
                <img src="Images/Person.png" alt="Person" id="person-icon">
            </div>
        </div>
    </div>

    <div class="watches-header">
    <h1>BABIES' SECTION</h1>
    <p>Soft, cozy, and adorable—our baby collection is designed with love. Featuring gentle fabrics, charming designs, and practical essentials, we ensure your little ones stay comfortable and stylish from day one. <a href="#">Read more</a></p>
</div>

    <!-- Hero Section -->
    <section class="hero-section">
        <img src="Images/babies1.avif" alt="Cute Baby Clothes">
        <div class="caption">
            <h2>Adorable & Comfortable</h2>
            <p>Discover our curated collection of cozy and stylish outfits for your little ones, perfect for every occasion.</p>
        </div>
    </section>

    <section class="hero-section">
        <div class="caption">
            <h2>Soft and Snuggly</h2>
            <p>From playtime to bedtime, our collection ensures ultimate comfort and care for your baby.</p>
        </div>
        <img src="Images/babies2.avif" alt="Baby Outfits">
    </section>

    <!-- Product Grid -->
    <section class="product-grid">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $product_id = $row['id'];
                $product_name = $row['name'];
                $product_price = $row['price'];
                $product_image = explode(",", $row['image'])[0];
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
        <p>&copy; 2024 TrendSwap. All Rights Reserved.</p>
    </footer>
</body>
</html>
