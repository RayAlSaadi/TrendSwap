<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db_connect.php'; 
header('Content-Type: application/json'); 

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "User not logged in"]);
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
$size = isset($_POST['size']) ? $_POST['size'] : '';

if ($product_id === 0) {
    echo json_encode(["error" => "Invalid product ID"]);
    exit;
}


if (empty($size)) {
    $category_check = $conn->prepare("SELECT category_id FROM products WHERE product_id = ?");
    $category_check->bind_param("i", $product_id);
    $category_check->execute();
    $result = $category_check->get_result();
    $product_data = $result->fetch_assoc();
    
   
    if (in_array($product_data['category_id'], [1, 2, 3])) {
        echo json_encode(["error" => "Please select a size"]);
        exit;
    } else {
       
        $size = "One Size";
    }
}


$stock_check = $conn->prepare("SELECT stock FROM product_sizes WHERE product_id = ? AND size = ?");
$stock_check->bind_param("is", $product_id, $size);
$stock_check->execute();
$stock_result = $stock_check->get_result();

if ($stock_result->num_rows === 0) {
    echo json_encode(["error" => "Selected size is not available"]);
    exit;
}

$stock_data = $stock_result->fetch_assoc();
if ($stock_data['stock'] < $quantity) {
    echo json_encode(["error" => "Not enough stock available. Only " . $stock_data['stock'] . " items left."]);
    exit;
}

//  Check if the user has a cart
$cart_query = $conn->prepare("SELECT cart_id FROM carts WHERE user_id = ?");
$cart_query->bind_param("i", $user_id);
$cart_query->execute();
$cart_result = $cart_query->get_result();
$cart_row = $cart_result->fetch_assoc();

if (!$cart_row) {
   
    $create_cart = $conn->prepare("INSERT INTO carts (user_id, created_at) VALUES (?, NOW())");
    $create_cart->bind_param("i", $user_id);
    $create_cart->execute();
    $cart_id = $create_cart->insert_id;
} else {
    $cart_id = $cart_row['cart_id'];
}


$check_item = $conn->prepare("SELECT cart_item_id, quantity FROM cart_items WHERE cart_id = ? AND product_id = ? AND size = ?");
$check_item->bind_param("iis", $cart_id, $product_id, $size);
$check_item->execute();
$item_result = $check_item->get_result();
$item_row = $item_result->fetch_assoc();

if ($item_row) {
    
    $new_quantity = $item_row['quantity'] + $quantity;
    
    
    if ($new_quantity > $stock_data['stock']) {
        echo json_encode(["error" => "Cannot add more items. Only " . $stock_data['stock'] . " items available in this size."]);
        exit;
    }
    
    $update_item = $conn->prepare("UPDATE cart_items SET quantity = ? WHERE cart_item_id = ?");
    $update_item->bind_param("ii", $new_quantity, $item_row['cart_item_id']);
    $update_item->execute();
} else {
    
    $add_item = $conn->prepare("INSERT INTO cart_items (cart_id, product_id, quantity, size, created_at) VALUES (?, ?, ?, ?, NOW())");
    $add_item->bind_param("iiis", $cart_id, $product_id, $quantity, $size);
    $add_item->execute();
}

echo json_encode(["success" => "Item added to cart"]);
exit;
?>