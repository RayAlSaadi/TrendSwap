<?php
session_start();
include 'db_connect.php';


if (!isset($_SESSION["user_id"]) || $_SESSION["user_id"] != 30) {
    header("Location: login.php");
    exit();
}


if (isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['new_status'];
    
    $update_query = $conn->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
    $update_query->bind_param("si", $new_status, $order_id);
    
    if ($update_query->execute()) {
        $success_message = "Order #$order_id status updated to $new_status";
    } else {
        $error_message = "Failed to update order status";
    }
}


$orders_query = "SELECT o.*, u.first_name, u.last_name FROM orders o 
                JOIN users u ON o.user_id = u.user_id 
                ORDER BY o.order_date DESC";
$orders_result = $conn->query($orders_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Order Management</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }
        
        .orders-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        
        .orders-table th, .orders-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .orders-table th {
            background-color: #f5f5f5;
        }
        
        .status-form {
            display: flex;
            gap: 10px;
        }
        
        .status-select {
            padding: 8px;
        }
        
        .update-btn {
            padding: 8px 12px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }
        
        .success-message {
            background-color: #e8f5e9;
            color: #2e7d32;
            padding: 10px;
            border-radius: 3px;
            margin-bottom: 20px;
        }
        
        .error-message {
            background-color: #ffebee;
            color: #c62828;
            padding: 10px;
            border-radius: 3px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <?php include 'phpLogic/header.php'; ?>
    
    <div class="container">
        <h1>Order Management</h1>
        
        <?php if (isset($success_message)): ?>
            <div class="success-message"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <table class="orders-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Total</th>
                    <th>Current Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($orders_result->num_rows > 0): ?>
                    <?php while ($order = $orders_result->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo $order['order_id']; ?></td>
                            <td><?php echo $order['first_name'] . ' ' . $order['last_name']; ?></td>
                            <td><?php echo date('F j, Y', strtotime($order['order_date'])); ?></td>
                            <td>£<?php echo number_format($order['total_amount'], 2); ?></td>
                            <td><?php echo $order['status']; ?></td>
                            <td>
                                <form method="post" class="status-form">
                                    <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                    <select name="new_status" class="status-select">
                                        <option value="Pending" <?php echo $order['status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="Processing" <?php echo $order['status'] == 'Processing' ? 'selected' : ''; ?>>Processing</option>
                                        <option value="Shipped" <?php echo $order['status'] == 'Shipped' ? 'selected' : ''; ?>>Shipped</option>
                                        <option value="Delivered" <?php echo $order['status'] == 'Delivered' ? 'selected' : ''; ?>>Delivered</option>
                                        <option value="Cancelled" <?php echo $order['status'] == 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                    </select>
                                    <button type="submit" name="update_status" class="update-btn">Update</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">No orders found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <?php include 'phpLogic/footer.php'; ?>
    <?php include 'moonoverlay.php'; ?>
</body>
</html>