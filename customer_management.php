<?php
session_start();
require 'db_connect.php';

// Check if user is admin
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Super Admin', 'Manager'])) {
    header("Location: admin_login.php");
    exit();
}

// Process search/filter
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Get all customers with filters
$customers_query = "SELECT u.*, 
                          (SELECT COUNT(*) FROM orders WHERE user_id = u.user_id) as order_count,
                          (SELECT SUM(total_amount) FROM orders WHERE user_id = u.user_id) as total_spent
                    FROM users u
                    WHERE 1=1";

$params = [];
$types = "";

if (!empty($search)) {
    $customers_query .= " AND (u.first_name LIKE ? OR u.last_name LIKE ? OR u.email LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "sss";
}

$customers_query .= " ORDER BY u.user_id DESC";

// Prepare and execute the query
$stmt = $conn->prepare($customers_query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$customers_result = $stmt->get_result();

// View customer details
$view_customer_id = isset($_GET['view_customer']) ? $_GET['view_customer'] : null;
$customer_details = null;
$customer_orders = null;

if ($view_customer_id) {
    // Get customer details
    $customer_query = $conn->prepare("SELECT u.*, 
                                           (SELECT COUNT(*) FROM orders WHERE user_id = u.user_id) as order_count,
                                           (SELECT SUM(total_amount) FROM orders WHERE user_id = u.user_id) as total_spent
                                     FROM users u
                                     WHERE u.user_id = ?");
    $customer_query->bind_param("i", $view_customer_id);
    $customer_query->execute();
    $customer_details = $customer_query->get_result()->fetch_assoc();
    
    // Get customer orders
    if ($customer_details) {
        $orders_query = $conn->prepare("SELECT o.*, COUNT(oi.order_item_id) as item_count
                                       FROM orders o
                                       LEFT JOIN order_items oi ON o.order_id = oi.order_id
                                       WHERE o.user_id = ?
                                       GROUP BY o.order_id
                                       ORDER BY o.order_date DESC");
        $orders_query->bind_param("i", $view_customer_id);
        $orders_query->execute();
        $customer_orders = $orders_query->get_result();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Management - TrendSwap</title>
    <link rel="stylesheet" href="css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #f9f9f9;
            padding-top: 20px;
        }
        .customer-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .customer-header {
            margin-bottom: 30px;
        }
        .admin-nav {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 30px;
        }
        .filter-section {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .table-container, .customer-details-container {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .customer-info, .customer-stats {
            margin-bottom: 20px;
        }
        .badge-pending { background-color: #ffc107; }
        .badge-processing { background-color: #17a2b8; }
        .badge-shipped { background-color: #007bff; }
        .badge-delivered { background-color: #28a745; }
        .badge-cancelled { background-color: #dc3545; }
    </style>
</head>
<body>
    <?php include 'phpLogic/header.php'; ?>
    
    <div class="customer-container">
        <div class="customer-header">
            <h1>Customer Management</h1>
            <p>View and manage customer accounts</p>
        </div>
        
        <div class="admin-nav">
            <a href="admin_dashboard.php" class="btn btn-outline-primary">Dashboard</a>
            <a href="stock_management.php" class="btn btn-outline-primary">Stock Management</a>
            <a href="product_management.php" class="btn btn-outline-primary">Product Management</a>
            <a href="order_management.php" class="btn btn-outline-primary">Order Management</a>
            <a href="customer_management.php" class="btn btn-primary">Customer Management</a>
        </div>
        
        <?php if ($customer_details): ?>
            <div class="customer-details-container">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Customer Details</h2>
                    <a href="customer_management.php" class="btn btn-secondary">Back to All Customers</a>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="customer-info">
                            <h4>Personal Information</h4>
                            <p><strong>Name:</strong> <?php echo htmlspecialchars($customer_details['first_name'] . ' ' . $customer_details['last_name']); ?></p>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($customer_details['email']); ?></p>
                            <p><strong>Address:</strong> <?php echo htmlspecialchars($customer_details['address'] ?? 'Not provided'); ?></p>
                            <p><strong>Joined:</strong> <?php echo date('F j, Y', strtotime($customer_details['created_at'])); ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="customer-stats">
                            <h4>Customer Statistics</h4>
                            <p><strong>Total Orders:</strong> <?php echo $customer_details['order_count']; ?></p>
                            <p><strong>Total Spent:</strong> £<?php echo number_format($customer_details['total_spent'] ?? 0, 2); ?></p>
                        </div>
                    </div>
                </div>
                
                <h4 class="mt-4">Order History</h4>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Date</th>
                                <th>Items</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($customer_orders && $customer_orders->num_rows > 0): ?>
                                <?php while ($order = $customer_orders->fetch_assoc()): ?>
                                    <tr>
                                        <td>#<?php echo $order['order_id']; ?></td>
                                        <td><?php echo date('d-m-Y H:i', strtotime($order['order_date'])); ?></td>
                                        <td><?php echo $order['item_count']; ?> items</td>
                                        <td>£<?php echo number_format($order['total_amount'], 2); ?></td>
                                        <td>
                                            <span class="badge badge-<?php echo strtolower($order['status']); ?>">
                                                <?php echo $order['status']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="order_management.php?view_order=<?php echo $order['order_id']; ?>" class="btn btn-info btn-sm">View Order</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">No orders found for this customer</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php else: ?>
            <div class="filter-section">
                <form method="get" class="row g-3">
                    <div class="col-md-9">
                        <label for="search" class="form-label">Search Customers</label>
                        <input type="text" name="search" id="search" class="form-control" value="<?php echo htmlspecialchars($search); ?>" placeholder="Name, email, address">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Search</button>
                    </div>
                </form>
            </div>
            
            <div class="table-container">
                <h2>Customers List</h2>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Address</th>
                                <th>Joined</th>
                                <th>Orders</th>
                                <th>Total Spent</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($customers_result && $customers_result->num_rows > 0): ?>
                                <?php while ($customer = $customers_result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $customer['user_id']; ?></td>
                                        <td><?php echo htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']); ?></td>
                                        <td><?php echo htmlspecialchars($customer['email']); ?></td>
                                        <td><?php echo htmlspecialchars($customer['address'] ?? 'Not provided'); ?></td>
                                        <td><?php echo date('d-m-Y', strtotime($customer['created_at'])); ?></td>
                                        <td><?php echo $customer['order_count']; ?></td>
                                        <td>£<?php echo number_format($customer['total_spent'] ?? 0, 2); ?></td>
                                        <td>
                                            <a href="?view_customer=<?php echo $customer['user_id']; ?>" class="btn btn-info btn-sm">View Details</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center">No customers found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <?php include 'phpLogic/footer.php'; ?>
</body>
</html>