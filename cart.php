<?php
session_start();
include 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php?redirect=cart.php");
    exit();
}

$user_id = $_SESSION["user_id"];

// Get cart information
$cart_query = $conn->prepare("SELECT cart_id FROM carts WHERE user_id = ?");
$cart_query->bind_param("i", $user_id);
$cart_query->execute();
$cart_result = $cart_query->get_result();
$cart = $cart_result->fetch_assoc();

$cart_items = [];
$total_amount = 0;
$item_count = 0;

if ($cart) {
    $cart_id = $cart["cart_id"];
    
    // Get all items from cart_items table including size
    $item_query = $conn->prepare("
        SELECT 
            ci.cart_item_id, 
            ci.quantity, 
            ci.size,
            p.product_id, 
            p.name, 
            p.price, 
            p.image,
            c.name as category_name,
            p.color
        FROM cart_items ci 
        JOIN products p ON ci.product_id = p.product_id 
        LEFT JOIN categories c ON p.category_id = c.category_id
        WHERE ci.cart_id = ?
    ");
    $item_query->bind_param("i", $cart_id);
    $item_query->execute();
    $result = $item_query->get_result();
    
    while ($row = $result->fetch_assoc()) {
        // Get primary image for product
        $image_query = $conn->prepare("
            SELECT image_path FROM product_images 
            WHERE product_id = ? AND is_primary = 1 
            LIMIT 1
        ");
        $image_query->bind_param("i", $row['product_id']);
        $image_query->execute();
        $image_result = $image_query->get_result();
        
        if ($image_result->num_rows > 0) {
            $image = $image_result->fetch_assoc();
            $row['display_image'] = $image['image_path'];
        } else {
            // Fallback to comma-separated images
            $images = explode(',', $row['image']);
            $row['display_image'] = trim($images[0]);
        }
        
        // Calculate subtotal for this item
        $row['subtotal'] = $row['quantity'] * $row['price'];
        $total_amount += $row['subtotal'];
        $item_count += $row['quantity'];
        
        // Check stock availability for this size
        $stock_query = $conn->prepare("
            SELECT stock FROM product_sizes 
            WHERE product_id = ? AND size = ?
        ");
        $stock_query->bind_param("is", $row['product_id'], $row['size']);
        $stock_query->execute();
        $stock_result = $stock_query->get_result();
        
        if ($stock_result->num_rows > 0) {
            $stock_data = $stock_result->fetch_assoc();
            $row['max_quantity'] = $stock_data['stock'];
            $row['in_stock'] = ($stock_data['stock'] > 0);
        } else {
            // Fallback to products table for total stock
            $product_query = $conn->prepare("SELECT stock FROM products WHERE product_id = ?");
            $product_query->bind_param("i", $row['product_id']);
            $product_query->execute();
            $product_result = $product_query->get_result();
            $product_data = $product_result->fetch_assoc();
            
            $row['max_quantity'] = $product_data['stock'];
            $row['in_stock'] = ($product_data['stock'] > 0);
        }
        
        $cart_items[] = $row;
    }
}

// Check if user has a wishlist
$wishlist_query = $conn->prepare("SELECT wishlist_id FROM wishlists WHERE user_id = ?");
$wishlist_query->bind_param("i", $user_id);
$wishlist_query->execute();
$wishlist_result = $wishlist_query->get_result();
$has_wishlist = ($wishlist_result->num_rows > 0);
$wishlist_id = $has_wishlist ? $wishlist_result->fetch_assoc()['wishlist_id'] : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Bag</title>
    <link rel="stylesheet" href="css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>



.cart-summary {
    background-color: var(--card-bg); /* Matches dark mode background */
    border: 1px solid var(--border-color);
    color: var(--text-color);
    transition: background-color 0.3s ease, color 0.3s ease;
}
body.darkmode .cart-summary {
    background-color: var(--card-bg) !important;
    color: var(--text-color) !important;
}

body.darkmode .cart-container .cart-title {
    color: var(--text-color) !important;
}


/* 🟣 Cart container - dark mode fix */
body.darkmode .cart-container {
    background-color: var(--base-color) !important;
    color: var(--text-color) !important;
    transition: background-color 0.3s ease, color 0.3s ease;
}

/* Optional: Adjust continue shopping button to match login button */
body.darkmode .cart-container .empty-cart a,
body.darkmode .cart-summary .continue-shopping {
    background-color: var(--base-color);
    color: var(--text-color);
    border: 1px solid var(--border-color);
    font-family: 'Montserrat', sans-serif;
    border-radius: 0;
    font-weight: bold;
    padding: 10px 20px;
    text-transform: uppercase;
    text-decoration: none;
    transition: background 0.3s ease, color 0.3s ease;
}

body.darkmode .cart-container .empty-cart a:hover,
body.darkmode .cart-summary .continue-shopping:hover {
    background-color: var(--text-color);
    color: var(--base-color);
}

        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #f8f8f8;
            margin: 0;
            padding: 0;
            color: #333;
        }
        
        .cart-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .cart-title {
            width: 100%;
            text-align: center;
            font-size: 28px;
            font-weight: 400;
            margin: 20px 0 40px;
        }
        
        .cart-main {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
        }
        
        .cart-items {
            flex: 2;
            min-width: 300px;
        }
        
        .cart-summary {
            flex: 1;
            min-width: 300px;
            padding: 20px;
            border: 1px solid #eee;
            background-color: #fff;
            align-self: flex-start;
        }
        
        .cart-item {
            background-color: #fff;
            margin-bottom: 20px;
            padding: 20px 30px;
            border: 1px solid #eee;
        }
        
        .item-main {
            display: flex;
            gap: 30px;
        }
        
        .item-image-container {
            width: 120px;
            height: 120px;
            flex-shrink: 0;
        }
        
        .item-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .item-details {
            flex-grow: 1;
        }
        
        .item-category {
            font-weight: 600;
            text-transform: uppercase;
            margin: 0 0 5px 0;
            font-size: 14px;
        }
        
        .item-name {
            font-size: 16px;
            margin: 0 0 15px;
            font-weight: 400;
        }
        
        .item-info {
            margin-bottom: 15px;
        }
        
        .item-color, .item-size {
            margin-bottom: 5px;
            font-size: 14px;
        }
        
        .item-price {
            font-size: 16px;
            margin-bottom: 20px;
        }
        
        .quantity-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 20px;
        }
        
        .quantity-section {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .quantity-label {
            font-size: 14px;
        }
        
        .quantity-dropdown {
            position: relative;
            display: inline-block;
        }
        
        .quantity-dropdown select {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background: transparent;
            width: 60px;
            padding: 8px 30px 8px 10px;
            border: 1px solid #ddd;
            cursor: pointer;
        }
        
        .quantity-dropdown::after {
            content: '';
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            width: 0; 
            height: 0; 
            border-left: 5px solid transparent;
            border-right: 5px solid transparent;
            border-top: 5px solid #333;
            pointer-events: none;
        }
        
        .item-actions {
            display: flex;
            gap: 20px;
            align-items: center;
        }
        
        .add-to-favourites {
            background: none;
            border: none;
            color: #333;
            text-decoration: underline;
            cursor: pointer;
            padding: 0;
            font-size: 14px;
            text-transform: uppercase;
        }
        
        .add-to-favourites:hover {
            color: #000;
        }
        
        .remove-btn {
            background-color: #f44336;
            color: white;
            border: none;
            padding: 8px 16px;
            cursor: pointer;
            font-size: 14px;
            text-transform: uppercase;
        }
        
        .remove-btn:hover {
            background-color: #d32f2f;
        }
        
        .summary-title {
            font-size: 18px;
            font-weight: 600;
            margin: 0 0 20px;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 14px;
        }
        
        .summary-row.total {
            font-weight: 600;
            font-size: 16px;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        
        .checkout-btn {
            width: 100%;
            padding: 15px;
            background-color: #304D30;
            color: white;
            border: none;
            cursor: pointer;
            margin-top: 20px;
            font-weight: 500;
            font-size: 14px;
            text-transform: uppercase;
        }
        
        .checkout-btn:hover {
            background-color: #1a301a;
        }
        
        .checkout-btn:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
        }
        
        .continue-shopping {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #666;
            text-decoration: none;
            font-size: 14px;
        }
        
        .continue-shopping:hover {
            text-decoration: underline;
        }
        
        .empty-cart {
            width: 100%;
            text-align: center;
            padding: 50px 0;
        }
        
        .empty-cart h2 {
            font-weight: 400;
        }
        
        .empty-cart a {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #304D30;
            color: white;
            text-decoration: none;
        }
        
        .out-of-stock {
            color: #d32f2f;
            font-weight: 500;
            font-size: 14px;
            margin-top: 5px;
        }
        
        .error-message {
            display: none;
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #fff;
            border: 1px solid #ddd;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            z-index: 1000;
            text-align: center;
            width: 80%;
            max-width: 400px;
        }
        
        .error-message .close-btn {
            margin-top: 15px;
            padding: 8px 16px;
            background-color: #4285F4;
            color: white;
            border: none;
            cursor: pointer;
        }
        
        @media (max-width: 768px) {
            .cart-main {
                flex-direction: column;
            }
            
            .item-main {
                flex-direction: column;
                gap: 15px;
            }
            
            .item-image-container {
                width: 100px;
                height: 100px;
                margin: 0 auto;
            }
            
            .quantity-row {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }
            
            .cart-title {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <?php include 'phpLogic/header.php'; ?>

    <div class="error-message" id="errorMessage">
        <p id="errorText">An error occurred while updating. Please try again.</p>
        <button class="close-btn" onclick="closeError()">Close</button>
    </div>
        <br><br><br><br><br><br><br><br><br>

    <div class="cart-container">
        <h1 class="cart-title">Shopping Bag</h1>
        
        <?php if (empty($cart_items)): ?>
            <div class="empty-cart">
                <h2>Your shopping bag is empty</h2>
                <p>You have no items in your shopping bag.</p>
                <a href="index.php">Continue Shopping</a>
            </div>
        <?php else: ?>
            <div class="cart-main">
                <div class="cart-items">
                    <?php 
                    $has_out_of_stock = false;
                    foreach ($cart_items as $item): 
                        if (!$item['in_stock']) {
                            $has_out_of_stock = true;
                        }
                        
                        // Get first color
                        $color = "Black";
                        if (!empty($item['color'])) {
                            $colors = explode(',', $item['color']);
                            $color = trim($colors[0]);
                        }
                    ?>
                        <div class="cart-item">
                            <div class="item-main">
                                <div class="item-image-container">
                                    <img src="Images/<?php echo htmlspecialchars($item['display_image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="item-image">
                                </div>
                                
                                <div class="item-details">
                                    <div class="item-category"><?php echo htmlspecialchars($item['category_name'] ?? 'MEN'); ?></div>
                                    <h3 class="item-name"><?php echo htmlspecialchars($item['name']); ?></h3>
                                    
                                    <div class="item-info">
                                        <div class="item-color">Colour: <?php echo htmlspecialchars($color); ?></div>
                                        <div class="item-size">Size: <?php echo htmlspecialchars($item['size']); ?></div>
                                    </div>
                                    
                                    <div class="item-price">£<?php echo number_format($item['price'], 2); ?></div>
                                    
                                    <?php if (!$item['in_stock']): ?>
                                        <div class="out-of-stock">Out of stock</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="quantity-row">
                                <div class="quantity-section">
                                    <div class="quantity-label">Quantity:</div>
                                    <?php if ($item['in_stock']): ?>
                                        <div class="quantity-dropdown">
                                            <select data-item-id="<?php echo $item['cart_item_id']; ?>">
                                                <?php for ($i = 1; $i <= min(10, $item['max_quantity']); $i++): ?>
                                                    <option value="<?php echo $i; ?>" <?php echo ($i == $item['quantity']) ? 'selected' : ''; ?>><?php echo $i; ?></option>
                                                <?php endfor; ?>
                                            </select>
                                        </div>
                                    <?php else: ?>
                                        <div><?php echo $item['quantity']; ?></div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="item-actions">
                                    <button class="add-to-favourites" data-id="<?php echo $item['product_id']; ?>">ADD TO FAVOURITES</button>
                                    <button class="remove-btn" data-id="<?php echo $item['cart_item_id']; ?>">REMOVE</button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="cart-summary">
                    <h2 class="summary-title">ORDER SUMMARY</h2>
                    
                    <div class="summary-row">
                        <span><?php echo $item_count; ?> Items Subtotal</span>
                        <span>£<?php echo number_format($total_amount, 2); ?></span>
                    </div>
                    
                    <div class="summary-row">
                        <span>Delivery</span>
                        <span>Calculated at checkout</span>
                    </div>
                    
                    <div class="summary-row total">
                        <span>Total</span>
                        <span>£<?php echo number_format($total_amount, 2); ?></span>
                    </div>
                    
                    <button onclick="window.location.href='checkout.php'" class="checkout-btn" <?php echo $has_out_of_stock ? 'disabled' : ''; ?>>
                        SECURE CHECKOUT NOW
                    </button>
                    
                    <a href="index.php" class="continue-shopping">Continue Shopping</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
<br><br><br><br><br><br><br><br><br><br><br><br>
    <?php include 'phpLogic/footer.php'; ?>
    
        <?php include 'moonoverlay.php'; ?>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Quantity dropdown change handler
            const quantitySelects = document.querySelectorAll('.quantity-dropdown select');
            
            quantitySelects.forEach(select => {
                select.addEventListener('change', function() {
                    const cartItemId = this.getAttribute('data-item-id');
                    const newQuantity = this.value;
                    
                    updateCartItem(cartItemId, newQuantity, select);
                });
            });
            
            // Remove item buttons
            const removeButtons = document.querySelectorAll('.remove-btn');
            
            removeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const cartItemId = this.getAttribute('data-id');
                    removeCartItem(cartItemId, button);
                });
            });
            
            // Add to wishlist/favorites buttons
            const favoriteButtons = document.querySelectorAll('.add-to-favourites');
            
            favoriteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const productId = this.getAttribute('data-id');
                    addToWishlist(productId, button);
                });
            });
        });
        
        function updateCartItem(itemId, quantity, element) {
            // Disable the element to prevent multiple submissions
            element.disabled = true;
            
            // Send AJAX request
            const xhr = new XMLHttpRequest();
           xhr.open('POST', 'update_cart_quantity.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            
            xhr.onload = function() {
                if (xhr.status === 200) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        
                        if (response.success) {
                            // Success - reload page to update all calculations
                            window.location.reload();
                        } else {
                            // Error
                            showError(response.error || 'Failed to update cart');
                            element.disabled = false;
                        }
                    } catch (e) {
                        showError('Invalid response from server');
                        element.disabled = false;
                    }
                } else {
                    showError('Server error: ' + xhr.status);
                    element.disabled = false;
                }
            };
            
            xhr.onerror = function() {
                showError('Network error');
                element.disabled = false;
            };
            
            xhr.send('cart_item_id=' + encodeURIComponent(itemId) + '&quantity=' + encodeURIComponent(quantity));
        }
        
        function removeCartItem(itemId, element) {
            if (!confirm('Are you sure you want to remove this item?')) {
                return;
            }
            
            // Disable the button
            element.disabled = true;
            
            // Send AJAX request
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'remove_from_cart.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            
            xhr.onload = function() {
                if (xhr.status === 200) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        
                        if (response.success) {
                            // Success - reload page
                            window.location.reload();
                        } else {
                            // Error
                            showError(response.error || 'Failed to remove item');
                            element.disabled = false;
                        }
                    } catch (e) {
                        showError('Invalid response from server');
                        element.disabled = false;
                    }
                } else {
                    showError('Server error: ' + xhr.status);
                    element.disabled = false;
                }
            };
            
            xhr.onerror = function() {
                showError('Network error');
                element.disabled = false;
            };
            
            xhr.send('cart_item_id=' + encodeURIComponent(itemId));
        }
        
        function addToWishlist(productId, element) {
            // Disable the button
            element.disabled = true;
            
            // Send AJAX request
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'add_to_wishlist.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            
            xhr.onload = function() {
                if (xhr.status === 200) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        
                        if (response.success) {
                            // Change button text temporarily as feedback
                            const originalText = element.textContent;
                            element.textContent = 'ADDED TO FAVOURITES';
                            
                            setTimeout(function() {
                                element.textContent = originalText;
                                element.disabled = false;
                            }, 2000);
                        } else {
                            // Error
                            showError(response.error || 'Failed to add to favourites');
                            element.disabled = false;
                        }
                    } catch (e) {
                        showError('Invalid response from server');
                        element.disabled = false;
                    }
                } else {
                    showError('Server error: ' + xhr.status);
                    element.disabled = false;
                }
            };
            
            xhr.onerror = function() {
                showError('Network error');
                element.disabled = false;
            };
            
            xhr.send('product_id=' + encodeURIComponent(productId));
        }
        
        function showError(message) {
            const errorElement = document.getElementById('errorMessage');
            const errorText = document.getElementById('errorText');
            
            errorText.textContent = message;
            errorElement.style.display = 'block';
        }
        
        function closeError() {
            document.getElementById('errorMessage').style.display = 'none';
        }
    </script>
</body>
</html>