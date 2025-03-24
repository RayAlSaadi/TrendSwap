<?php 
session_start();
include 'db_connect.php';
$sql = "SELECT * FROM products WHERE category_id = 2";
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
    <?php include 'phpLogic/header.php'; ?>
    
    <div class="watches-header">
        <h1>WOMEN'S SECTION</h1>
        <p>Discover elegance and versatility with our women's collection. Whether you're dressing for work, leisure, or special occasions, our handpicked styles offer the perfect blend of fashion and functionality. <a href="#">Read more</a></p>
    </div>
    <section class="hero-section">
        <img src="Images/womendress1.avif" alt="Elegant Dresses">
        <div class="caption">
            <h2>Style Redefined</h2>
            <p>Explore our curated collection of women's fashion, blending timeless elegance with modern trends for every occasion.</p>
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
                $product_id = $row['product_id'];
                $product_name = $row['name'];
                $product_desc = $row['description'];
                $product_price = $row['price'];
                $product_images = explode(",", $row['image']);
                $product_image = trim($product_images[0]); // Get first image
                ?>
                <div class="product">
                    <a href="product_details.php?id=<?php echo $product_id; ?>">
                        <img src="Images/<?php echo htmlspecialchars($product_image); ?>" alt="<?php echo htmlspecialchars($product_name); ?>">
                    </a>
                    <h3><?php echo htmlspecialchars($product_name); ?></h3>
                    <p>£<?php echo number_format($product_price, 2); ?></p>
                    <button class="add-to-wishlist" data-id="<?php echo $product_id; ?>">&#x2764;</button>
                </div>
                <?php
            }
        } else {
            echo "<p>No products found in this category.</p>";
        }
        ?>
    </section>

    <?php include 'moonoverlay.php'; ?>
    <?php include 'phpLogic/footer.php'; ?>
  
    <!-- Notification element -->
    <div id="notification" class="notification"></div>

    <!-- External JS File (cart.js) -->
    <script src="js/cart.js"></script>
</body>
</html>