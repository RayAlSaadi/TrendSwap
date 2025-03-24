<?php
session_start();
include 'db_connect.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];
$cart_item_id = isset($_POST['cart_item_id']) ? (int)$_POST['cart_item_id'] : 0;

if ($cart_item_id === 0) {
    echo json_encode(['error' => 'Invalid input']);
    exit;
}

// Verify this cart item belongs to the user
$verify_query = $conn->prepare("
    SELECT ci.cart_item_id, c.user_id 
    FROM cart_items ci
    JOIN carts c ON ci.cart_id = c.cart_id
    WHERE ci.cart_item_id = ? AND c.user_id = ?
");
$verify_query->bind_param("ii", $cart_item_id, $user_id);
$verify_query->execute();
$verify_result = $verify_query->get_result();

if ($verify_result->num_rows === 0) {
    echo json_encode(['error' => 'Item not found in your cart']);
    exit;
}

// Delete the cart item
$delete_query = $conn->prepare("DELETE FROM cart_items WHERE cart_item_id = ?");
$delete_query->bind_param("i", $cart_item_id);
$delete_query->execute();

if ($delete_query->affected_rows > 0) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => 'Failed to remove item']);
}
?>