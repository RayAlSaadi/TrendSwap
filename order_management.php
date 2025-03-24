<?php
session_start();
require 'db_connect.php';

// Check if user is admin
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Super Admin', 'Manager'])) {
    header("Location: admin_login.php");
    exit();
}

// Process status updates
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

// Process view order details
$view_order_id = isset($_GET['view_order']) ? $_GET['view_order'] : null;
$order_details = null;
$order_items = null;

if ($view_order_id) {
    // Get order details
    $order_query = $conn->prepare("SELECT o.*, u.first_name, u.last_name, u.email, u.address
                                  FROM orders o
                                  JOIN users u ON o.user_id = u.user_id
                                  WHERE o.order_id = ?");
    $order_query->bind_param("i", $view_order_id);
    $order_query->execute();
    $order_details = $order_query->get_result()->fetch_assoc();
    
    // Get order items
    if ($order_details) {
        $items_query = $conn->prepare("SELECT oi.*, p.name as product_name, p.image
                                      FROM order_items oi
                                      JOIN products p ON oi.product_id = p.product_id
                                      WHERE oi.order_id = ?");
        $items_query->bind_param("i", $view_order_id);
        $items_query->execute();
        $order_items = $items_query->get_result();
    }
}

// Process filters
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Build query with filters
$orders_query = "SELECT o.*, u.first_name, u.last_name, u.email
                FROM orders o
                JOIN users u ON o.user_id = u.user_id
                WHERE 1=1";

$params = [];
$types = "";

if (!empty($status_filter)) {
    $orders_query .= " AND o.status = ?";
    $params[] = $status_filter;
    $types .= "s";
}

if (!empty($date_from)) {
    $orders_query .= " AND o.order_date >= ?";
    $params[] = $date_from . " 00:00:00";
    $types .= "s";
}

if (!empty($date_to)) {
    $orders_query .= " AND o.order_date <= ?";
    $params[] = $date_to . " 23:59:59";
    $types .= "s";
}

if (!empty($search)) {
    $orders_query .= " AND (u.first_name LIKE ? OR u.last_name LIKE ? OR u.email LIKE ? OR o.order_id LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "ssss";
}

$orders_query .= " ORDER BY o.order_date DESC";

// Prepare and execute query
$stmt = $conn->prepare($orders_query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$orders_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management - TrendSwap</title>
    <link rel="stylesheet" href="css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #f9f9f9;
            padding-top: 20px;
        }
        .order-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .order-header {
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
        .table-container, .order-details-container {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .status-select {
            width: 150px;
        }
        .product-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 4px;
        }
        .customer-info, .order-summary {
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
    
    <div class="order-container">
        <div class="order-header">
            <h1>Order Management</h1>
            <p>View and manage customer orders</p>
        </div>
        
        <div class="admin-nav">
            <a href="admin_dashboard.php" class="btn btn-outline-primary">Dashboard</a>
            <a href="stock_management.php" class="btn btn-outline-primary">Stock Management</a>
            <a href="product_management.php" class="btn btn-outline-primary">Product Management</a>
            <a href="order_management.php" class="btn btn-primary">Order Management</a>
            <a href="customer_management.php" class="btn btn-outline-primary">Customer Management</a>
        </div>
        
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <?php if ($order_details): ?>
            <div class="order-details-container">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Order #<?php echo $order_details['order_id']; ?> Details</h2>
                    <a href="order_management.php" class="btn btn-secondary">Back to All Orders</a>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="customer-info">
                            <h4>Customer Information</h4>
                            <p><strong>Name:</strong> <?php echo htmlspecialchars($order_details['first_name'] . ' ' . $order_details['last_name']); ?></p>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($order_details['email']); ?></p>
                            <p><strong>Address:</strong> <?php echo htmlspecialchars($order_details['address'] ?? 'Not provided'); ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="order-summary">
                            <h4>Order Summary</h4>
                            <p><strong>Order Date:</strong> <?php echo date('F j, Y, g:i a', strtotime($order_details['order_date'])); ?></p>
                            <p><strong>Total Amount:</strong> £<?php echo number_format($order_details['total_amount'], 2); ?></p>
                            <p>
                                <strong>Status:</strong> 
                                <span class="badge badge-<?php echo strtolower($order_details['status']); ?>">
                                    <?php echo $order_details['status']; ?>
                                </span>
                            </p>
                            
                            <form method="post" class="mt-3">
                                <div class="input-group">
                                    <input type="hidden" name="order_id" value="<?php echo $order_details['order_id']; ?>">
                                    <select name="new_status" class="form-select">
                                        <option value="Pending" <?php echo $order_details['status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="Processing" <?php echo $order_details['status'] == 'Processing' ? 'selected' : ''; ?>>Processing</option>
                                        <option value="Shipped" <?php echo $order_details['status'] == 'Shipped' ? 'selected' : ''; ?>>Shipped</option>
                                        <option value="Delivered" <?php echo $order_details['status'] == 'Delivered' ? 'selected' : ''; ?>>Delivered</option>
                                        <option value="Cancelled" <?php echo $order_details['status'] == 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                    </select>
                                    <button type="submit" name="update_status" class="btn btn-primary">Update Status</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <h4 class="mt-4">Order Items</h4>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Size</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($order_items && $order_items->num_rows > 0): ?>
                                <?php while ($item = $order_items->fetch_assoc()): ?>
                                    <tr>
                                        <td>
                                            <?php 
                                            // Get first image if multiple exist
                                            $images = explode(',', $item['image']);
                                            $first_image = $images[0];
                                            ?>
                                            <div class="d-flex align-items-center">
                                                <img src="images/products/<?php echo htmlspecialchars($first_image); ?>" alt="Product Image" class="product-image me-2">
                                                <?php echo htmlspecialchars($item['product_name']); ?>
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($item['size']); ?></td>
                                        <td>£<?php echo number_format($item['price'], 2); ?></td>
                                        <td><?php echo $item['quantity']; ?></td>
                                        <td>£<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center">No items found for this order</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4" class="text-end">Total:</th>
                                <th>£<?php echo number_format($order_details['total_amount'], 2); ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        <?php else: ?>
            <div class="filter-section">
                <form method="get" class="row g-3">
                    <div class="col-md-3">
                        <label for="status" class="form-label">Order Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="Pending" <?php echo $status_filter == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="Processing" <?php echo $status_filter == 'Processing' ? 'selected' : ''; ?>>Processing</option>
                            <option value="Shipped" <?php echo $status_filter == 'Shipped' ? 'selected' : ''; ?>>Shipped</option>
                            <option value="Delivered" <?php echo $status_filter == 'Delivered' ? 'selected' : ''; ?>>Delivered</option>
                            <option value="Cancelled" <?php echo $status_filter == 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="date_from" class="form-label">Date From</label>
                        <input type="date" name="date_from" id="date_from" class="form-control" value="<?php echo htmlspecialchars($date_from); ?>">
                    </div>
                    <div class="col-md-2">
                        <label for="date_to" class="form-label">Date To</label>
                        <input type="date" name="date_to" id="date_to" class="form-control" value="<?php echo htmlspecialchars($date_to); ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="search" class="form-label">Search Orders</label>
                        <input type="text" name="search" id="search" class="form-control" value="<?php echo htmlspecialchars($search); ?>" placeholder="Order ID, customer name, email">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                    </div>
                </form>
            </div>
            
            <div class="table-container">
                <h2>Orders List</h2>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Email</th>
                                <th>Date</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($orders_result && $orders_result->num_rows > 0): ?>
                                <?php while ($order = $orders_result->fetch_assoc()): ?>
                                    <tr>
                                        <td>#<?php echo $order['order_id']; ?></td>
                                        <td><?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?></td>
                                        <td><?php echo htmlspecialchars($order['email']); ?></td>
                                        <td><?php echo date('d-m-Y H:i', strtotime($order['order_date'])); ?></td>
                                        <td>£<?php echo number_format($order['total_amount'], 2); ?></td>
                                        <td>
                                            <span class="badge badge-<?php echo strtolower($order['status']); ?>">
                                                <?php echo $order['status']; ?>
                                            </span>
                                        </td>
                                        <td class="d-flex gap-2">
                                            <a href="?view_order=<?php echo $order['order_id']; ?>" class="btn btn-info btn-sm">View Details</a>
                                            <form method="post" class="d-flex">
                                                <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                                <select name="new_status" class="form-select form-select-sm status-select me-1">
                                                    <option value="Pending" <?php echo $order['status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                                    <option value="Processing" <?php echo $order['status'] == 'Processing' ? 'selected' : ''; ?>>Processing</option>
                                                    <option value="Shipped" <?php echo $order['status'] == 'Shipped' ? 'selected' : ''; ?>>Shipped</option>
                                                    <option value="Delivered" <?php echo $order['status'] == 'Delivered' ? 'selected' : ''; ?>>Delivered</option>
                                                    <option value="Cancelled" <?php echo $order['status'] == 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                                </select>
                                                <button type="submit" name="update_status" class="btn btn-primary btn-sm">Update</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center">No orders found</td>
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