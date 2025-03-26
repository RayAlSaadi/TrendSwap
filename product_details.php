<?php 
session_start();
include 'db_connect.php';


if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$product_id = (int)$_GET['id'];


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


$images = [];


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
    
    if (!empty($product['image'])) {
        $images = explode(',', $product['image']);
        foreach ($images as &$img) {
            $img = trim($img);
        }
    }
}


$sizes = [];


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
  
    if (!empty($product['size'])) {
        $size_array = explode(',', $product['size']);
        foreach ($size_array as $size) {
            $sizes[trim($size)] = $product['stock']; 
        }
    }
}


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
   
        $r_images = explode(',', $related['image']);
        $related['display_image'] = trim($r_images[0]);
    }
    
    $related_products[] = $related;
}


$reviews_query = $conn->prepare("
    SELECT r.*, u.first_name, u.last_name 
    FROM reviews r
    JOIN users u ON r.user_id = u.user_id
    WHERE r.product_id = ?
    ORDER BY r.review_date DESC
");
$reviews_query->bind_param("i", $product_id);
$reviews_query->execute();
$reviews_result = $reviews_query->get_result();

// Calculate average rating
$avg_rating_query = $conn->prepare("
    SELECT AVG(rating) as average_rating, COUNT(*) as review_count 
    FROM reviews 
    WHERE product_id = ?
");
$avg_rating_query->bind_param("i", $product_id);
$avg_rating_query->execute();
$avg_rating_result = $avg_rating_query->get_result();
$rating_data = $avg_rating_result->fetch_assoc();
$average_rating = $rating_data['average_rating'] ? round($rating_data['average_rating'], 1) : 0;
$review_count = $rating_data['review_count'];
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
    color: var(--text-color); 
text-align: left;
}

        
        .product-price {
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
            color: var(--text-color);

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

.reviews-section {
    width: 100%;
    margin: 40px 0;
}

.reviews-section h2 {
    margin-bottom: 20px;
}

.rating-summary {
    display: flex;
    margin-bottom: 30px;
}

.average-rating {
    display: flex;
    align-items: center;
    gap: 15px;
}

.rating-value {
    font-size: 48px;
    font-weight: bold;
    color: var(--text-color);
}

.stars {
    display: flex;
}

.star {
    color: #ddd;
    font-size: 24px;
}

.star.filled {
    color: #FFD700;
}

.star.half-filled {
    position: relative;
    color: #ddd;
}

.star.half-filled::before {
    content: "★";
    position: absolute;
    color: #FFD700;
    width: 50%;
    overflow: hidden;
}

.review-count {
    font-size: 14px;
    color: #666;
}

.write-review, .login-to-review {
    background-color: #f9f9f9;
    padding: 20px;
    border-radius: 5px;
    margin-bottom: 30px;
}

.write-review h3 {
    margin-bottom: 15px;
}

.login-to-review {
    text-align: center;
}

.login-to-review a {
    color: #4CAF50;
    text-decoration: underline;
}

.rating-input {
    margin-bottom: 15px;
}

.star-rating {
    display: flex;
    flex-direction: row-reverse;
    gap: 5px;
}

.star-rating input {
    display: none;
}

.star-rating label {
    font-size: 30px;
    color: #ddd;
    cursor: pointer;
    transition: color 0.2s;
}

.star-rating label:hover,
.star-rating label:hover ~ label,
.star-rating input:checked ~ label {
    color: #FFD700;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
}

.form-group textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    resize: vertical;
}

.submit-review-btn {
    background-color: #4CAF50;
    color: white;
}

.submit-review-btn:hover {
    background-color: #388E3C;
}

.reviews-list {
    margin-top: 30px;
}

.review-item {
    border-bottom: 1px solid #eee;
    padding: 20px 0;
}

.review-item:last-child {
    border-bottom: none;
}

.review-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
}

.reviewer-name {
    font-weight: bold;
}

.review-date {
    color: #666;
    font-size: 14px;
}

.review-rating {
    margin-bottom: 10px;
}

.review-comment {
    line-height: 1.6;
}

