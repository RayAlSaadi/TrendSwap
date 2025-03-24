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
$cart_item_id = isset($_POST['cart_item_id']) ? (int)$_POST['cart_item_id'] : 0;
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;

// Validate input
if ($cart_item_id === 0 || $quantity < 1) {
    echo json_encode(['error' => 'Invalid input']);
    exit;
}

// Verify this cart item belongs to the user
$verify_query = $conn->prepare("
    SELECT ci.cart_item_id, ci.product_id, ci.size, c.user_id 
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

$cart_item = $verify_result->fetch_assoc();

// Check if requested quantity is available in stock
$stock_check = $conn->prepare("
    SELECT stock FROM product_sizes 
    WHERE product_id = ? AND size = ?
");
$stock_check->bind_param("is", $cart_item['product_id'], $cart_item['size']);
$stock_check->execute();
$stock_result = $stock_check->get_result();

if ($stock_result->num_rows > 0) {
    $stock_data = $stock_result->fetch_assoc();
    
    if ($stock_data['stock'] < $quantity) {
        echo json_encode([
            'error' => 'Not enough items in stock. Only ' . $stock_data['stock'] . ' available.'
        ]);
        exit;
    }
}

// All checks passed, update the quantity
try {
    // Begin transaction for consistency
    $conn->begin_transaction();
    
    // Update quantity
    $update_query = $conn->prepare("UPDATE cart_items SET quantity = ? WHERE cart_item_id = ?");
    $update_query->bind_param("ii", $quantity, $cart_item_id);
    $update_query->execute();
    
    if ($update_query->affected_rows < 0) {
        throw new Exception('Failed to update quantity');
    }
    
    // Get updated subtotal for response
    $item_query = $conn->prepare("
        SELECT ci.quantity, p.price
        FROM cart_items ci
        JOIN products p ON ci.product_id = p.product_id
        WHERE ci.cart_item_id = ?
    ");
    $item_query->bind_param("i", $cart_item_id);
    $item_query->execute();
    $result = $item_query->get_result();
    $item = $result->fetch_assoc();
    
    $subtotal = $item['quantity'] * $item['price'];
    
    // Commit the transaction
    $conn->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Cart updated successfully',
        'subtotal' => number_format($subtotal, 2),
        'quantity' => $quantity
    ]);
} catch (Exception $e) {
    // Roll back the transaction on error
    $conn->rollback();
    echo json_encode(['error' => $e->getMessage()]);
}