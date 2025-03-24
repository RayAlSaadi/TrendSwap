<?php
include 'db_connect.php'; // Include your database connection file

// Check if category_id is set and valid
if (isset($_GET['category_id']) && !empty($_GET['category_id'])) {
    $category_id = $_GET['category_id'];

    // Query to get products by category_id
    $sql = "SELECT product_id, name FROM products WHERE category_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }

    // Return the products as JSON
    echo json_encode($products);
} else {
    // Return an empty array if category_id is not set
    echo json_encode([]);
}
?>