.no-reviews {
    text-align: center;
    padding: 20px;
    color: #666;
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
        <div class="divider"></div>

<div class="reviews-section">
    <h2>Customer Reviews</h2>
    
    <div class="rating-summary">
        <div class="average-rating">
            <span class="rating-value"><?php echo $average_rating; ?></span>
            <div class="stars">
                <?php for($i = 1; $i <= 5; $i++): ?>
                    <?php if($i <= $average_rating): ?>
                        <span class="star filled">★</span>
                    <?php elseif($i <= $average_rating + 0.5 && $i > $average_rating): ?>
                        <span class="star half-filled">★</span>
                    <?php else: ?>
                        <span class="star">★</span>
                    <?php endif; ?>
                <?php endfor; ?>
            </div>
            <span class="review-count"><?php echo $review_count; ?> review<?php echo $review_count != 1 ? 's' : ''; ?></span>
        </div>
    </div>
    
    <?php if(isset($_SESSION['user_id'])): ?>
        <div class="write-review">
            <h3>Write a Review</h3>
            <form id="review-form" method="post" action="submit_review.php">
                <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                
                <div class="rating-input">
                    <p>Your Rating:</p>
                    <div class="star-rating">
                        <?php for($i = 5; $i >= 1; $i--): ?>
                            <input type="radio" name="rating" id="star<?php echo $i; ?>" value="<?php echo $i; ?>" required>
                            <label for="star<?php echo $i; ?>">★</label>
                        <?php endfor; ?>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="review-comment">Your Review (optional):</label>
                    <textarea id="review-comment" name="comment" rows="4" placeholder="Share your thoughts about this product..."></textarea>
                </div>
                
                <button type="submit" class="btn submit-review-btn">Submit Review</button>
            </form>
        </div>
    <?php else: ?>
        <div class="login-to-review">
            <p>Please <a href="login.php?redirect=product_details.php?id=<?php echo $product_id; ?>">log in</a> to write a review.</p>
        </div>
    <?php endif; ?>
    
    <div class="reviews-list">
        <?php if($reviews_result->num_rows > 0): ?>
            <?php while($review = $reviews_result->fetch_assoc()): ?>
                <div class="review-item">
                    <div class="review-header">
                        <div class="reviewer-name">
                            <?php echo htmlspecialchars($review['first_name'] . ' ' . substr($review['last_name'], 0, 1) . '.'); ?>
                        </div>
                        <div class="review-date">
                            <?php echo date('F j, Y', strtotime($review['review_date'])); ?>
                        </div>
                    </div>
                    
                    <div class="review-rating">
                        <?php for($i = 1; $i <= 5; $i++): ?>
                            <span class="star <?php echo ($i <= $review['rating']) ? 'filled' : ''; ?>">★</span>
                        <?php endfor; ?>
                    </div>
                    
                    <?php if(!empty($review['comment'])): ?>
                        <div class="review-comment">
                            <?php echo nl2br(htmlspecialchars($review['comment'])); ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="no-reviews">
                <p>This product has no reviews yet. Be the first to leave a review!</p>
            </div>
        <?php endif; ?>
    </div>
</div>
    </div>

    <!-- Include footer.php -->
    <?php include 'phpLogic/footer.php'; ?>

    <?php include 'moonoverlay.php'; ?>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
          
            const thumbnails = document.querySelectorAll('.thumbnail');
            const mainImage = document.getElementById('main-image');
            
            thumbnails.forEach(thumb => {
                thumb.addEventListener('click', function() {
                   
                    mainImage.src = 'Images/' + this.dataset.src;
                    
                    
                    thumbnails.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                });
            });
            
            
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
            
           
            const addToCartBtn = document.querySelector('.add-to-cart-btn');
            addToCartBtn?.addEventListener('click', function() {
                if (this.disabled) return;
                
                const productId = this.dataset.id;
                const quantity = parseInt(quantityInput.value);
                let selectedSize = "";
                
             
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
                    
                    
                    const sizeLabel = document.querySelector(`input[name="size"][value="${selectedSize}"]`).closest('label');
                    if (sizeLabel.classList.contains('sold-out')) {
                        showNotification("Selected size is out of stock");
                        return;
                    }
                }
                
               
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

const urlParams = new URLSearchParams(window.location.search);
if (urlParams.has('review')) {
    if (urlParams.get('review') === 'submitted') {
        showNotification("Thank you! Your review has been submitted.");
    } else if (urlParams.get('review') === 'updated') {
        showNotification("Your review has been updated successfully.");
    }
} else if (urlParams.has('error')) {
    if (urlParams.get('error') === 'submit_failed') {
        showNotification("Error submitting review. Please try again.");
    } else if (urlParams.get('error') === 'update_failed') {
        showNotification("Error updating review. Please try again.");
    } else if (urlParams.get('error') === 'login_required') {
        showNotification("Please log in to leave a review.");
    } else if (urlParams.get('error') === 'missing_fields') {
        showNotification("Please fill in all required fields.");
    }
}
    </script>
</body>
</html>