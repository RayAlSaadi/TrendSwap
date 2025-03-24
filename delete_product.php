<?php
session_start();
$is_logged_in = isset($_SESSION['user_id']);
$is_admin = isset($_SESSION['role']) && in_array($_SESSION['role'], ['Super Admin', 'Manager']);
require 'db_connect.php';
if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];
    $sql = "DELETE FROM products WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    if ($stmt->execute()) {
        header('Location: admin_dashboard.php');  // Redirect to the admin dashboard after deletion
        exit;
    } else {
        echo "Error deleting product.";
    }
} else {
    die("Invalid product ID.");
}
