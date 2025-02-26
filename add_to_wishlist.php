<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["message" => "Please log in to add items to wishlist."]);
    exit();
}

$user_id = $_SESSION["user_id"];
$product_id = $_POST["product_id"];

// Check if user already has a wishlist
$wishlist_query = $conn->prepare("SELECT wishlist_id FROM wishlists WHERE user_id = ?");
$wishlist_query->bind_param("i", $user_id);
$wishlist_query->execute();
$wishlist_result = $wishlist_query->get_result();
$wishlist = $wishlist_result->fetch_assoc();

if (!$wishlist) {
    $stmt = $conn->prepare("INSERT INTO wishlists (user_id, created_at) VALUES (?, NOW())");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $wishlist_id = $stmt->insert_id;
} else {
    $wishlist_id = $wishlist["wishlist_id"];
}

// Check if product exists in wishlist
$item_query = $conn->prepare("SELECT wishlist_item_id FROM wishlist_items WHERE wishlist_id = ? AND product_id = ?");
$item_query->bind_param("ii", $wishlist_id, $product_id);
$item_query->execute();
$item_result = $item_query->get_result();
$item = $item_result->fetch_assoc();

if (!$item) {
    $insert_stmt = $conn->prepare("INSERT INTO wishlist_items (wishlist_id, product_id, created_at) VALUES (?, ?, NOW())");
    $insert_stmt->bind_param("ii", $wishlist_id, $product_id);
    $insert_stmt->execute();
}

echo json_encode(["message" => "Item added to wishlist successfully"]);
?>
