<?php
session_start();
require 'db_connect.php';

// Check if user is admin
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Super Admin', 'Manager'])) {
    header("Location: admin_login.php");
    exit();
}

// Process stock update
if (isset($_POST['update_stock'])) {
    $product_id = $_POST['product_id'];
    $size = $_POST['size'];
    $new_stock = $_POST['stock'];
    
    // Update stock in product_sizes table
    $stmt = $conn->prepare("UPDATE product_sizes SET stock = ? WHERE product_id = ? AND size = ?");
    $stmt->bind_param("iis", $new_stock, $product_id, $size);
    
    if ($stmt->execute()) {
        // Update total stock in products table
        $update_total = $conn->prepare("UPDATE products p SET p.stock = 
            (SELECT SUM(stock) FROM product_sizes WHERE product_id = ?) 
            WHERE product_id = ?");
        $update_total->bind_param("ii", $product_id, $product_id);
        $update_total->execute();
        
        $success_message = "Stock updated successfully for product ID: $product_id, Size: $size";
    } else {
        $error_message = "Failed to update stock. Error: " . $conn->error;
    }
}

// Process category filter
$category_filter = isset($_GET['category']) ? $_GET['category'] : '';
$search_term = isset($_GET['search']) ? $_GET['search'] : '';

// Get all categories for filter
$categories_query = "SELECT category_id, name FROM categories ORDER BY name";
$categories_result = $conn->query($categories_query);

// Build the products query with filters
$products_query = "SELECT p.product_id, p.name, c.name as category_name, 
                          ps.size, ps.stock, p.low_stock_threshold
                   FROM products p
                   JOIN product_sizes ps ON p.product_id = ps.product_id
                   LEFT JOIN categories c ON p.category_id = c.category_id
                   WHERE 1=1";

$params = [];
$types = "";

if (!empty($category_filter)) {
    $products_query .= " AND p.category_id = ?";
    $params[] = $category_filter;
    $types .= "i";
}

if (!empty($search_term)) {
    $products_query .= " AND (p.name LIKE ? OR p.description LIKE ?)";
    $search_param = "%$search_term%";
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "ss";
}

$products_query .= " ORDER BY p.name, CASE ps.size 
                                      WHEN 'S' THEN 1 
                                      WHEN 'M' THEN 2 
                                      WHEN 'L' THEN 3 
                                      WHEN 'One Size' THEN 4 
                                    END";

// Prepare and execute the query
$stmt = $conn->prepare($products_query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$products_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Management - TrendSwap</title>
    <link rel="stylesheet" href="css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #f9f9f9;
            padding-top: 20px;
        }
        .stock-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .stock-header {
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
        .table-container {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .stock-input {
            width: 80px;
        }
        .table-responsive {
            margin-top: 15px;
        }
        .action-btns {
            display: flex;
            gap: 5px;
        }
        .low-stock {
            background-color: #fff3cd;
        }
        .out-of-stock {
            background-color: #f8d7da;
        }
    </style>
</head>
<body>
    <?php include 'phpLogic/header.php'; ?>
    
    <div class="stock-container">
        <div class="stock-header">
            <h1>Stock Management</h1>
            <p>Update and monitor inventory levels for all products by size</p>
        </div>
        
        <div class="admin-nav">
            <a href="admin_dashboard.php" class="btn btn-outline-primary">Dashboard</a>
            <a href="stock_management.php" class="btn btn-primary">Stock Management</a>
            <a href="product_management.php" class="btn btn-outline-primary">Product Management</a>
            <a href="order_management.php" class="btn btn-outline-primary">Order Management</a>
            <a href="customer_management.php" class="btn btn-outline-primary">Customer Management</a>
        </div>
        
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <div class="filter-section">
            <form method="get" class="row g-3">
                <div class="col-md-4">
                    <label for="category" class="form-label">Filter by Category</label>
                    <select name="category" id="category" class="form-select">
                        <option value="">All Categories</option>
                        <?php if ($categories_result): ?>
                            <?php while ($category = $categories_result->fetch_assoc()): ?>
                                <option value="<?php echo $category['category_id']; ?>" <?php echo ($category_filter == $category['category_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="search" class="form-label">Search Products</label>
                    <input type="text" name="search" id="search" class="form-control" value="<?php echo htmlspecialchars($search_term); ?>" placeholder="Enter product name or description">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                </div>
            </form>
        </div>
        
        <div class="table-container">
            <h2>Inventory by Size</h2>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>Size</th>
                            <th>Stock</th>
                            <th>Threshold</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($products_result && $products_result->num_rows > 0): ?>
                            <?php 
                            $current_product = null;
                            $row_class = '';
                            ?>
                            <?php while ($product = $products_result->fetch_assoc()): ?>
                                <?php 
                                // Determine row class based on stock level
                                if ($product['stock'] == 0) {
                                    $row_class = 'table-danger';
                                    $status = 'Out of Stock';
                                } elseif ($product['stock'] <= $product['low_stock_threshold']) {
                                    $row_class = 'table-warning';
                                    $status = 'Low Stock';
                                } else {
                                    $row_class = '';
                                    $status = 'In Stock';
                                }
                                ?>
                                <tr class="<?php echo $row_class; ?>">
                                    <td><?php echo $product['product_id']; ?></td>
                                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                                    <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                                    <td><?php echo htmlspecialchars($product['size']); ?></td>
                                    <td>
                                        <form method="post" class="d-flex align-items-center">
                                            <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                            <input type="hidden" name="size" value="<?php echo $product['size']; ?>">
                                            <input type="number" name="stock" value="<?php echo $product['stock']; ?>" min="0" class="form-control form-control-sm stock-input">
                                            <button type="submit" name="update_stock" class="btn btn-primary btn-sm ms-2">Update</button>
                                        </form>
                                    </td>
                                    <td><?php echo $product['low_stock_threshold']; ?></td>
                                    <td><?php echo $status; ?></td>
                                    <td>
                                        <div class="action-btns">
                                            <a href="edit_product.php?id=<?php echo $product['product_id']; ?>" class="btn btn-outline-secondary btn-sm">Edit Product</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center">No products found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <?php include 'phpLogic/footer.php'; ?>
</body>
</html>