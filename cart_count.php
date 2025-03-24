<?php
include 'db_connect.php'; // Database connection

session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['count' => 0]); // If not logged in, return 0
    exit;
}

$user_id = $_SESSION['user_id'];

// Get the cart_id for the logged-in user
$sql = "SELECT cart_id FROM carts WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$cart = $result->fetch_assoc();

if (!$cart) {
    echo json_encode(['count' => 0]); // No cart found for user
    exit;
}

$cart_id = $cart['cart_id'];

// Get the total quantity of items in the cart
$sql = "SELECT SUM(quantity) as total_items FROM cart_items WHERE cart_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $cart_id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

$total_items = $data['total_items'] ?? 0;

echo json_encode(['count' => $total_items]);
?>
