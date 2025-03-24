<?php
// searchbar_test.php

// Include necessary files
include 'db_connect.php';

// Define test function
function testSearch($query, $filter = '') {
    echo "<h3>Testing search with query: '$query', filter: '$filter'</h3>";
    
    // Capture output
    ob_start();
    
    // Set up GET parameters
    $_GET['query'] = $query;
    if (!empty($filter)) {
        $_GET['filter'] = $filter;
    } else {
        unset($_GET['filter']);
    }
    
    // Include the file to test
    include 'searchbar.php';
    
    // Get the output
    $output = ob_get_clean();
    
    // Return the result
    return $output;
}

// Function to count products in results
function countProducts($html) {
    $count = substr_count($html, '<div class=\'product\'>');
    return $count;
}

// Style for the test page
echo "
<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .test-case { border: 1px solid #ddd; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
    .results { background-color: #f9f9f9; padding: 10px; margin-top: 10px; overflow: auto; max-height: 400px; }
    .pass { color: green; font-weight: bold; }
    .fail { color: red; font-weight: bold; }
    .count { font-weight: bold; margin-bottom: 10px; }
    
    /* Product styling for test results */
    .results .product { display: inline-block; margin: 10px; width: 200px; vertical-align: top; }
    .results .product img { max-width: 100%; height: auto; max-height: 150px; object-fit: contain; }
    .results .product h3 { font-size: 14px; margin: 5px 0; }
    .results .product p { margin: 5px 0; }
    .results .button-row { margin-top: 10px; }
</style>
";

echo "<h1>Searchbar.php Unit Tests</h1>";

// Test 1: Basic search functionality
echo "<div class='test-case'>";
echo "<h2>Test 1: Basic Search</h2>";
$result1 = testSearch('shirt');
$count1 = countProducts($result1);
echo "<div class='count'>Found $count1 products</div>";
echo "<div class='results'>$result1</div>";
echo "</div>";

// Test 2: No results
echo "<div class='test-case'>";
echo "<h2>Test 2: No Results</h2>";
$result2 = testSearch('xyz123');
$count2 = countProducts($result2);
echo "<div class='count'>Found $count2 products</div>";
echo "<div class='results'>$result2</div>";
echo "</div>";

// Test 3: Filter by price (ascending)
echo "<div class='test-case'>";
echo "<h2>Test 3: Filter by Price (Low to High)</h2>";
$result3 = testSearch('shirt', 'price_asc');
$count3 = countProducts($result3);
echo "<div class='count'>Found $count3 products</div>";
echo "<div class='results'>$result3</div>";
echo "</div>";

// Test 4: Filter by price (descending)
echo "<div class='test-case'>";
echo "<h2>Test 4: Filter by Price (High to Low)</h2>";
$result4 = testSearch('shirt', 'price_desc');
$count4 = countProducts($result4);
echo "<div class='count'>Found $count4 products</div>";
echo "<div class='results'>$result4</div>";
echo "</div>";

// Test 5: Filter by size
echo "<div class='test-case'>";
echo "<h2>Test 5: Filter by Size (Medium)</h2>";
$result5 = testSearch('shirt', 'size_M');
$count5 = countProducts($result5);
echo "<div class='count'>Found $count5 products</div>";
echo "<div class='results'>$result5</div>";
echo "</div>";
