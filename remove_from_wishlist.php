<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["message" => "User not logged in."]);
    exit();
}

$user_id = $_SESSION["user_id"];
$product_id = $_POST["product_id"];

// Get user's wishlist ID
$wishlist_query = $conn->prepare("SELECT wishlist_id FROM wishlists WHERE user_id = ?");
$wishlist_query->bind_param("i", $user_id);
$wishlist_query->execute();
$wishlist_result = $wishlist_query->get_result();
$wishlist = $wishlist_result->fetch_assoc();

if ($wishlist) {
    $wishlist_id = $wishlist["wishlist_id"];

    // Remove product from wishlist_items
    $delete_stmt = $conn->prepare("DELETE FROM wishlist_items WHERE wishlist_id = ? AND product_id = ?");
    $delete_stmt->bind_param("ii", $wishlist_id, $product_id);
    $delete_stmt->execute();
}

echo json_encode(["message" => "Item removed from wishlist."]);
?>
