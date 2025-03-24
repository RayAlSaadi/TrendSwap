document.addEventListener("DOMContentLoaded", () => {
    console.log("✅ cart.js loaded"); // Debugging

    // 📌 ADD TO CART FUNCTIONALITY
    const cartButtons = document.querySelectorAll(".add-to-cart");

    cartButtons.forEach(button => {
        button.addEventListener("click", function () {
            const productId = this.dataset.id;
            console.log("🛒 Add to Cart clicked - Product ID:", productId);

            if (!productId) {
                console.error("❌ No Product ID Found!");
                return;
            }

            fetch("add_to_cart.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `product_id=${productId}&quantity=1`
            })
            .then(response => response.json())
            .then(data => {
                console.log("📨 Server Response:", data);

                if (data.success) {
                    showNotification(data.success);
                    updateCartCount();
                } else {
                    showNotification(data.error || "Error adding item to cart");
                }
            })
            .catch(error => console.error("🚨 Error adding to cart:", error));
        });
    });

    // 📌 ADD TO WISHLIST FUNCTIONALITY
    const wishlistButtons = document.querySelectorAll(".add-to-wishlist");

    wishlistButtons.forEach(button => {
        button.addEventListener("click", function () {
            const productId = this.dataset.id;
            console.log("❤️ Wishlist clicked - Product ID:", productId);

            if (!productId) {
                console.error("❌ No Product ID Found!");
                return;
            }

            fetch("add_to_wishlist.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `product_id=${productId}`
            })
            .then(response => response.json())
            .then(data => {
                console.log("📨 Server Response:", data);
                showNotification(data.success || data.error || "Error adding item to wishlist");
            })
            .catch(error => console.error("🚨 Error adding to wishlist:", error));
        });
    });

    // 📌 SHOW NOTIFICATIONS
    function showNotification(message) {
        const notification = document.getElementById("notification");
        if (!notification) {
            console.error("❌ Notification element not found!");
            return;
        }

        notification.textContent = message;
        notification.classList.add("show");
        notification.style.display = "block";

        setTimeout(() => {
            notification.classList.remove("show");
            notification.style.display = "none";
        }, 3000);
    }

    // 📌 UPDATE CART COUNT FUNCTIONALITY
    function updateCartCount() {
        fetch("cart_count.php")
        .then(response => response.json()) // Expect JSON response
        .then(data => {
            console.log("🛒 Cart count response:", data);
            const cartIcon = document.querySelector(".bag-icon");
            if (cartIcon) {
                cartIcon.setAttribute("data-count", data.count || 0);
            } else {
                console.error("❌ Cart icon not found!");
            }
        })
        .catch(error => console.error("🚨 Error fetching cart count:", error));
    }

    // Initial cart count update
    updateCartCount();
});

// SEARCH BAR TOGGLE FUNCTIONALITY
document.addEventListener("DOMContentLoaded", () => {
    const searchIcon = document.getElementById("search-icon");
    const searchBar = document.getElementById("search-bar");
    let isSearchOpen = false;

    if (searchIcon && searchBar) { // Ensure elements exist before adding event listeners
        searchIcon.addEventListener("click", (event) => {
            event.stopPropagation();
            if (isSearchOpen) {
                searchBar.style.width = "0";
                searchBar.classList.remove("open");
            } else {
                searchBar.style.width = "200px";
                searchBar.classList.add("open");
            }
            isSearchOpen = !isSearchOpen;
        });

        document.addEventListener("click", (event) => {
            if (!searchBar.contains(event.target) && !searchIcon.contains(event.target)) {
                searchBar.style.width = "0";
                searchBar.classList.remove("open");
                isSearchOpen = false;
            }
        });
    } else {
        console.error("⚠️ Search bar or search icon not found!");
    }
});

function showNotification(message) {
    let notification = document.getElementById('notification');

    // ✅ If notification element doesn't exist, create it
    if (!notification) {
        notification = document.createElement("div");
        notification.id = "notification";
        notification.className = "notification";
        document.body.appendChild(notification);
    }

    notification.textContent = message;
    notification.classList.add('show');

    setTimeout(() => {
        notification.classList.remove('show');
    }, 3000);
}
// Fetch Cart Count from `cart_count.php`
function updateCartCount() {
    fetch("cart_count.php")
    .then(response => response.json())
    .then(data => {
        const cartCount = document.getElementById("cart-count");
        if (cartCount) {
            cartCount.textContent = data.count; // Update count
        }
    })
    .catch(error => console.error("Error fetching cart count:", error));
}

// Run updateCartCount on Page Load
document.addEventListener("DOMContentLoaded", updateCartCount);

