<?php
session_start();
include 'db_connect.php';

$category_id = 5; 
$sql = "SELECT * FROM products WHERE category_id = $category_id";
$result = $conn->query($sql);
?>  
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accessories Collection</title>
    <link rel="stylesheet" href="css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
 
    <!-- Include header.php -->
    <?php include 'phpLogic/header.php'; ?>
    <div class="watches-header">
        <h1>ACCESSORIES</h1>
        <p>Complete your look with our carefully selected accessories. From sleek watches to statement bags and jewelry, our collection adds the finishing touch to your style. Elevate your everyday outfits effortlessly. <a href="#">Read more</a></p>
    </div>
    <section class="hero-section">
        <img src="Images/menwatch7.avif" alt="Luxury Accessories">
        <div class="caption">
            <h2>Elegant Accessories</h2>
            <p>Discover our curated collection of high-quality accessories that complete your look with sophistication.</p>
        </div>
    </section>
    <section class="hero-section">
        <div class="caption">
            <h2>Style with Confidence</h2>
            <p>From stylish handbags to statement jewelry, explore our collection to enhance your everyday look.</p>
        </div>
        <img src="Images/pmenwatch1.avif" alt="Fashion Accessories">
    </section>
    <section class="product-grid">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $product_id = $row['product_id'];
                $product_name = $row['name'];
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
    <!-- Include footer.php -->
    <?php include 'phpLogic/footer.php'; ?>
    <?php include 'moonoverlay.php'; ?>
   
   
    <div id="notification" class="notification"></div>

    <!-- External JS File (cart.js) -->
    <script src="js/cart.js"></script>
</body>
</html>