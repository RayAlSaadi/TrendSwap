<?php
$host = "localhost:3306";
$username = "cs2team2";  
$password = "VJNGzpwV9pVxZwb";  
$dbname = "cs2team2_db";  

$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
} else {
    echo "Database connection successful!";
}
?>