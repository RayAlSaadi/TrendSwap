<?php
session_start();  // Start the session
// Check if user is logged in by checking the session data
$is_logged_in = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <title>About Us</title>
</head>
<style>
body.darkmode .about-container {
    color: var(--text-color) !important;
    transition: background-color 0.3s ease, color 0.3s ease;
}

body.darkmode .about-container h1,
body.darkmode .about-container h2,
body.darkmode .about-container p,
body.darkmode .about-container li,
body.darkmode .about-container ul {
    color: var(--text-color) !important;
}


</style>
<body>
    <!-- Include header.php -->
    <?php include 'phpLogic/header.php'; ?>
     <?php include 'moonoverlay.php'; ?>
    
  
    <div class="about-container">
        <h1>About TrendSwap</h1>
        <p>
            Welcome to the TrendSwap website! We are a clothing company that specializes in a wide range clothes and accessories. TrendSwap offers a diverse and stylish yet affordable range of clothes to suit your needs.
        </p>
        <h2>Our Mission</h2>
        <p>
            Our goal is to provide a stylish , sustainable and affordable experience. Here at Trendswap,our goal is to allow individuals to express themselves through fashion, with our large range of different styles and products.
        </p>
        <h2>Our Team</h2>
        <p>
            We are a team of 9 fashion enthusiasts, focused on the goal of giving out customers an amazing experience, From out designers, support teams to out software team, we are constantly looking for ways to improve Trendswap and improve our customer experience
        </p>
        <h2>Contact Us</h2>
        <p>Any questions? please contact us:</p>
        <ul>
            <li>Email: support@trendswap.com</li>
            <li>Phone: 07123456789</li>
            <li>Address: Aston Uni</li>
        </ul>
    </div>
    <?php include 'phpLogic/footer.php'; ?>
    
     
      <div id="notification" class="notification"></div>

      <script src="js/cart.js"></script>
    
</body>
</html>