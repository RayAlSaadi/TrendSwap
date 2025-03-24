<?php
session_start();
include 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php?redirect=previous_orders.php");
    exit();
}

$user_id = $_SESSION["user_id"];

// Get all orders for this user
$orders_query = $conn->prepare("
    SELECT * FROM orders 
    WHERE user_id = ? 
    ORDER BY order_date DESC
");
$orders_query->bind_param("i", $user_id);
$orders_query->execute();
$orders_result = $orders_query->get_result();

$orders = [];
while ($order = $orders_result->fetch_assoc()) {
    // Get order items
    $items_query = $conn->prepare("
        SELECT oi.*, p.name, p.image 
        FROM order_items oi
        JOIN products p ON oi.product_id = p.product_id
        WHERE oi.order_id = ?
    ");
    $items_query->bind_param("i", $order['order_id']);
    $items_query->execute();
    $items_result = $items_query->get_result();
    
    $items = [];
    while ($item = $items_result->fetch_assoc()) {
        // Get first image
        if (strpos($item['image'], ',') !== false) {
            $images = explode(',', $item['image']);
            $item['display_image'] = trim($images[0]);
        } else {
            $item['display_image'] = $item['image'];
        }
        
        // Try to get image from product_images table if available
        $image_query = $conn->prepare("
            SELECT image_path 
            FROM product_images 
            WHERE product_id = ? AND is_primary = 1 
            LIMIT 1
        ");
        $image_query->bind_param("i", $item['product_id']);
        $image_query->execute();
        $image_result = $image_query->get_result();
        
        if ($image_result->num_rows > 0) {
            $image = $image_result->fetch_assoc();
            $item['display_image'] = $image['image_path'];
        }
        
        // Check if product has been returned or can be returned
        $return_query = $conn->prepare("
            SELECT * FROM order_returns 
            WHERE order_id = ? AND order_item_id = ?
        ");
        $return_query->bind_param("ii", $order['order_id'], $item['order_item_id']);
        $return_query->execute();
        $return_result = $return_query->get_result();
        
        if ($return_result->num_rows > 0) {
            $return = $return_result->fetch_assoc();
            $item['return_status'] = $return['status'];
        } else {
            $item['return_status'] = null;
        }
        
        $items[] = $item;
    }
    
    $order['items'] = $items;
    $orders[] = $order;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Orders</title>
    <link rel="stylesheet" href="css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <style>
        .orders-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }
        
        .orders-header {
            margin-bottom: 30px;
            text-align: center;
        }
        
        .order-card {
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            overflow: hidden;
        }
        
        .order-header {
            background-color: #f5f5f5;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            align-items: center;
            border-bottom: 1px solid #eee;
        }
        
        .order-id {
            font-weight: bold;
        }
        
        .order-date, .order-status {
            margin: 5px 0;
        }
        
        .order-status {
            padding: 3px 10px;
            border-radius: 15px;
            font-size: 14px;
            display: inline-block;
        }
        
        .status-pending {
            background-color: #f5f5f5;
            color: #333;
        }
        
        .status-processing {
            background-color: #e3f2fd;
            color: #0d47a1;
        }
        
        .status-shipped {
            background-color: #e8f5e9;
            color: #1b5e20;
        }
        
        .status-delivered {
            background-color: #1b5e20;
            color: white;
        }
        
        .status-cancelled {
            background-color: #ffebee;
            color: #b71c1c;
        }
        
        .status-returned {
            background-color: #ede7f6;
            color: #4527a0;
        }
        
        .order-items {
            padding: 20px;
        }
        
        .order-item {
            display: flex;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }
        
        .order-item:last-child {
            border-bottom: none;
        }
        
        .item-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            margin-right: 15px;
            border-radius: 5px;
        }
        
        .item-details {
            flex: 1;
        }
        
        .item-name {
            margin-top: 0;
            margin-bottom: 5px;
        }
        
        .item-price, .item-quantity {
            margin: 5px 0;
            color: #666;
        }
        
        .item-total {
            font-weight: bold;
        }
        
        .item-size {
            font-size: 14px;
            color: #666;
        }
        
        .item-actions {
            display: flex;
            flex-direction: column;
            justify-content: center;
            margin-left: 15px;
        }
        
        .return-btn {
            background-color: #f5f5f5;
            color: #333;
            border: 1px solid #ddd;
            padding: 8px 12px;
            border-radius: 3px;
            cursor: pointer;
            font-size: 14px;
            text-align: center;
        }
        
        .return-btn:hover {
            background-color: #e0e0e0;
        }
        
        .return-btn.disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .return-status {
            font-size: 14px;
            margin-top: 5px;
            text-align: center;
        }
        
        .order-summary {
            background-color: #f9f9f9;
            padding: 15px 20px;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid #eee;
        }
        
        .order-total {
            font-weight: bold;
            font-size: 18px;
        }
        
        .item-return-form {
            margin-top: 10px;
            padding: 10px;
            background-color: #f5f5f5;
            border-radius: 3px;
            display: none;
        }
        
        .item-return-form select, .item-return-form textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 3px;
        }
        
        .item-return-form button {
            background-color: #f44336;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 3px;
            cursor: pointer;
        }
        
        .empty-orders {
            text-align: center;
            padding: 50px 20px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }
        
        .empty-orders a {
            display: inline-block;
            margin-top: 20px;
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 3px;
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

/* DARK MODE FIX FOR EMPTY ORDER STATE */
body.darkmode .empty-orders {
  background-color: var(--card-bg) !important;
  color: var(--text-color) !important;
  border: 1px solid var(--border-color) !important;
  text-align: center;
  padding: 50px 20px;
  border-radius: 8px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.2);
  transition: background-color 0.3s ease, color 0.3s ease;
}

body.darkmode .empty-orders h2,
body.darkmode .empty-orders p,
body.darkmode .empty-orders a {
  color: var(--text-color) !important;
}

body.darkmode .empty-orders a.btn {
  background-color: transparent;
  border: 2px solid var(--text-color);
  padding: 10px 20px;
  font-weight: bold;
  border-radius: 5px;
  display: inline-block;
  margin-top: 20px;
  color: var(--text-color);
  transition: background-color 0.3s ease, color 0.3s ease;
}

body.darkmode .empty-orders a.btn:hover {
  background-color: var(--text-color);
  color: var(--base-color);
}


body.darkmode .orders-header h1 {

  color: var(--text-color);
}


</style>
</head>
<body>
    <!-- Include header.php -->
    <?php include 'phpLogic/header.php'; ?>

    <div id="notification"></div>

    <div class="orders-container">
        <div class="orders-header">
            <h1>Your Orders</h1>
        </div>

        <?php if (empty($orders)): ?>
            <div class="empty-orders">
                <h2>You haven't placed any orders yet</h2>
                <p>Browse our products and place your first order!</p>
                <a href="index.php">Start Shopping</a>
            </div>
        <?php else: ?>
            <?php foreach ($orders as $order): ?>
                <div class="order-card">
                    <div class="order-header">
                        <div>
                            <div class="order-id">Order #<?php echo $order['order_id']; ?></div>
                            <div class="order-date">Placed on <?php echo date('F j, Y', strtotime($order['order_date'])); ?></div>
                        </div>
                        <div class="order-status status-<?php echo strtolower($order['status']); ?>">
                            <?php echo $order['status']; ?>
                        </div>
                    </div>

                    <div class="order-items">
                        <?php foreach ($order['items'] as $item): ?>
                            <div class="order-item">
                                <img src="Images/<?php echo htmlspecialchars($item['display_image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="item-image">
                                
                                <div class="item-details">
                                    <h3 class="item-name"><?php echo htmlspecialchars($item['name']); ?></h3>
                                    <p class="item-price">Price: £<?php echo number_format($item['price'], 2); ?></p>
                                    <p class="item-quantity">Quantity: <?php echo $item['quantity']; ?></p>
                                    <?php if (!empty($item['size'])): ?>
                                        <p class="item-size">Size: <?php echo htmlspecialchars($item['size']); ?></p>
                                    <?php endif; ?>
                                    <p class="item-total">Total: £<?php echo number_format($item['price'] * $item['quantity'], 2); ?></p>
                                </div>
                                
                                <div class="item-actions">
                                    <?php if ($order['status'] === 'Delivered'): ?>
                                        <?php if ($item['return_status'] === null): ?>
                                            <button class="return-btn" data-order="<?php echo $order['order_id']; ?>" data-item="<?php echo $item['order_item_id']; ?>">Return Item</button>
                                            <div class="item-return-form" id="return-form-<?php echo $item['order_item_id']; ?>">
                                                <select name="reason" required>
                                                    <option value="">Select a reason</option>
                                                    <option value="Wrong size">Wrong size</option>
                                                    <option value="Damaged">Damaged</option>
                                                    <option value="Not as described">Not as described</option>
                                                    <option value="Changed mind">Changed mind</option>
                                                    <option value="Other">Other</option>
                                                </select>
                                                <textarea name="comments" placeholder="Additional comments (optional)" rows="3"></textarea>
                                                <button class="submit-return" data-order="<?php echo $order['order_id']; ?>" data-item="<?php echo $item['order_item_id']; ?>">Submit Return</button>
                                            </div>
                                        <?php else: ?>
                                            <div class="return-status">
                                                Return <?php echo strtolower($item['return_status']); ?>
                                            </div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <button class="return-btn disabled" disabled>Can't return yet</button>
                                        <div class="return-status">
                                            Order must be delivered first
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="order-summary">
                        <div class="order-total">
                            Total: £<?php echo number_format($order['total_amount'], 2); ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Include footer.php -->
    <?php include 'phpLogic/footer.php'; ?>

    <?php include 'moonoverlay.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle return forms
            const returnButtons = document.querySelectorAll('.return-btn:not(.disabled)');
            
            returnButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const itemId = this.getAttribute('data-item');
                    const returnForm = document.getElementById(`return-form-${itemId}`);
                    
                    if (returnForm.style.display === 'block') {
                        returnForm.style.display = 'none';
                    } else {
                        returnForm.style.display = 'block';
                    }
                });
            });
            
            // Submit return request
            const submitReturnButtons = document.querySelectorAll('.submit-return');
            
            submitReturnButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const orderId = this.getAttribute('data-order');
                    const itemId = this.getAttribute('data-item');
                    const returnForm = document.getElementById(`return-form-${itemId}`);
                    const reason = returnForm.querySelector('select[name="reason"]').value;
                    const comments = returnForm.querySelector('textarea[name="comments"]').value;
                    
                    if (!reason) {
                        showNotification('Please select a return reason');
                        return;
                    }
                    
                    // Send return request
                    fetch('process_return.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `order_id=${orderId}&order_item_id=${itemId}&reason=${encodeURIComponent(reason)}&comments=${encodeURIComponent(comments)}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showNotification(data.success);
                            // Replace return button with status message
                            const actionDiv = button.closest('.item-actions');
                            actionDiv.innerHTML = '<div class="return-status">Return requested</div>';
                        } else {
                            showNotification(data.error || 'Error processing return');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showNotification('Error processing return');
                    });
                });
            });
            
            // Show notification function
            function showNotification(message) {
                const notification = document.getElementById('notification');
                if (!notification) return;
                
                notification.textContent = message;
                notification.classList.add('show');
                notification.style.display = 'block';
                
                setTimeout(() => {
                    notification.classList.remove('show');
                    setTimeout(() => {
                        notification.style.display = 'none';
                    }, 300);
                }, 3000);
            }
        });
    </script>
</body>
</html>