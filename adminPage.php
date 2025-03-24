<?php
session_start();
$is_logged_in = isset($_SESSION['user_id']);
$is_admin = isset($_SESSION['role']) && in_array($_SESSION['role'], ['Super Admin', 'Manager']);
require 'db_connect.php';
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
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Montserrat', sans-serif; }
        .admin-container { padding: 20px; max-width: 1200px; margin: auto; }
        .admin-header { text-align: center; margin-bottom: 20px; }
        .admin-section { margin-top: 20px; }
        .table-responsive { overflow-x: auto; }
    </style>
</head>
<body>
    <?php include 'phpLogic/header.php'; ?>
    <div class="container admin-container">
        <h2 class="admin-header">Admin Dashboard</h2>
        <?php if ($is_admin): ?>
        <!-- User Section -->
        <div class="admin-section">
            <h3>Registered Users</h3>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>User ID</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Email</th>
                            <th>Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Fetch users from database
                        $sql = "SELECT user_id, first_name, last_name, email, address FROM users";
                        $result = $conn->query($sql);
                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                echo "<tr>
                                    <td>" . htmlspecialchars($row['user_id']) . "</td>
                                    <td>" . htmlspecialchars($row['first_name']) . "</td>
                                    <td>" . htmlspecialchars($row['last_name']) . "</td>
                                    <td>" . htmlspecialchars($row['email']) . "</td>
                                    <td>" . htmlspecialchars($row['address']) . "</td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' class='text-center'>No users found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="admin-section">
            <h3>Product Inventory</h3>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Product ID</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Size</th>
                            <th>Color</th>
                            <th>Description</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT product_id, name, price, stock, size, color, description FROM products";
                        $result = $conn->query($sql);
                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                $stock_status = ($row['stock'] <= 0) ? "Out of Stock" : (($row['stock'] <= 5) ? "Low Stock" : "In Stock");
                                echo "<tr>
                                    <td>" . htmlspecialchars($row['product_id']) . "</td>
                                    <td>" . htmlspecialchars($row['name']) . "</td>
                                    <td>" . htmlspecialchars($row['price']) . "</td>
                                    <td>" . htmlspecialchars($row['stock']) . " ($stock_status)</td>
                                    <td>" . htmlspecialchars($row['size']) . "</td>
                                    <td>" . htmlspecialchars($row['color']) . "</td>
                                    <td>" . htmlspecialchars($row['description']) . "</td>
                                    <td>
                                        <a href='edit_product.php?product_id=" . $row['product_id'] . "' class='btn btn-warning btn-sm'>Edit</a>
                                        <a href='delete_product.php?product_id=" . $row['product_id'] . "' class='btn btn-danger btn-sm'>Delete</a>
                                    </td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='8' class='text-center'>No products found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="admin-section">
            <h3>Low Stock Alerts</h3>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Product ID</th>
                            <th>Name</th>
                            <th>Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT product_id, name, stock FROM products WHERE stock <= 5"; // Adjust threshold here
                        $result = $conn->query($sql);
                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                echo "<tr>
                                    <td>" . htmlspecialchars($row['product_id']) . "</td>
                                    <td>" . htmlspecialchars($row['name']) . "</td>
                                    <td>" . htmlspecialchars($row['stock']) . "</td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='3' class='text-center'>No low stock items</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php else: ?>
            <p class="text-center text-danger">Access Denied. Admins only.</p>
        <?php endif; ?>
    </div>
    <?php include 'phpLogic/footer.php'; ?>
</body>
</html>
