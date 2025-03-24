<?php
session_start();
include 'db_connect.php';
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];
$wishlist_item_id = isset($_POST['wishlist_item_id']) ? (int)$_POST['wishlist_item_id'] : 0;

if ($wishlist_item_id === 0) {
    echo json_encode(['error' => 'Invalid wishlist item ID']);
    exit;
}

// Verify that the wishlist item belongs to the user before removing
$check_query = $conn->prepare("
    SELECT wi.wishlist_item_id 
    FROM wishlist_items wi
    JOIN wishlists w ON wi.wishlist_id = w.wishlist_id
    WHERE wi.wishlist_item_id = ? AND w.user_id = ?
");
$check_query->bind_param("ii", $wishlist_item_id, $user_id);
$check_query->execute();
$result = $check_query->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['error' => 'Item not found in your wishlist']);
    exit;
}

// Remove the item from the wishlist
$remove_query = $conn->prepare("DELETE FROM wishlist_items WHERE wishlist_item_id = ?");
$remove_query->bind_param("i", $wishlist_item_id);

if ($remove_query->execute()) {
    echo json_encode(['success' => 'Item removed from wishlist']);
} else {
    echo json_encode(['error' => 'Failed to remove item from wishlist']);
}