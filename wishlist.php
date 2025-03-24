<?php 
session_start();
include 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php?redirect=wishlist.php");
    exit();
}

$user_id = $_SESSION["user_id"];

// Check if user has a wishlist
$wishlist_query = $conn->prepare("SELECT wishlist_id FROM wishlists WHERE user_id = ?");
$wishlist_query->bind_param("i", $user_id);
$wishlist_query->execute();
$wishlist_result = $wishlist_query->get_result();
$wishlist = $wishlist_result->fetch_assoc();

// If no wishlist exists, create one
if (!$wishlist) {
    $create_wishlist = $conn->prepare("INSERT INTO wishlists (user_id, created_at) VALUES (?, NOW())");
    $create_wishlist->bind_param("i", $user_id);
    $create_wishlist->execute();
    $wishlist_id = $create_wishlist->insert_id;
} else {
    $wishlist_id = $wishlist["wishlist_id"];
}

// Get all items from wishlist with product details
$item_query = $conn->prepare("
    SELECT wi.wishlist_item_id, p.product_id, p.name, p.price, p.image, p.stock 
    FROM wishlist_items wi 
    JOIN products p ON wi.product_id = p.product_id 
    WHERE wi.wishlist_id = ?
");
$item_query->bind_param("i", $wishlist_id);
$item_query->execute();
$result = $item_query->get_result();

$wishlist_items = [];
while ($row = $result->fetch_assoc()) {
    // Get the first image if multiple
    if (strpos($row['image'], ',') !== false) {
        $images = explode(',', $row['image']);
        $row['display_image'] = trim($images[0]);
    } else {
        $row['display_image'] = $row['image'];
    }
    $wishlist_items[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Wishlist</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .wishlist-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }
        .wishlist-header {
            margin-bottom: 30px;
            text-align: center;
        }
        .wishlist-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 30px;
        }
        .wishlist-item {
            border: 1px solid #eee;
            border-radius: 5px;
            padding: 15px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .wishlist-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .wishlist-item img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 3px;
            margin-bottom: 10px;
        }
        .wishlist-item h3 {
            margin: 10px 0;
            font-size: 18px;
        }
        .wishlist-item .price {
            font-weight: bold;
            color: #333;
            margin-bottom: 15px;
        }
        .wishlist-item .buttons {
            display: flex;
            justify-content: space-between;
        }
        .wishlist-item button {
            padding: 8px 12px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }
        .add-to-cart {
            background-color: #4CAF50;
            color: white;
        }
        .remove-wishlist {
            background-color: #f44336;
            color: white;
        }
        .empty-wishlist {
            text-align: center;
            padding: 50px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }
        #notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 25px;
            background-color: #333;
            color: white;
            border-radius: 5px;
            z-index: 1000;
            display: none;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        #notification.show {
            display: block;
            opacity: 1;
        }

/* Ensure wishlist title respects dark mode text color */
body.darkmode .wishlist-header h1 {
  color: var(--text-color) !important;
}

/* Ensure product price respects dark mode */
body.darkmode .wishlist-item .price {
  color: var(--text-color) !important;
}

body.darkmode .wishlist-container {
  background-color: var(--base-color);
}

    </style>
</head>
<body>
            <?php include 'moonoverlay.php'; ?>

    <!-- Include header.php -->
    <?php include 'phpLogic/header.php'; ?>

    <div id="notification"></div>

    <div class="wishlist-container">
        <div class="wishlist-header">
            <h1>Your Wishlist</h1>
        </div>

        <?php if (empty($wishlist_items)): ?>
            <div class="empty-wishlist">
                <h2>Your wishlist is empty</h2>
                <p>Browse our products and add items to your wishlist!</p>
                <a href="index.php" class="btn">Start Shopping</a>
            </div>
        <?php else: ?>
            <div class="wishlist-grid">
                <?php foreach ($wishlist_items as $item): ?>
                    <div class="wishlist-item">
                        <a href="product_details.php?id=<?php echo $item['product_id']; ?>">
                            <img src="Images/<?php echo htmlspecialchars($item['display_image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                        </a>
                        <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                        <p class="price">£<?php echo number_format($item['price'], 2); ?></p>
                        <div class="buttons">
                            <button class="add-to-cart" data-id="<?php echo $item['product_id']; ?>" <?php echo ($item['stock'] <= 0) ? 'disabled' : ''; ?>>
                                <?php echo ($item['stock'] <= 0) ? 'Out of Stock' : 'Add to Cart'; ?>
                            </button>
                            <button class="remove-wishlist" data-id="<?php echo $item['wishlist_item_id']; ?>">Remove</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Include footer.php -->
    <?php include 'phpLogic/footer.php'; ?>

    <script src="js/script.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Add to cart functionality
            const addToCartButtons = document.querySelectorAll(".add-to-cart");
            addToCartButtons.forEach(button => {
                button.addEventListener("click", function() {
                    const productId = this.dataset.id;
                    
                    fetch("add_to_cart.php", {
                        method: "POST",
                        headers: { "Content-Type": "application/x-www-form-urlencoded" },
                        body: `product_id=${productId}&quantity=1`
                    })
                    .then(response => response.json())
                    .then(data => {
                        showNotification(data.success || data.error);
                        if (data.success) {
                            updateCartCount();
                        }
                    })
                    .catch(error => {
                        console.error("Error adding to cart:", error);
                        showNotification("Error adding item to cart");
                    });
                });
            });

            // Remove from wishlist functionality
            const removeButtons = document.querySelectorAll(".remove-wishlist");
            removeButtons.forEach(button => {
                button.addEventListener("click", function() {
                    const wishlistItemId = this.dataset.id;
                    
                    fetch("remove_from_wishlist.php", {
                        method: "POST",
                        headers: { "Content-Type": "application/x-www-form-urlencoded" },
                        body: `wishlist_item_id=${wishlistItemId}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        showNotification(data.success || data.error);
                        if (data.success) {
                            // Remove item from DOM
                            this.closest('.wishlist-item').remove();
                            
                            // Check if wishlist is now empty
                            if (document.querySelectorAll('.wishlist-item').length === 0) {
                                location.reload(); // Reload to show empty wishlist message
                            }
                        }
                    })
                    .catch(error => {
                        console.error("Error removing from wishlist:", error);
                        showNotification("Error removing item from wishlist");
                    });
                });
            });

            function showNotification(message) {
                const notification = document.getElementById("notification");
                notification.textContent = message;
                notification.classList.add("show");

                setTimeout(() => {
                    notification.classList.remove("show");
                }, 3000);
            }
        });
    </script>
</body>
</html>