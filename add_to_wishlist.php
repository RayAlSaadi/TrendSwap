<?php
session_start();
include 'db_connect.php';
header('Content-Type: application/json');


if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Please log in to add items to your wishlist']);
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;

if ($product_id === 0) {
    echo json_encode(['error' => 'Invalid product ID']);
    exit;
}


$product_check = $conn->prepare("SELECT product_id FROM products WHERE product_id = ?");
$product_check->bind_param("i", $product_id);
$product_check->execute();
$product_result = $product_check->get_result();

if ($product_result->num_rows === 0) {
    echo json_encode(['error' => 'Product not found']);
    exit;
}


$wishlist_check = $conn->prepare("SELECT wishlist_id FROM wishlists WHERE user_id = ?");
$wishlist_check->bind_param("i", $user_id);
$wishlist_check->execute();
$wishlist_result = $wishlist_check->get_result();
$wishlist = $wishlist_result->fetch_assoc();


if (!$wishlist) {
    $create_wishlist = $conn->prepare("INSERT INTO wishlists (user_id, created_at) VALUES (?, NOW())");
    $create_wishlist->bind_param("i", $user_id);
    $create_wishlist->execute();
    $wishlist_id = $create_wishlist->insert_id;
} else {
    $wishlist_id = $wishlist['wishlist_id'];
}


$item_check = $conn->prepare("
    SELECT wishlist_item_id 
    FROM wishlist_items 
    WHERE wishlist_id = ? AND product_id = ?
");
$item_check->bind_param("ii", $wishlist_id, $product_id);
$item_check->execute();
$item_result = $item_check->get_result();

if ($item_result->num_rows > 0) {
    echo json_encode(['success' => 'Item is already in your wishlist']);
    exit;
}


$add_item = $conn->prepare("
    INSERT INTO wishlist_items (wishlist_id, product_id, created_at) 
    VALUES (?, ?, NOW())
");
$add_item->bind_param("ii", $wishlist_id, $product_id);

if ($add_item->execute()) {
    echo json_encode(['success' => 'Item added to wishlist']);
} else {
    echo json_encode(['error' => 'Failed to add item to wishlist']);
}