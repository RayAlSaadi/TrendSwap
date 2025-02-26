<?php
$servername = "localhost";
$username = "root";  // Adjust according to your DB credentials
$password = "";  // Adjust accordingly
$dbname = "trendswap_local";  // Change to your actual database name

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

