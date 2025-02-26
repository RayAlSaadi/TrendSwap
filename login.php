<?php
session_start();
$servername = "localhost";
$username = "root";  
$password = "";  
$dbname = "trendswap_local";  

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    // Fetch user from database
    $sql = "SELECT id, username, password FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Temporary: No password hashing (change later for security)
        if ($password == $user['password']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            // Redirect to home page
            header("Location: index.html");
            exit();
        } else {
            echo "<script>alert('Invalid password. Try again.'); window.location.href='login.html';</script>";
        }
    } else {
        echo "<script>alert('No account found with that email.'); window.location.href='login.html';</script>";
    }
}
?>
