<?php
session_start();
require 'db_connect.php';

// Check if user is admin
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Super Admin', 'Manager'])) {
    header("Location: adminLogin.php");
    exit();
}

// Get quick stats
$stats = [
    'products' => 0,
    'customers' => 0,
    'orders' => 0,
    'low_stock_items' => 0
];

// Count products
$result = $conn->query("SELECT COUNT(*) as count FROM products");
if ($result) {
    $stats['products'] = $result->fetch_assoc()['count'];
}

// Count customers
$result = $conn->query("SELECT COUNT(*) as count FROM users");
if ($result) {
    $stats['customers'] = $result->fetch_assoc()['count'];
}

// Count orders
$result = $conn->query("SELECT COUNT(*) as count FROM orders");
if ($result) {
    $stats['orders'] = $result->fetch_assoc()['count'];
}

// Count low stock items
$result = $conn->query("SELECT COUNT(*) as count FROM product_sizes ps JOIN products p ON ps.product_id = p.product_id WHERE ps.stock <= p.low_stock_threshold");
if ($result) {
    $stats['low_stock_items'] = $result->fetch_assoc()['count'];
}

// Get recent orders
$recent_orders_query = "SELECT o.order_id, u.first_name, u.last_name, o.order_date, o.total_amount, o.status 
                        FROM orders o
                        JOIN users u ON o.user_id = u.user_id
                        ORDER BY o.order_date DESC
                        LIMIT 5";
$recent_orders_result = $conn->query($recent_orders_query);

// Get low stock products
$low_stock_query = "SELECT p.product_id, p.name, ps.size, ps.stock, p.low_stock_threshold 
                    FROM products p
                    JOIN product_sizes ps ON p.product_id = ps.product_id
                    WHERE ps.stock <= p.low_stock_threshold
                    ORDER BY ps.stock ASC
                    LIMIT 5";
$low_stock_result = $conn->query($low_stock_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - TrendSwap</title>
    <link rel="stylesheet" href="css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #f9f9f9;
            padding-top: 20px;
        }
        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .dashboard-header {
            margin-bottom: 30px;
        }
        .dashboard-card {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .stat-card h3 {
            font-size: 36px;
            font-weight: bold;
            margin: 10px 0;
        }
        .stat-card p {
            color: #666;
            font-size: 14px;
            margin-bottom: 0;
        }
        .admin-nav {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 30px;
        }
        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
        }
        .low-stock {
            background-color: #fff3cd;
        }
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <?php include 'phpLogic/header.php'; ?>
    
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1>Admin Dashboard</h1>
            <p>Welcome, <?php echo htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?>!</p>
        </div>
        
        <div class="admin-nav">
            <a href="admin_dashboard.php" class="btn btn-primary">Dashboard</a>
            <a href="stock_management.php" class="btn btn-outline-primary">Stock Management</a>
            <a href="product_management.php" class="btn btn-outline-primary">Product Management</a>
            <a href="order_management.php" class="btn btn-outline-primary">Order Management</a>
            <a href="customer_management.php" class="btn btn-outline-primary">Customer Management</a>
        </div>
        
        <div class="stats-container">
            <div class="stat-card">
                <p>Total Products</p>
                <h3><?php echo $stats['products']; ?></h3>
                <a href="product_management.php" class="btn btn-sm btn-outline-secondary mt-2">View All</a>
            </div>
            <div class="stat-card">
                <p>Total Customers</p>
                <h3><?php echo $stats['customers']; ?></h3>
                <a href="customer_management.php" class="btn btn-sm btn-outline-secondary mt-2">View All</a>
            </div>
            <div class="stat-card">
                <p>Total Orders</p>
                <h3><?php echo $stats['orders']; ?></h3>
                <a href="order_management.php" class="btn btn-sm btn-outline-secondary mt-2">View All</a>
            </div>
            <div class="stat-card">
                <p>Low Stock Items</p>
                <h3><?php echo $stats['low_stock_items']; ?></h3>
                <a href="stock_management.php" class="btn btn-sm btn-outline-secondary mt-2">View All</a>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="dashboard-card">
                    <div class="section-header">
                        <h2>Recent Orders</h2>
                        <a href="order_management.php" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Customer</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($recent_orders_result && $recent_orders_result->num_rows > 0): ?>
                                    <?php while ($order = $recent_orders_result->fetch_assoc()): ?>
                                        <tr>
                                            <td>#<?php echo $order['order_id']; ?></td>
                                            <td><?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?></td>
                                            <td><?php echo date('d-m-Y', strtotime($order['order_date'])); ?></td>
                                            <td>£<?php echo number_format($order['total_amount'], 2); ?></td>
                                            <td>
                                                <span class="badge bg-<?php 
                                                    echo match($order['status']) {
                                                        'Pending' => 'warning',
                                                        'Processing' => 'info',
                                                        'Shipped' => 'primary',
                                                        'Delivered' => 'success',
                                                        'Cancelled' => 'danger',
                                                        default => 'secondary'
                                                    };
                                                ?>">
                                                    <?php echo $order['status']; ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center">No recent orders found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="dashboard-card">
                    <div class="section-header">
                        <h2>Low Stock Alerts</h2>
                        <a href="stock_management.php" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Size</th>
                                    <th>Stock</th>
                                    <th>Threshold</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($low_stock_result && $low_stock_result->num_rows > 0): ?>
                                    <?php while ($product = $low_stock_result->fetch_assoc()): ?>
                                        <tr class="<?php echo ($product['stock'] == 0) ? 'table-danger' : 'table-warning'; ?>">
                                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                                            <td><?php echo htmlspecialchars($product['size']); ?></td>
                                            <td><?php echo $product['stock']; ?></td>
                                            <td><?php echo $product['low_stock_threshold']; ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center">No low stock items found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'phpLogic/footer.php'; ?>
</body>
</html>