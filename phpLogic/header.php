<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/header.css">
    
</head>
<body>

    <div class="delivery-info">
        Free Delivery Available | Find out more
    </div>

    <div class="navbar">
        <div class="navbar-left">
            <div class="navbar-logo">
                <img src="Images/Logo.png" alt="Logo" a href="index.php">
            </div>
            <div class="navbar-links">
                <a href="index.php">HOME</a>
                <a href="men.php">MEN</a>
                <a href="women.php">WOMAN</a>
                <a href="kids.php">KIDS</a>
                <a href="accessories.php">ACCESSORIES</a>
                <a href="babies.php">BABIES</a>
                <a href="aboutus.php">ABOUT US</a>
            </div>
        </div>
        <div class="navbar-right">
            <div class="search-icon">
                <img src="Images/Search.png" alt="Search" id="search-icon">
            </div>

            <div class="bag-icon">
                <a href="cart.php"><img src="Images/Bag.png" alt="Bag"></a>
            </div>

            <div class="user-dropdown-container">
                <div class="person-icon">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <a href="userPage.php"><img src="Images/Person.png" alt="Person"></a>
                    <?php else: ?>
                        <a href="login.php"><img src="Images/Person.png" alt="Login"></a>
                    <?php endif; ?>
                </div>
                <div class="dropdown-menu">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <a href="userPage.php">My Account</a>
                        <a href="previous_orders.php">My Orders</a>
                     <a href="wishlist.php">My Wishlist</a>
                      
                 
                    <?php if(isset($_SESSION['role']) && in_array($_SESSION['role'], ['Super Admin', 'Manager'])): ?>
            		<a href="admin_dashboard.php">Admin Dashboard</a>
        			<?php endif; ?>
        			<a href="logout.php">Logout</a>
                    <?php else: ?>
                        <a href="login.php">Login</a>
                        <a href="signup.php">Sign Up</a>
                    	<a href="adminLogin.php">Admin Login</a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="search-bar" id="search-bar">
                <form method="GET">
                    <input type="text" name="query" placeholder="Search..." 
                           value="<?php echo isset($_GET['query']) ? htmlspecialchars($_GET['query']) : '' ?>">
                </form>
            </div>
        </div>
    </div>

    <div id="search-sidebar" class="search-sidebar">
        <div class="sidebar-header">
            <h3>Search Results</h3>
             <div class="filter-dropdown">
        <button id="filterButton">Filters ▾</button>
        <div id="filterMenu" class="filter-menu">
            <div class="filter-option" data-filter="price_asc">Price: Low to High</div>
            <div class="filter-option" data-filter="price_desc">Price: High to Low</div>
            <div class="filter-option" data-filter="size_S">Size: Small</div>
            <div class="filter-option" data-filter="size_M">Size: Medium</div>
            <div class="filter-option" data-filter="size_L">Size: Large</div>
        </div>
    </div>

            <span class="close-sidebar">&times;</span>
        </div>
        <div id="search-results-container"></div>
    </div>
    <div id="sidebar-overlay" class="sidebar-overlay"></div>

    

    <script>
  document.addEventListener("DOMContentLoaded", () => {
    const searchIcon = document.getElementById("search-icon");
    const searchBar = document.getElementById("search-bar");
    const searchForm = document.querySelector('#search-bar form');
    const sidebar = document.getElementById('search-sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    const closeBtn = document.querySelector('.close-sidebar');

    let isSearchOpen = false;
    let currentSearchTerm = '';
    let currentFilter = '';

    //  search bar visibility
    if (searchIcon && searchBar) { 
        searchIcon.addEventListener("click", (event) => {
            event.stopPropagation();
            searchBar.classList.toggle('open');
            isSearchOpen = !isSearchOpen;
        });

        document.addEventListener("click", (event) => {
            if (!searchBar.contains(event.target) && !searchIcon.contains(event.target)) {
                searchBar.classList.remove('open');
                isSearchOpen = false;
            }
        });
    }

  
    function fetchSearchResults() {
        if (!currentSearchTerm) {
            
            return;
        }

        const params = new URLSearchParams({
    query: currentSearchTerm,
    filter: currentFilter
});

const url = `searchbar.php?${params.toString()}`;


fetch(url)
  .then(response => response.text())
  .then(html => {
      document.getElementById('search-results-container').innerHTML = html;
	 attachCartAndWishlistListeners();
      sidebar.classList.add('active');
      overlay.style.display = 'block';

  });
    
    }
  
  
  function attachCartAndWishlistListeners() {
        // Add to Cart Buttons
        const cartButtons = document.querySelectorAll('.add-to-cart');
        cartButtons.forEach(button => {
            button.addEventListener('click', () => {
                const productId = button.getAttribute('data-id');

                fetch('add_to_cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `product_id=${productId}&quantity=1`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Added to cart!');
                    } else {
                        alert(data.error || 'Something went wrong.');
                    }
                })
                .catch(err => {
                    console.error('Error:', err);
                });
            });
        });

        // Wishlist Buttons
const wishlistButtons = document.querySelectorAll('.wishlist-button, .add-to-wishlist');
        wishlistButtons.forEach(button => {
    button.addEventListener('click', () => {
        const productId = button.getAttribute('data-id');

        fetch('add_to_wishlist.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `product_id=${productId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Added to wishlist!');
            } else {
                alert(data.error || 'Something went wrong.');
            }
        })
        .catch(err => {
            console.error('Error:', err);
        });
    });
});

    }

 
    if (searchForm) {
        searchForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const searchInput = searchForm.querySelector('input');
            currentSearchTerm = searchInput.value.trim();
            currentFilter = ''; 
            fetchSearchResults();
        });
    }

    // Filter dropdown handling
    const filterButton = document.getElementById('filterButton');
    const filterMenu = document.getElementById('filterMenu');

    if (filterButton && filterMenu) {
        filterButton.addEventListener('click', (e) => {
            e.stopPropagation();
            filterMenu.classList.toggle('show');
        });

        document.addEventListener('click', (e) => {
            if (!filterButton.contains(e.target) && !filterMenu.contains(e.target)) {
                filterMenu.classList.remove('show');
            }
        });

       
        const filterOptions = filterMenu.querySelectorAll('.filter-option');
        filterOptions.forEach(option => {
            option.addEventListener('click', () => {
                currentFilter = option.getAttribute('data-filter');
                console.log("Filter applied:", currentFilter);
                fetchSearchResults();
            });
        });
    }

    
    closeBtn.addEventListener('click', () => {
        sidebar.classList.remove('active');
        overlay.style.display = 'none';
    });

    overlay.addEventListener('click', () => {
        sidebar.classList.remove('active');
        overlay.style.display = 'none';
    });

  });
</script>

</body>
</html>
