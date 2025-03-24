<?php
session_start();
require 'db_connect.php';

// Check if user is admin
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Super Admin', 'Manager'])) {
    header("Location: admin_login.php");
    exit();
}

// Process delete product
if (isset($_POST['delete_product'])) {
    $product_id = $_POST['product_id'];
    
    // Start transaction
    $conn->begin_transaction();
    try {
        // Delete from product_sizes
        $stmt = $conn->prepare("DELETE FROM product_sizes WHERE product_id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        
        // Delete from product_images
        $stmt = $conn->prepare("DELETE FROM product_images WHERE product_id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        
        // Delete the product
        $stmt = $conn->prepare("DELETE FROM products WHERE product_id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        
        // Commit the transaction
        $conn->commit();
        $success_message = "Product deleted successfully";
    } catch (Exception $e) {
        // Rollback in case of error
        $conn->rollback();
        $error_message = "Error deleting product: " . $e->getMessage();
    }
}

// Process category filter
$category_filter = isset($_GET['category']) ? $_GET['category'] : '';
$search_term = isset($_GET['search']) ? $_GET['search'] : '';

// Get all categories for filter
$categories_query = "SELECT category_id, name FROM categories ORDER BY name";
$categories_result = $conn->query($categories_query);

// Build the products query with filters
$products_query = "SELECT p.product_id, p.name, p.price, 
                          (SELECT SUM(ps.stock) FROM product_sizes ps WHERE ps.product_id = p.product_id) as total_stock,
                          p.size, p.color, c.name as category_name, p.description, p.is_featured, p.low_stock_threshold
                   FROM products p
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

$products_query .= " ORDER BY p.product_id DESC";

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
    <title>Product Management - TrendSwap</title>
    <link rel="stylesheet" href="css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #f9f9f9;
            padding-top: 20px;
        }
        .product-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .product-header {
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
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
        .product-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 4px;
        }
        .action-btns {
            display: flex;
            gap: 5px;
        }
        .truncate {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 200px;
        }
        .stock-status {
            font-weight: bold;
        }
        .low-stock {
            color: #ffc107;
        }
        .out-of-stock {
            color: #dc3545;
        }
        .description-cell {
            max-width: 200px;
        }
    </style>
</head>
<body>
    <?php include 'phpLogic/header.php'; ?>
    
    <div class="product-container">
        <div class="product-header">
            <div>
                <h1>Product Management</h1>
                <p>Manage your product catalog</p>
            </div>
            <a href="add_product.php" class="btn btn-success">Add New Product</a>
        </div>
        
        <div class="admin-nav">
            <a href="admin_dashboard.php" class="btn btn-outline-primary">Dashboard</a>
            <a href="stock_management.php" class="btn btn-outline-primary">Stock Management</a>
            <a href="product_management.php" class="btn btn-primary">Product Management</a>
            <a href="order_management.php" class="btn btn-outline-primary">Order Management</a>
            <a href="customer_management.php" class="btn btn-outline-primary">Customer Management</a>
        </div>
        
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>