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
$order_id = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;
$order_item_id = isset($_POST['order_item_id']) ? (int)$_POST['order_item_id'] : 0;
$reason = isset($_POST['reason']) ? $_POST['reason'] : '';
$comments = isset($_POST['comments']) ? $_POST['comments'] : '';

if ($order_id === 0 || $order_item_id === 0 || empty($reason)) {
    echo json_encode(['error' => 'Missing required information']);
    exit;
}

// Verify that this order and item belong to the user
$verify_query = $conn->prepare("
    SELECT o.order_id, oi.order_item_id, oi.quantity 
    FROM orders o
    JOIN order_items oi ON o.order_id = oi.order_id
    WHERE o.order_id = ? AND oi.order_item_id = ? AND o.user_id = ? AND o.status = 'Delivered'
");
$verify_query->bind_param("iii", $order_id, $order_item_id, $user_id);
$verify_query->execute();
$verify_result = $verify_query->get_result();

if ($verify_result->num_rows === 0) {
    echo json_encode(['error' => 'Invalid order or item']);
    exit;
}

$order_item = $verify_result->fetch_assoc();

// Check if this item has already been returned or is in the process of being returned
$check_return = $conn->prepare("
    SELECT return_id FROM order_returns 
    WHERE order_id = ? AND order_item_id = ?
");
$check_return->bind_param("ii", $order_id, $order_item_id);
$check_return->execute();
$check_result = $check_return->get_result();

if ($check_result->num_rows > 0) {
    echo json_encode(['error' => 'Return request already exists for this item']);
    exit;
}

// Create the return request
$create_return = $conn->prepare("
    INSERT INTO order_returns (
        order_id, 
        order_item_id, 
        user_id, 
        quantity, 
        reason, 
        status, 
        created_at
    ) VALUES (?, ?, ?, ?, ?, 'Requested', NOW())
");
$create_return->bind_param("iiiis", $order_id, $order_item_id, $user_id, $order_item['quantity'], $reason);

if ($create_return->execute()) {
    echo json_encode(['success' => 'Return request submitted successfully']);
} else {
    echo json_encode(['error' => 'Failed to submit return request']);
}