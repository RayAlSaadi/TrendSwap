<?php
// Start the session to track logged-in users and cart details
session_start();
?>

<link rel="stylesheet" href="css/styles.css">


    <div class="navbar">
        <div class="navbar-left">
            <div class="navbar-logo">
                <img src="Images/Logo.jpg" alt="Logo">
            </div>
            <div class="navbar-links">
                <a href="index.php">HOME</a>
                <a href="men.php" class="active">MEN</a>
                <a href="women.php">WOMAN</a>
                <a href="kids.php">KIDS</a>
                <a href="accessories.php">ACCESSORIES</a>
                <a href="babies.php">BABIES</a>
                <a href="aboutus.html">ABOUT US</a>
            </div>
        </div>
        <div class="navbar-right">
            <div class="search-icon">
                <img src="Images/Search.png" alt="Search" id="search-icon">
            </div>
            <div class="search-bar" id="search-bar">
                <input type="text" placeholder="Search...">
            </div>
            <div class="bag-icon">
    <a href="cart.php">
        <img src="Images/Bag.png" alt="Bag">
        <span id="cart-count" class="cart-count">0</span> 
    </a>
</div>

<!-- Load External Scripts -->
<script src="cart.js"></script>
<script src="darkmode.js"></script>