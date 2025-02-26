<?php
include 'db_connect.php';

$product_id = $_GET["id"];
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo $product["name"]; ?></title>
</head>
<body>
    <h1><?php echo $product["name"]; ?></h1>
    <p><?php echo $product["description"]; ?></p>
    <p>Price: £<?php echo $product["price"]; ?></p>
    <button class="add-to-cart" data-id="<?php echo $product["id"]; ?>">Add to Cart</button>
    <button class="add-to-wishlist" data-id="<?php echo $product["id"]; ?>">Add to Wishlist</button>
</body>
</html>
