<?php
include 'db_connect.php'; // database connection 

if (isset($_GET['query'])) {
    $search = trim($_GET['query']); 
    $search = $conn->real_escape_string($search); 

    $filter = isset($_GET['filter']) ? $_GET['filter'] : '';

    $whereClause = "WHERE name LIKE '%$search%' OR description LIKE '%$search%'";
    $orderClause = "";

    switch ($filter) {
        case 'price_asc':
            $orderClause = "ORDER BY price ASC";
            break;
        case 'price_desc':
            $orderClause = "ORDER BY price DESC";
            break;
        case 'size_S':
            $whereClause .= " AND size LIKE '%S%'";
            break;
        case 'size_M':
            $whereClause .= " AND size LIKE '%M%'";
            break;
        case 'size_L':
            $whereClause .= " AND size LIKE '%L%'";
            break;
    }

    $sql = "SELECT * FROM products $whereClause $orderClause";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $imageArray = explode(",", $row['image']);
            $firstImage = isset($imageArray[0]) ? $imageArray[0] : 'default.jpg';

            echo "<div class='product'>";
            echo "<img src='Images/" . htmlspecialchars($firstImage, ENT_QUOTES) . "' alt='" . htmlspecialchars($row['name'], ENT_QUOTES) . "'>";
            echo "<h3 class='product-name'>" . htmlspecialchars($row['name'], ENT_QUOTES) . "</h3>";
            echo "<p>£" . number_format($row['price'], 2) . "</p>";
            echo "<div class='button-row'>";
            echo "<button class='add-to-wishlist' data-id='" . $row['product_id'] . "'>❤</button>";
            echo "</div>";
            echo "</div>";
        }
    } else {
        echo "<p>No products found.</p>";
    }
}
?>
