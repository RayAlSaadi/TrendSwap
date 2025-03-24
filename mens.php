<?php 
include 'db_connect.php'; 


$category_id = 1; 
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
    <title>Mens Collection</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/footer.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <style>




        /* Product Styling */
        .product {
            position: relative;
            transition: transform 0.3s, box-shadow 0.3s;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .product:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .product img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            border-radius: 4px;
            margin-bottom: 10px;
        }
        
        .product h3 {
            font-size: 18px;
            margin: 10px 0;
            color: #333;
        }
        
        .product p {
            margin: 5px 0;
            color: #666;
        }
        
        .product .product-price {
            font-size: 18px;
            color: #333;
            font-weight: bold;
        }
        
        .product h3 {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin: 10px 0 5px 0;
        }
        
        .product p {
            font-size: 16px;
            color: #333;
            margin: 5px 0 15px 0;
        }
        


        /* Notification Styling */
        #notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 25px;
            background-color: #333;
            color: white;
            border-radius: 5px;
            z-index: 1000;
            display: none;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        #notification.show {
            display: block;
            opacity: 1;
        }



    </style>
</head>

<body>

    <!-- Include header.php -->
    <?php include 'phpLogic/header.php'; ?>
    
    <?php include 'moonoverlay.php'; ?>

    <div class="watches-header">
        <h1>MEN'S SECTION</h1>
        <p>Elevate your style with our exclusive men's collection. From timeless classics to modern essentials, our carefully curated selection ensures you stay sharp, confident, and comfortable. <a href="#">Read more</a><br></p>
    </div>

    <!--  Hero Section (Banner) -->
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

    <!--  Product Grid Section -->
    <section class="product-grid">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $product_id = $row['product_id']; 
                $product_name = $row['name'];
                $product_desc = $row['description'];
                $product_price = $row['price'];
                $product_size = $row['size'];   
                $product_color = $row['color'];  
                $product_images = explode(",", $row['image']); 
                
              
                $product_image = !empty($product_images[0]) ? $product_images[0] : "default.jpg"; 

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

   
    <?php include 'phpLogic/footer.php'; ?>
   
    <div id="notification" class="notification"></div>

    <!-- cart.js -->
    <script src="js/cart.js"></script>
    
    <script>
       
        document.addEventListener("DOMContentLoaded", function() {
        
            const addToCartButtons = document.querySelectorAll('.add-to-cart');
            addToCartButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const productId = this.dataset.id;
                    
                    fetch("add_to_cart.php", {
                        method: "POST",
                        headers: { "Content-Type": "application/x-www-form-urlencoded" },
                        body: `product_id=${productId}&quantity=1`
                    })
                    .then(response => response.json())
                    .then(data => {
                        showNotification(data.success || data.error);
                        if (data.success) {
                            updateCartCount();
                        }
                    })
                    .catch(error => {
                        console.error("Error adding to cart:", error);
                        showNotification("Error adding item to cart");
                    });
                });
            });
            
           
            const wishlistButtons = document.querySelectorAll('.add-to-wishlist');
            wishlistButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault(); 
                    const productId = this.dataset.id;
                    
                    fetch("add_to_wishlist.php", {
                        method: "POST",
                        headers: { "Content-Type": "application/x-www-form-urlencoded" },
                        body: `product_id=${productId}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        showNotification(data.success || data.error);
                    })
                    .catch(error => {
                        console.error("Error adding to wishlist:", error);
                        showNotification("Error adding to wishlist");
                    });
                });
            });
            
          
            function showNotification(message) {
                const notification = document.getElementById("notification");
                if (!notification) return;
                
                notification.textContent = message;
                notification.classList.add("show");
                notification.style.display = "block";
                
                setTimeout(() => {
                    notification.classList.remove("show");
                    setTimeout(() => {
                        notification.style.display = "none";
                    }, 300);
                }, 3000);
            }
            
         
            function updateCartCount() {
                fetch("cart_count.php")
                .then(response => response.json())
                .then(data => {
                    const cartIcon = document.querySelector(".bag-icon");
                    if (cartIcon) {
                        cartIcon.setAttribute("data-count", data.count || 0);
                    }
                })
                .catch(error => console.error("Error updating cart count:", error));
            }
        });
    </script>
</body>
</html>