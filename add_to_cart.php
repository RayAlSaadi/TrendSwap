
<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db_connect.php'; // Ensure this is correct

header('Content-Type: application/json'); // Ensures JSON output

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "User not logged in"]);
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

if ($product_id === 0) {
    echo json_encode(["error" => "Invalid product ID"]);
    exit;
}

// 1️⃣ Check if the user has a cart
$cart_query = $conn->prepare("SELECT cart_id FROM carts WHERE user_id = ?");
$cart_query->bind_param("i", $user_id);
$cart_query->execute();
$cart_result = $cart_query->get_result();
$cart_row = $cart_result->fetch_assoc();

if (!$cart_row) {
    // 2️⃣ If cart doesn't exist, create one
    $create_cart = $conn->prepare("INSERT INTO carts (user_id, created_at) VALUES (?, NOW())");
    $create_cart->bind_param("i", $user_id);
    $create_cart->execute();
    $cart_id = $create_cart->insert_id;
} else {
    $cart_id = $cart_row['cart_id'];
}

// 3️⃣ Check if product exists in cart_items
$check_item = $conn->prepare("SELECT cart_item_id, quantity FROM cart_items WHERE cart_id = ? AND product_id = ?");
$check_item->bind_param("ii", $cart_id, $product_id);
$check_item->execute();
$item_result = $check_item->get_result();
$item_row = $item_result->fetch_assoc();

if ($item_row) {
    // 4️⃣ If product exists, update quantity
    $new_quantity = $item_row['quantity'] + $quantity;
    $update_item = $conn->prepare("UPDATE cart_items SET quantity = ? WHERE cart_item_id = ?");
    $update_item->bind_param("ii", $new_quantity, $item_row['cart_item_id']);
    $update_item->execute();
} else {
    // 5️⃣ Insert new item into cart
    $add_item = $conn->prepare("INSERT INTO cart_items (cart_id, product_id, quantity, created_at) VALUES (?, ?, ?, NOW())");
    $add_item->bind_param("iii", $cart_id, $product_id, $quantity);
    $add_item->execute();
}

echo json_encode(["success" => "Item added to cart"]);
exit;
?>
