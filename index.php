<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TrendSwap</title>
    <link rel="stylesheet" href="css/styles.css"> 
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
<body>
    
        <?php include 'phpLogic/header.php'; ?>
            <?php include 'moonoverlay.php'; ?>
    
    


    <section class="categories-section">
        <div class="category">
            <img src="Images/banner3.jpg" alt="Men's Fashion">     
            <a href="men.php">MEN</a>
        </div>
        
        <div class="category">
            <img src="Images/banner2.jpg" alt="Women's Fashion">   
            <a href="women.php">WOMEN</a>   
        </div>
        
        <div class="category">
            <img src="Images/banner4.jpg" alt="Kids' Fashion"> 
            <a href="kids.php">KIDS</a>
        </div>
    </section>

    <!-- ✅ Footer -->
    <?php include 'phpLogic/footer.php'; ?>

    <script>
        const searchIcon = document.getElementById('search-icon');
        const searchBar = document.getElementById('search-bar');

        searchIcon.addEventListener('click', () => {
            searchBar.classList.toggle('active');
        });
    </script>
</body>
</html>

