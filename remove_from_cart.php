<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["message" => "User not logged in."]);
    exit();
}

$user_id = $_SESSION["user_id"];
$product_id = $_POST["product_id"];

// Get user's cart ID
$cart_query = $conn->prepare("SELECT cart_id FROM carts WHERE user_id = ?");
$cart_query->bind_param("i", $user_id);
$cart_query->execute();
$cart_result = $cart_query->get_result();
$cart = $cart_result->fetch_assoc();

if ($cart) {
    $cart_id = $cart["cart_id"];

    // Remove product from cart_items
    $delete_stmt = $conn->prepare("DELETE FROM cart_items WHERE cart_id = ? AND product_id = ?");
    $delete_stmt->bind_param("ii", $cart_id, $product_id);
    $delete_stmt->execute();
}

echo json_encode(["message" => "Item removed from cart."]);
?>
