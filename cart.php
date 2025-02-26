<?php
session_start();
include 'db_connect.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="css/styles.css"> <!-- Keep styles in the root directory -->
</head>
<body>

    <!-- ✅ Include the Navbar -->
    <?php include 'navbar.php'; ?>

    <div class="cart-container">
        <h1>Shopping Bag</h1>

        <?php
        if (!isset($_SESSION["user_id"])) {
            echo "<p>Please <a href='login.php'>log in</a> to view your cart.</p>";
            exit();
        }

        $user_id = $_SESSION["user_id"];

        // Get user's cart ID
        $cart_query = $conn->prepare("SELECT cart_id FROM carts WHERE user_id = ?");
        $cart_query->bind_param("i", $user_id);
        $cart_query->execute();
        $cart_result = $cart_query->get_result();
        $cart = $cart_result->fetch_assoc();

        $cart_items = [];

        if ($cart) {
            $cart_id = $cart["cart_id"];

            // Get all items from cart_items table
            $item_query = $conn->prepare("
                SELECT p.product_id, p.name, p.price, p.image, ci.quantity 
                FROM cart_items ci 
                JOIN products p ON ci.product_id = p.product_id 
                WHERE ci.cart_id = ?
            ");
            $item_query->bind_param("i", $cart_id);
            $item_query->execute();
            $result = $item_query->get_result();
            
            while ($row = $result->fetch_assoc()) {
                $cart_items[] = $row;
            }
        }
        ?>

        <?php if (empty($cart_items)): ?>
            <p>Your cart is empty.</p>
        <?php else: ?>
            <div class="cart-items">
                <?php
                $total_price = 0;
                foreach ($cart_items as $item):
                    $product_images = explode(",", $item["image"]); // Extract only the first image
                    $first_image = trim($product_images[0]);
                    $item_total = $item["price"] * $item["quantity"];
                    $total_price += $item_total;
                ?>
                    <div class="cart-item">
                        <img src="Images/<?php echo htmlspecialchars($first_image); ?>" alt="<?php echo htmlspecialchars($item["name"]); ?>" width="50">

                        <div class="cart-details">
                            <h3><?php echo htmlspecialchars($item["name"]); ?></h3>
                            <p>£<?php echo number_format($item["price"], 2); ?></p>
                            <label for="quantity-<?php echo $item["product_id"]; ?>">Quantity:</label>
                            <select class="cart-quantity" data-id="<?php echo $item["product_id"]; ?>">
                                <?php for ($i = 1; $i <= 10; $i++): ?>
                                    <option value="<?php echo $i; ?>" <?php echo ($item["quantity"] == $i) ? "selected" : ""; ?>><?php echo $i; ?></option>
                                <?php endfor; ?>
                            </select>
                            <button class="remove-item" data-id="<?php echo $item["product_id"]; ?>">Remove</button>
                        </div>
                        <div class="cart-total">
                            <p>Total: £<?php echo number_format($item_total, 2); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="cart-summary">
                <h2>Order Summary</h2>
                <p>Subtotal: £<?php echo number_format($total_price, 2); ?></p>
                <p>Delivery: Calculated at checkout</p>
                <h3>Total: £<?php echo number_format($total_price, 2); ?></h3>
                <button class="checkout-button">Secure Checkout Now</button>
            </div>
        <?php endif; ?>
    </div>

    <!-- ✅ Include Footer -->
    <?php include 'footer.php'; ?>

    <!-- ✅ External JS File (cart.js) -->
    <script src="js/cart.js"></script>

</body>
</html>
