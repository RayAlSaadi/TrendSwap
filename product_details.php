<?php 
session_start();
include 'db_connect.php';

// Get product ID from URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$product_id = (int)$_GET['id'];

// Get product details
$product_query = $conn->prepare("
    SELECT p.*, c.name as category_name 
    FROM products p
    JOIN categories c ON p.category_id = c.category_id
    WHERE p.product_id = ?
");
$product_query->bind_param("i", $product_id);
$product_query->execute();
$product_result = $product_query->get_result();

if ($product_result->num_rows === 0) {
    header('Location: index.php');
    exit;
}

$product = $product_result->fetch_assoc();

// Get product images
$images = [];

// First try to get images from product_images table
$images_query = $conn->prepare("
    SELECT image_path, is_primary 
    FROM product_images 
    WHERE product_id = ? 
    ORDER BY is_primary DESC
");
$images_query->bind_param("i", $product_id);
$images_query->execute();
$images_result = $images_query->get_result();

if ($images_result->num_rows > 0) {
    while ($image = $images_result->fetch_assoc()) {
        $images[] = $image['image_path'];
    }
} else {
    // Fallback to comma-separated images in products table
    if (!empty($product['image'])) {
        $images = explode(',', $product['image']);
        foreach ($images as &$img) {
            $img = trim($img);
        }
    }
}

// Get product sizes
$sizes = [];

// First try to get sizes from product_sizes table
$sizes_query = $conn->prepare("
    SELECT size, stock 
    FROM product_sizes 
    WHERE product_id = ? 
    ORDER BY size
");
$sizes_query->bind_param("i", $product_id);
$sizes_query->execute();
$sizes_result = $sizes_query->get_result();

if ($sizes_result->num_rows > 0) {
    while ($size = $sizes_result->fetch_assoc()) {
        $sizes[$size['size']] = $size['stock'];
    }
} else {
    // Fallback to comma-separated sizes in products table
    if (!empty($product['size'])) {
        $size_array = explode(',', $product['size']);
        foreach ($size_array as $size) {
            $sizes[trim($size)] = $product['stock']; // Assign general stock to each size
        }
    }
}

// Get related products from the same category
$related_query = $conn->prepare("
    SELECT * FROM products 
    WHERE category_id = ? AND product_id != ? 
    LIMIT 4
");
$related_query->bind_param("ii", $product['category_id'], $product_id);
$related_query->execute();
$related_result = $related_query->get_result();
$related_products = [];

while ($related = $related_result->fetch_assoc()) {
    // Get primary image for related product
    $r_image_query = $conn->prepare("
        SELECT image_path 
        FROM product_images 
        WHERE product_id = ? AND is_primary = 1 
        LIMIT 1
    ");
    $r_image_query->bind_param("i", $related['product_id']);
    $r_image_query->execute();
    $r_image_result = $r_image_query->get_result();
    
    if ($r_image_result->num_rows > 0) {
        $r_image = $r_image_result->fetch_assoc();
        $related['display_image'] = $r_image['image_path'];
    } else {
        // Fallback to first image in comma-separated list
        $r_images = explode(',', $related['image']);
        $related['display_image'] = trim($r_images[0]);
    }
    
    $related_products[] = $related;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - Demo Shop</title>
    <link rel="stylesheet" href="css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <style>


        /* Product Details Specific Styles */
        .product-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
        }
        
        .product-images {
            flex: 1;
            min-width: 300px;
        }
        
        .main-image {
            width: 100%;
            height: 400px;
            object-fit: cover;
            margin-bottom: 15px;
            border-radius: 5px;
        }
        
        .thumbnail-container {
            display: flex;
            gap: 10px;
            overflow-x: auto;
        }
        
        .thumbnail {
            width: 80px;
            height: 80px;
            object-fit: cover;
            cursor: pointer;
            border-radius: 3px;
            opacity: 0.7;
            transition: opacity 0.3s;
        }
        
        .thumbnail:hover, .thumbnail.active {
            opacity: 1;
        }
        
        .product-info {
            flex: 1;
            min-width: 300px;
        }
        
.product-title {
    font-size: 28px;
    margin-bottom: 10px;
    color: var(--text-color); /* instead of #333 */
text-align: left;
}

        
        .product-price {
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
            color: var(--text-color); /* instead of #333 */

        }
        
        .product-description {
            margin-bottom: 20px;
            line-height: 1.6;
        }
        
        .product-meta {
            margin-bottom: 20px;
        }
        
        .product-actions {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .quantity-selector {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        
.quantity-btn {
    width: 40px;
    height: 40px;
    background-color: var(--base-variant);
    border: 1px solid var(--border-color);
    color: var(--text-color);
    font-size: 20px;
    font-weight: bold;
    cursor: pointer;
    border-radius: 4px;
    transition: background-color 0.3s ease, color 0.3s ease, transform 0.1s ease;
    display: flex;
    justify-content: center;
    align-items: center;
    user-select: none;
}

.quantity-btn:hover {
    background-color: grey;
    color: white;
    transform: scale(1.05);
}

.quantity-btn:active {
    transform: scale(0.97);
}


        
        .quantity-input {
            width: 60px;
            height: 40px;
            text-align: center;
            border: 1px solid #ddd;
            margin: 0 5px;
        }
        
        .stock-status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 3px;
            margin-bottom: 20px;
        }
        
        .in-stock {
            background-color: #e8f5e9;
            color: #2e7d32;
        }
        
        .low-stock {
            background-color: #fff8e1;
            color: #ff8f00;
        }
        
        .out-of-stock {
            background-color: #ffebee;
            color: #c62828;
        }
        
        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        
        .add-to-cart-btn {
            background-color: #4CAF50;
            color: white;
        }
        
        .add-to-cart-btn:hover {
            background-color: #388E3C;
        }
        
        .add-to-cart-btn:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
        }
        
        .wishlist-btn {
            background-color: #f8f9fa;
            color: #333;
            border: 1px solid #ddd;
        }
        
        .wishlist-btn:hover {
            background-color: #e9ecef;
        }
        
        .divider {
            height: 1px;
            background-color: #eee;
            margin: 30px 0;
        }
        
        .related-products {
            width: 100%;
            margin-top: 40px;
        }
        
        .related-products h2 {
            margin-bottom: 20px;
        }
        
        .related-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }
        
        .related-item {
            border: 1px solid #eee;
            border-radius: 5px;
            padding: 15px;
            transition: transform 0.3s;
        }
        
        .related-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .related-item img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 3px;
            margin-bottom: 10px;
        }
        
        .related-item h3 {
            font-size: 16px;
            margin: 10px 0;
        }
        
        .related-item .price {
            font-weight: bold;
        }
        
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
        
        .size-options, .color-options {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 10px;
        }
        
        .size-options label, .color-options label {
            display: inline-block;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 3px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .size-options input[type="radio"], .color-options input[type="radio"] {
            display: none;
        }
        
        .size-options input[type="radio"]:checked + span,
        .color-options input[type="radio"]:checked + span {
            background-color: #333;
            color: white;
            font-weight: bold;
        }
        
        .size-options label span, .color-options label span {
            padding: 8px 12px;
            border-radius: 3px;
            display: inline-block;
        }
        
        .sold-out {
            text-decoration: line-through;
            opacity: 0.5;
            cursor: not-allowed;
            background-color: #f8f8f8;
            color: #aaa;
        }


    </style>
</head>
<body>
    <!-- Include header.php -->
    <?php include 'phpLogic/header.php'; ?>

    <div id="notification"></div>

    <div class="product-container">
        <div class="product-images">
            <?php if (!empty($images)): ?>
                <img src="Images/<?php echo htmlspecialchars($images[0]); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="main-image" id="main-image">
                
                <?php if (count($images) > 1): ?>
                    <div class="thumbnail-container">
                        <?php foreach ($images as $index => $img): ?>
                            <img 
                                src="Images/<?php echo htmlspecialchars($img); ?>" 
                                alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                class="thumbnail <?php echo $index === 0 ? 'active' : ''; ?>"
                                data-src="<?php echo htmlspecialchars($img); ?>"
                            >
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="no-image">No image available</div>
            <?php endif; ?>
        </div>

        <div class="product-info">
            <h1 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h1>
            <p class="product-price">£<?php echo number_format($product['price'], 2); ?></p>
            
            <?php
            // Determine stock status message and class
            $stock_status_class = '';
            $stock_status_text = '';
            $total_stock = array_sum($sizes) ?: $product['stock'];
            
            if ($total_stock <= 0) {
                $stock_status_class = 'out-of-stock';
                $stock_status_text = 'Out of Stock';
            } elseif ($total_stock < 5) {
                $stock_status_class = 'low-stock';
                $stock_status_text = 'Low Stock - Only ' . $total_stock . ' left';
            } else {
                $stock_status_class = 'in-stock';
                $stock_status_text = 'In Stock';
            }
            ?>
            
            <div class="stock-status <?php echo $stock_status_class; ?>">
                <?php echo $stock_status_text; ?>
            </div>

            <div class="product-description">
                <?php echo nl2br(htmlspecialchars($product['description'])); ?>
            </div>

            <div class="product-meta">
                <p><strong>Category:</strong> <?php echo htmlspecialchars($product['category_name']); ?></p>
                
                <?php if(!empty($sizes)): ?>
                    <div class="size-selection">
                        <p><strong>Size:</strong></p>
                        <div class="size-options">
                            <?php foreach($sizes as $size => $stock): ?>
                                <label class="<?php echo ($stock <= 0) ? 'sold-out' : ''; ?>">
                                    <input type="radio" name="size" value="<?php echo htmlspecialchars($size); ?>" <?php echo ($stock <= 0) ? 'disabled' : ''; ?>>
                                    <span><?php echo htmlspecialchars($size); ?> <?php echo ($stock <= 0) ? '(Sold out)' : ''; ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if(!empty($product['color'])): ?>
                    <div class="color-selection">
                        <p><strong>Color:</strong></p>
                        <div class="color-options">
                            <?php
                            $colors = explode(',', $product['color']);
                            foreach($colors as $color): 
                                $color = trim($color);
                            ?>
                                <label>
                                    <input type="radio" name="color" value="<?php echo htmlspecialchars($color); ?>">
                                    <span><?php echo htmlspecialchars($color); ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <div class="quantity-selector">
                <button class="quantity-btn decrement-btn">-</button>
                <input type="number" class="quantity-input" value="1" min="1" max="<?php echo $total_stock; ?>">
                <button class="quantity-btn increment-btn">+</button>
            </div>

            <div class="product-actions">
                <button 
                    class="btn add-to-cart-btn" 
                    data-id="<?php echo $product['product_id']; ?>"
                    <?php echo ($total_stock <= 0) ? 'disabled' : ''; ?>
                >
                    <?php echo ($total_stock <= 0) ? 'Out of Stock' : 'Add to Cart'; ?>
                </button>
                <button class="btn wishlist-btn add-to-wishlist" data-id="<?php echo $product['product_id']; ?>">
                    Add to Wishlist
                </button>
            </div>
        </div>

        <?php if (!empty($related_products)): ?>
            <div class="divider"></div>
            
            <div class="related-products">
                <h2>You May Also Like</h2>
                <div class="related-grid">
                    <?php foreach ($related_products as $related): ?>
                        <div class="related-item">
                            <a href="product_details.php?id=<?php echo $related['product_id']; ?>">
                                <img src="Images/<?php echo htmlspecialchars($related['display_image']); ?>" alt="<?php echo htmlspecialchars($related['name']); ?>">
                                <h3><?php echo htmlspecialchars($related['name']); ?></h3>
                                <p class="price">£<?php echo number_format($related['price'], 2); ?></p>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Include footer.php -->
    <?php include 'phpLogic/footer.php'; ?>

    <?php include 'moonoverlay.php'; ?>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Thumbnail Image Switching
            const thumbnails = document.querySelectorAll('.thumbnail');
            const mainImage = document.getElementById('main-image');
            
            thumbnails.forEach(thumb => {
                thumb.addEventListener('click', function() {
                    // Update main image
                    mainImage.src = 'Images/' + this.dataset.src;
                    
                    // Update active class
                    thumbnails.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                });
            });
            
            // Quantity Buttons
            const decrementBtn = document.querySelector('.decrement-btn');
            const incrementBtn = document.querySelector('.increment-btn');
            const quantityInput = document.querySelector('.quantity-input');
            const maxStock = <?php echo $total_stock; ?>;
            
            decrementBtn.addEventListener('click', function() {
                let currentValue = parseInt(quantityInput.value);
                if (currentValue > 1) {
                    quantityInput.value = currentValue - 1;
                }
            });
            
            incrementBtn.addEventListener('click', function() {
                let currentValue = parseInt(quantityInput.value);
                if (currentValue < maxStock) {
                    quantityInput.value = currentValue + 1;
                }
            });
            
            // Add to Cart
            const addToCartBtn = document.querySelector('.add-to-cart-btn');
            addToCartBtn?.addEventListener('click', function() {
                if (this.disabled) return;
                
                const productId = this.dataset.id;
                const quantity = parseInt(quantityInput.value);
                let selectedSize = "";
                
                // Get selected size if size options exist
                const sizeRadios = document.querySelectorAll('input[name="size"]');
                if (sizeRadios.length > 0) {
                    let sizeSelected = false;
                    for (const radio of sizeRadios) {
                        if (radio.checked) {
                            selectedSize = radio.value;
                            sizeSelected = true;
                            break;
                        }
                    }
                    
                    if (!sizeSelected) {
                        showNotification("Please select a size");
                        return;
                    }
                    
                    // Check if selected size is out of stock
                    const sizeLabel = document.querySelector(`input[name="size"][value="${selectedSize}"]`).closest('label');
                    if (sizeLabel.classList.contains('sold-out')) {
                        showNotification("Selected size is out of stock");
                        return;
                    }
                }
                
                // Send fetch request
                fetch("add_to_cart.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: `product_id=${productId}&quantity=${quantity}&size=${selectedSize}`
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
            
            // Add to Wishlist
            const wishlistBtn = document.querySelector('.add-to-wishlist');
            wishlistBtn?.addEventListener('click', function() {
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
            
            // Notification function
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
            
            // Update cart count
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