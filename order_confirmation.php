<?php
session_start();
include 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

// Check if order_id is provided
if (!isset($_GET['order_id']) || empty($_GET['order_id'])) {
    header("Location: index.php");
    exit();
}

$order_id = (int)$_GET['order_id'];

// Get order details
$order_query = $conn->prepare("
    SELECT o.*, u.first_name, u.last_name, u.email 
    FROM orders o
    JOIN users u ON o.user_id = u.user_id
    WHERE o.order_id = ? AND o.user_id = ?
");
$order_query->bind_param("ii", $order_id, $user_id);
$order_query->execute();
$order_result = $order_query->get_result();

// If order not found or doesn't belong to user, redirect
if ($order_result->num_rows === 0) {
    header("Location: index.php");
    exit();
}

$order = $order_result->fetch_assoc();

// Get order items
$item_query = $conn->prepare("
    SELECT oi.*, p.name 
    FROM order_items oi
    JOIN products p ON oi.product_id = p.product_id
    WHERE oi.order_id = ?
");
$item_query->bind_param("i", $order_id);
$item_query->execute();
$items_result = $item_query->get_result();

$order_items = [];
while ($item = $items_result->fetch_assoc()) {
    $order_items[] = $item;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .confirmation-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 0 20px;
        }
        
        .confirmation-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .confirmation-header h1 {
            color: #4CAF50;
        }
        
        .confirmation-header p {
            font-size: 18px;
            margin-top: 10px;
        }
        
        .confirmation-details {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 30px;
        }
        
        .confirmation-section {
            margin-bottom: 20px;
        }
        
        .confirmation-section h2 {
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        
        .detail-row {
            display: flex;
            margin-bottom: 10px;
        }
        
        .detail-label {
            flex: 1;
            font-weight: bold;
        }
        
        .detail-value {
            flex: 2;
        }
        
        .order-items {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .order-items th {
            background-color: #f0f0f0;
            padding: 10px;
            text-align: left;
        }
        
        .order-items td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .continue-shopping {
            display: block;
            width: 200px;
            margin: 30px auto;
            background-color: #4CAF50;
            color: white;
            text-align: center;
            padding: 12px 20px;
            text-decoration: none;
            border-radius: 4px;
        }
        
        .continue-shopping:hover {
            background-color: #45a049;
        }
        
        .order-summary {
            margin-top: 20px;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 5px 0;
        }
        
        .summary-total {
            font-weight: bold;
            font-size: 18px;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <!-- Include header.php -->
    <?php include 'phpLogic/header.php'; ?>

    <div class="confirmation-container">
        <div class="confirmation-header">
            <h1>Thank You for Your Order!</h1>
            <p>Your order has been successfully placed and is being processed.</p>
            <p>Order #<?php echo $order_id; ?></p>
            <p>A confirmation email has been sent to <?php echo htmlspecialchars($order['email']); ?></p>
        </div>

        <div class="confirmation-details">
            <div class="confirmation-section">
                <h2>Order Details</h2>
                <div class="detail-row">
                    <div class="detail-label">Order Date:</div>
                    <div class="detail-value"><?php echo date('F j, Y, g:i a', strtotime($order['order_date'])); ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Order Status:</div>
                    <div class="detail-value"><?php echo htmlspecialchars($order['status']); ?></div>
                </div>
            </div>

            <div class="confirmation-section">
                <h2>Customer Information</h2>
                <div class="detail-row">
                    <div class="detail-label">Name:</div>
                    <div class="detail-value"><?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Email:</div>
                    <div class="detail-value"><?php echo htmlspecialchars($order['email']); ?></div>
                </div>
            </div>

            <div class="confirmation-section">
                <h2>Order Summary</h2>
                <table class="order-items">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($order_items as $item): ?>
                            <tr>
                                <td>
                                    <?php echo htmlspecialchars($item['name']); ?>
                                    <?php if (!empty($item['size'])): ?>
                                        <small>(Size: <?php echo htmlspecialchars($item['size']); ?>)</small>
                                    <?php endif; ?>
                                </td>
                                <td>£<?php echo number_format($item['price'], 2); ?></td>
                                <td><?php echo $item['quantity']; ?></td>
                                <td>£<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="order-summary">
                    <div class="summary-row">
                        <span>Subtotal:</span>
                        <span>£<?php echo number_format($order['total_amount'], 2); ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Shipping:</span>
                        <span>Free</span>
                    </div>
                    <div class="summary-row summary-total">
                        <span>Total:</span>
                        <span>£<?php echo number_format($order['total_amount'], 2); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <a href="index.php" class="continue-shopping">Continue Shopping</a>
    </div>

    <!-- Include footer.php -->
    <?php include 'phpLogic/footer.php'; ?>

    <?php include 'moonoverlay.php'; ?>
    
    <script src="js/cart.js"></script>
</body>
</html>