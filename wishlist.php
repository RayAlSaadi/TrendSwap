<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

// Get user's wishlist ID
$wishlist_query = $conn->prepare("SELECT wishlist_id FROM wishlists WHERE user_id = ?");
$wishlist_query->bind_param("i", $user_id);
$wishlist_query->execute();
$wishlist_result = $wishlist_query->get_result();
$wishlist = $wishlist_result->fetch_assoc();

$wishlist_items = [];

if ($wishlist) {
    $wishlist_id = $wishlist["wishlist_id"];

    // Get all items from wishlist_items table
    $item_query = $conn->prepare("
        SELECT p.id, p.name, p.price, p.image 
        FROM wishlist_items wi 
        JOIN products p ON wi.product_id = p.id 
        WHERE wi.wishlist_id = ?
    ");
    $item_query->bind_param("i", $wishlist_id);
    $item_query->execute();
    $result = $item_query->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $wishlist_items[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>My Wishlist</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Your Wishlist</h1>

    <?php if (empty($wishlist_items)): ?>
        <p>Your wishlist is empty.</p>
    <?php else: ?>
        <table>
            <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Action</th>
            </tr>
            <?php foreach ($wishlist_items as $item): ?>
                <tr>
                    <td>
                        <img src="Images/<?php echo $item["image"]; ?>" alt="<?php echo $item["name"]; ?>" width="50">
                        <?php echo $item["name"]; ?>
                    </td>
                    <td>£<?php echo number_format($item["price"], 2); ?></td>
                    <td>
                        <button class="remove-wishlist" data-id="<?php echo $item["id"]; ?>">Remove</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <script>
        document.querySelectorAll(".remove-wishlist").forEach(button => {
            button.addEventListener("click", function () {
                const productId = this.dataset.id;

                fetch("remove_from_wishlist.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: `product_id=${productId}`
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    location.reload();
                });
            });
        });
    </script>
</body>
</html>
