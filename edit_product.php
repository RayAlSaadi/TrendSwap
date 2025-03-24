<?php
session_start();
$is_logged_in = isset($_SESSION['user_id']);
$is_admin = isset($_SESSION['role']) && in_array($_SESSION['role'], ['Super Admin', 'Manager']);
require 'db_connect.php';
if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];
    $sql = "SELECT * FROM products WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
    } else {
        die("Product not found.");
    }
} else {
    die("Invalid product ID.");
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $size = $_POST['size'];
    $color = $_POST['color'];
    $description = $_POST['description'];
    $sql = "UPDATE products SET name = ?, price = ?, stock = ?, size = ?, color = ?, description = ? WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sdisssi", $name, $price, $stock, $size, $color, $description, $product_id);
    if ($stmt->execute()) {
        header('Location: adminPage.php');
        exit;
    } else {
        echo "Error updating product.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - TrendSwap</title>
    <link rel="stylesheet" href="css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <?php include 'phpLogic/header.php'; ?>
    <div class="container">
        <h2>Edit Product</h2>
        <form method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Product Name</label>
                <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Price</label>
                <input type="number" step="0.01" class="form-control" id="price" name="price" value="<?= htmlspecialchars($product['price']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="stock" class="form-label">Stock</label>
                <input type="number" class="form-control" id="stock" name="stock" value="<?= htmlspecialchars($product['stock']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="size" class="form-label">Size</label>
                <input type="text" class="form-control" id="size" name="size" value="<?= htmlspecialchars($product['size']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="color" class="form-label">Color</label>
                <input type="text" class="form-control" id="color" name="color" value="<?= htmlspecialchars($product['color']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="4"><?= htmlspecialchars($product['description']) ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
    </div>
    <?php include 'phpLogic/footer.php'; ?>
</body>
</html>

