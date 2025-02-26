<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "trendswap_local";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
} else {
    echo "Database connection successful!";
}
?>
