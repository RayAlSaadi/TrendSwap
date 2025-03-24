<?php
session_start();
include 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php?redirect=checkout.php");
    exit();
}

$user_id = $_SESSION["user_id"];

// Get user information
$user_query = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$user_query->bind_param("i", $user_id);
$user_query->execute();
$user_result = $user_query->get_result();
$user = $user_result->fetch_assoc();

// Get cart information
$cart_query = $conn->prepare("SELECT cart_id FROM carts WHERE user_id = ?");
$cart_query->bind_param("i", $user_id);
$cart_query->execute();
$cart_result = $cart_query->get_result();
$cart = $cart_result->fetch_assoc();

$cart_items = [];
$total_amount = 0;

if ($cart) {
    $cart_id = $cart["cart_id"];
    
    // Get all items from cart_items table with size
    $item_query = $conn->prepare("
        SELECT 
            ci.cart_item_id, 
            ci.quantity, 
            ci.size,
            p.product_id, 
            p.name, 
            p.price
        FROM cart_items ci 
        JOIN products p ON ci.product_id = p.product_id 
        WHERE ci.cart_id = ?
    ");
    $item_query->bind_param("i", $cart_id);
    $item_query->execute();
    $result = $item_query->get_result();
    
    while ($row = $result->fetch_assoc()) {
        // Calculate subtotal for this item
        $row['subtotal'] = $row['quantity'] * $row['price'];
        $total_amount += $row['subtotal'];
        
        // Verify stock availability for each item by size
        $stock_check = $conn->prepare("
            SELECT stock FROM product_sizes 
            WHERE product_id = ? AND size = ?
        ");
        $stock_check->bind_param("is", $row['product_id'], $row['size']);
        $stock_check->execute();
        $stock_result = $stock_check->get_result();
        
        if ($stock_result->num_rows > 0) {
            $stock_data = $stock_result->fetch_assoc();
            $row['in_stock'] = ($stock_data['stock'] >= $row['quantity']);
            $row['available_stock'] = $stock_data['stock'];
        } else {
            // Fallback to product table
            $product_stock = $conn->prepare("SELECT stock FROM products WHERE product_id = ?");
            $product_stock->bind_param("i", $row['product_id']);
            $product_stock->execute();
            $product_result = $product_stock->get_result();
            $product_data = $product_result->fetch_assoc();
            
            $row['in_stock'] = ($product_data['stock'] >= $row['quantity']);
            $row['available_stock'] = $product_data['stock'];
        }
        
        $cart_items[] = $row;
    }
}

// Process checkout if form submitted
$order_success = false;
$order_id = 0;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['checkout'])) {
    // Check stock availability once more before processing
    $stock_error = false;
    foreach ($cart_items as $item) {
        if (!$item['in_stock']) {
            $stock_error = true;
            $error_message = "Some items in your cart are no longer available in the selected size/quantity";
            break;
        }
    }
    
    if (!$stock_error) {
        // Get form data
        $payment_method = $_POST['payment_method'];
        
        // Begin transaction
        $conn->begin_transaction();
        
        try {
            // 1. Create order record
            $create_order = $conn->prepare("
                INSERT INTO orders (
                    user_id, 
                    order_date,
                    total_amount,
                    status
                ) VALUES (?, NOW(), ?, 'Pending')
            ");
            
            $create_order->bind_param(
                "id", 
                $user_id, 
                $total_amount
            );
            
            $create_order->execute();
            $order_id = $conn->insert_id;
            
            // 2. Add order items with size information
            foreach ($cart_items as $item) {
                $add_order_item = $conn->prepare("
                    INSERT INTO order_items (
                        order_id,
                        product_id,
                        quantity,
                        price,
                        size
                    ) VALUES (?, ?, ?, ?, ?)
                ");
                
                $add_order_item->bind_param(
                    "iiids", 
                    $order_id, 
                    $item['product_id'], 
                    $item['quantity'], 
                    $item['price'],
                    $item['size']
                );
                
                $add_order_item->execute();
                
                // 3. Update product inventory in product_sizes table
                $update_inventory = $conn->prepare("
                    UPDATE product_sizes 
                    SET stock = stock - ?
                    WHERE product_id = ? AND size = ?
                ");
                $update_inventory->bind_param("iis", $item['quantity'], $item['product_id'], $item['size']);
                $update_inventory->execute();
                
                // Update total stock in products table
                $update_total = $conn->prepare("
                    UPDATE products p 
                    SET p.stock = (
                        SELECT COALESCE(SUM(ps.stock), 0) 
                        FROM product_sizes ps 
                        WHERE ps.product_id = ?
                    )
                    WHERE p.product_id = ?
                ");
                $update_total->bind_param("ii", $item['product_id'], $item['product_id']);
                $update_total->execute();
            }
            
            // 4. Clear the user's cart
            $clear_cart = $conn->prepare("DELETE FROM cart_items WHERE cart_id = ?");
            $clear_cart->bind_param("i", $cart_id);
            $clear_cart->execute();
            
            // Commit transaction
            $conn->commit();
            $order_success = true;
            
        } catch (Exception $e) {
            // Roll back transaction on error
            $conn->rollback();
            $error_message = "Error processing your order: " . $e->getMessage();
        }
        
        if ($order_success) {
            // Redirect to order confirmation page
            header("Location: order_confirmation.php?order_id=" . $order_id);
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .checkout-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
        }
        
        .checkout-form {
            flex: 2;
            min-width: 500px;
        }
        
        @media (max-width: 768px) {
            .checkout-form {
                min-width: 100%;
            }
        }
        
        .checkout-summary {
            flex: 1;
            min-width: 300px;
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
            align-self: flex-start;
        }
        
        .form-section {
            margin-bottom: 30px;
        }
        
        .form-section h2 {
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        
        .form-row {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 15px;
            gap: 15px;
        }
        
        .form-group {
            flex: 1;
            min-width: 200px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }
        
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .payment-methods {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 15px;
        }
        
        .payment-method {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 15px;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
        }
        
        .payment-method:hover {
            border-color: #333;
        }
        
        .payment-method.selected {
            border-color: #4CAF50;
            background-color: #f1f8e9;
        }
        
        .payment-method input {
            margin-right: 10px;
        }
        
        .checkout-btn {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 15px 25px;
            font-size: 16px;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            margin-top: 20px;
        }
        
        .checkout-btn:hover {
            background-color: #45a049;
        }
        
        .checkout-btn:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
        }
        
        .item-list {
            margin-bottom: 20px;
        }
        
        .checkout-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .out-of-stock-item {
            background-color: #ffebee;
            color: #c62828;
        }
        
        .item-name {
            flex: 2;
        }
        
        .item-quantity {
            flex: 1;
            text-align: center;
        }
        
        .item-price {
            flex: 1;
            text-align: right;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        
        .summary-total {
            font-weight: bold;
            font-size: 18px;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
        }
        
        .card-icons {
            display: flex;
            gap: 5px;
            margin-top: 10px;
        }
        
        .card-icon {
            width: 40px;
            height: 25px;
            background-color: #f0f0f0;
            border-radius: 3px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 10px;
        }
        
        .credit-card-form {
            margin-top: 15px;
            display: none;
        }
        
        .credit-card-form.active {
            display: block;
        }
        
        .empty-checkout {
            text-align: center;
            padding: 50px 20px;
            background-color: #f9f9f9;
            border-radius: 5px;
            margin-bottom: 20px;
            width: 100%;
        }
        
        .error-message {
            background-color: #ffebee;
            color: #c62828;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <!-- Include header.php -->
    <?php include 'phpLogic/header.php'; ?>

    <div class="checkout-container">
        <?php if (isset($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <?php if (empty($cart_items)): ?>
            <div class="empty-checkout">
                <h2>Your cart is empty</h2>
                <p>Add some products to your cart before checkout.</p>
                <a href="index.php" style="display: inline-block; margin-top: 15px; padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 4px;">Continue Shopping</a>
            </div>
        <?php else: ?>
            <?php 
            // Check if any items are out of stock
            $has_out_of_stock = false;
            foreach($cart_items as $item) {
                if (!$item['in_stock']) {
                    $has_out_of_stock = true;
                    break;
                }
            }
            ?>
            
            <form class="checkout-form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="form-section">
                    <h2>Shipping Address</h2>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="firstname">First Name</label>
                            <input type="text" id="firstname" name="firstname" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="lastname">Last Name</label>
                            <input type="text" id="lastname" name="lastname" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="address">Address</label>
                            <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($user['address'] ?? ''); ?>" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="city">City</label>
                            <input type="text" id="city" name="city" required>
                        </div>
                        <div class="form-group">
                            <label for="state">State/Province</label>
                            <input type="text" id="state" name="state">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="postal_code">Postal Code</label>
                            <input type="text" id="postal_code" name="postal_code" required>
                        </div>
                        <div class="form-group">
                            <label for="country">Country</label>
                            <select id="country" name="country" required>
                                <option value="">Select a country</option>
                                <option value="UK">United Kingdom</option>
                                <option value="US">United States</option>
                                <option value="CA">Canada</option>
                                <option value="AU">Australia</option>
                                <option value="DE">Germany</option>
                                <option value="FR">France</option>
                                <!-- Add more countries as needed -->
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h2>Payment Method</h2>
                    <div class="payment-methods">
                        <label class="payment-method">
                            <input type="radio" name="payment_method" value="credit_card" checked>
                            <span>Credit Card</span>
                            <div class="card-icons">
                                <div class="card-icon">VISA</div>
                                <div class="card-icon">MC</div>
                                <div class="card-icon">AMEX</div>
                            </div>
                        </label>
                        
                        <div class="credit-card-form active">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="card_number">Card Number</label>
                                    <input type="text" id="card_number" name="card_number" placeholder="1234 5678 9012 3456">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="card_name">Name on Card</label>
                                    <input type="text" id="card_name" name="card_name">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="expiry_date">Expiry Date</label>
                                    <input type="text" id="expiry_date" name="expiry_date" placeholder="MM/YY">
                                </div>
                                <div class="form-group">
                                    <label for="cvv">CVV</label>
                                    <input type="text" id="cvv" name="cvv" placeholder="123">
                                </div>
                            </div>
                        </div>
                        
                        <label class="payment-method">
                            <input type="radio" name="payment_method" value="paypal">
                            <span>PayPal</span>
                        </label>
                        
                        <label class="payment-method">
                            <input type="radio" name="payment_method" value="cash_on_delivery">
                            <span>Cash on Delivery</span>
                        </label>
                    </div>
                </div>

                <button type="submit" name="checkout" class="checkout-btn" <?php echo $has_out_of_stock ? 'disabled' : ''; ?>>
                    <?php echo $has_out_of_stock ? 'Some items are out of stock' : 'Complete Order'; ?>
                </button>
            </form>

            <div class="checkout-summary">
                <h2>Order Summary</h2>
                <div class="item-list">
                    <?php foreach ($cart_items as $item): ?>
                        <div class="checkout-item <?php echo !$item['in_stock'] ? 'out-of-stock-item' : ''; ?>">
                            <div class="item-name">
                                <?php echo htmlspecialchars($item['name']); ?>
                                <?php if (!empty($item['size'])): ?>
                                    <small>(Size: <?php echo htmlspecialchars($item['size']); ?>)</small>
                                <?php endif; ?>
                                <?php if (!$item['in_stock']): ?>
                                    <br><small>Out of stock (Available: <?php echo $item['available_stock']; ?>)</small>
                                <?php endif; ?>
                            </div>
                            <div class="item-quantity">×<?php echo $item['quantity']; ?></div>
                            <div class="item-price">£<?php echo number_format($item['subtotal'], 2); ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="summary-row">
                    <span>Subtotal</span>
                    <span>£<?php echo number_format($total_amount, 2); ?></span>
                </div>
                <div class="summary-row">
                    <span>Shipping</span>
                    <span>Free</span>
                </div>
                <div class="summary-row summary-total">
                    <span>Total</span>
                    <span>£<?php echo number_format($total_amount, 2); ?></span>
                </div>
                
                <?php if ($has_out_of_stock): ?>
                    <div class="error-message" style="margin-top: 20px;">
                        Some items in your cart are out of stock. Please update your cart before proceeding.
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Include footer.php -->
    <?php include 'phpLogic/footer.php'; ?>

    <?php include 'moonoverlay.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Payment method selection
            const paymentMethods = document.querySelectorAll('.payment-method');
            const creditCardForm = document.querySelector('.credit-card-form');
            
            paymentMethods.forEach(method => {
                method.addEventListener('click', function() {
                    const radio = this.querySelector('input[type="radio"]');
                    radio.checked = true;
                    
                    // Remove selected class from all methods
                    paymentMethods.forEach(m => m.classList.remove('selected'));
                    
                    // Add selected class to clicked method
                    this.classList.add('selected');
                    
                    // Show/hide credit card form
                    if (radio.value === 'credit_card') {
                        creditCardForm.classList.add('active');
                    } else {
                        creditCardForm.classList.remove('active');
                    }
                });
            });
            
            // Form validation
            const checkoutForm = document.querySelector('.checkout-form');
            
            checkoutForm?.addEventListener('submit', function(e) {
                const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
                
                if (paymentMethod === 'credit_card') {
                    const cardNumber = document.getElementById('card_number').value;
                    const cardName = document.getElementById('card_name').value;
                    const expiryDate = document.getElementById('expiry_date').value;
                    const cvv = document.getElementById('cvv').value;
                    
                    if (!cardNumber || !cardName || !expiryDate || !cvv) {
                        e.preventDefault();
                        alert('Please complete all credit card information');
                    }
                }
            });
        });
    </script>
</body>
</html>