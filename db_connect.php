<?php
$host = "localhost";
$username = "cs2team2";  
$password = "VJNGzpwV9pVxZwb";  
$dbname = "cs2team2_db";  

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
