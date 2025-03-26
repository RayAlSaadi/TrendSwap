<?php
session_start();
include 'db_connect.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect=product_details.php?id=" . $_POST['product_id'] . "&error=login_required");
    exit;
}


if (!isset($_POST['product_id']) || !isset($_POST['rating'])) {
    header("Location: product_details.php?id=" . $_POST['product_id'] . "&error=missing_fields");
    exit;
}

$user_id = (int)$_SESSION['user_id']; 
$product_id = (int)$_POST['product_id'];
$rating = (int)$_POST['rating'];
$comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';


if ($rating < 1 || $rating > 5) {
    header("Location: product_details.php?id=$product_id&error=invalid_rating");
    exit;
}


$user_check = $conn->prepare("SELECT user_id FROM users WHERE user_id = ?");
$user_check->bind_param("i", $user_id);
$user_check->execute();
$user_result = $user_check->get_result();

if ($user_result->num_rows === 0) {
   
    session_destroy(); 
    header("Location: login.php?redirect=product_details.php?id=$product_id&error=invalid_session");
    exit;
}


$product_check = $conn->prepare("SELECT product_id FROM products WHERE product_id = ?");
$product_check->bind_param("i", $product_id);
$product_check->execute();
$product_result = $product_check->get_result();

if ($product_result->num_rows === 0) {
    
    header("Location: index.php?error=invalid_product");
    exit;
}


$check_query = $conn->prepare("SELECT review_id FROM reviews WHERE user_id = ? AND product_id = ?");
$check_query->bind_param("ii", $user_id, $product_id);
$check_query->execute();
$check_result = $check_query->get_result();

if ($check_result->num_rows > 0) {
    
    $review = $check_result->fetch_assoc();
    $update_query = $conn->prepare("UPDATE reviews SET rating = ?, comment = ?, review_date = CURRENT_TIMESTAMP WHERE review_id = ?");
    $update_query->bind_param("isi", $rating, $comment, $review['review_id']);
    
    if ($update_query->execute()) {
        header("Location: product_details.php?id=$product_id&review=updated");
    } else {
        header("Location: product_details.php?id=$product_id&error=update_failed");
    }
} else {
    
    try {
        $insert_query = $conn->prepare("INSERT INTO reviews (user_id, product_id, rating, comment) VALUES (?, ?, ?, ?)");
        $insert_query->bind_param("iiis", $user_id, $product_id, $rating, $comment);
        
        if ($insert_query->execute()) {
            header("Location: product_details.php?id=$product_id&review=submitted");
        } else {
            header("Location: product_details.php?id=$product_id&error=submit_failed");
        }
    } catch (Exception $e) {
        
        error_log("Review submission error: " . $e->getMessage());
        header("Location: product_details.php?id=$product_id&error=database_error");
    }
}
?>