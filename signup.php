<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database Connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "trendswap_local";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$message = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data and sanitize
    $first_name = $conn->real_escape_string($_POST['first_name']);
    $last_name = $conn->real_escape_string($_POST['last_name']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash password

    // Insert user into database
    $sql = "INSERT INTO users (username, email, password, created_at) 
            VALUES ('$first_name $last_name', '$email', '$password', NOW())";

    if ($conn->query($sql) === TRUE) {
        $_SESSION["user_id"] = $conn->insert_id;
        $_SESSION["username"] = "$first_name $last_name";
        header("Location: index.html"); // Redirect to index.html after signup
        exit();
    } else {
        $message = "Error: " . $conn->error;
    }
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">

    <style>
        /* Sign-Up Page Styles */
        .signup-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 80vh;
            background-color: #f9f9f9;
        }

        .signup-box {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
            width: 350px;
        }

        .signup-box h2 {
            font-size: 24px;
            margin-bottom: 20px;
            font-weight: 700;
        }

        .signup-box input {
            width: 90%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 0;
            font-family: 'Montserrat', sans-serif;
            font-size: 14px;
        }

        .signup-box button {
            width: 95%;
            padding: 10px;
            font-family: 'Montserrat', sans-serif;
            font-weight: bold;
            border: 2px solid black;
            background: white;
            color: black;
            cursor: pointer;
            transition: background 0.3s ease, color 0.3s ease;
            border-radius: 0;
        }

        .signup-box button.active {
            background: black;
            color: white;
        }

        .signup-box p {
            font-size: 14px;
            margin-top: 15px;
        }

        .signup-box p a {
            color: black;
            text-decoration: none;
            font-weight: bold;
        }

        .signup-box p a:hover {
            color: gray;
        }
    </style>
</head>
<body>
    <div class="delivery-info">
        Free Delivery Available | Find out more
    </div>
    <div class="navbar">
        <div class="navbar-left">
            <div class="navbar-logo">
                <img src="Images/Logo.jpg" alt="Logo">
            </div>
            <div class="navbar-links">
                <a href="index.html">HOME</a>
                <a href="men.php">MEN</a>
                <a href="woman.php">WOMAN</a>
                <a href="kids.php">KIDS</a>
                <a href="accessories.php">ACCESSORIES</a>
                <a href="babies.php">BABIES</a>
                <a href="aboutus.html" class="active">ABOUT US</a>
            </div>
        </div>
        <div class="navbar-right">
            <div class="search-icon">
                <img src="Images/Search.png" alt="Search" id="search-icon">
            </div>
            <div class="bag-icon">
                <img src="Images/Bag.png" alt="Bag">
            </div>
            <div class="person-icon">
                <img src="Images/Person.png" alt="Person" id="person-icon">
            </div>
            <div class="search-bar" id="search-bar">
                <input type="text" placeholder="Search...">
            </div>
        </div>
    </div>

    <div class="signup-container">
        <div class="signup-box">
            <h2>Sign Up</h2>
            <?php if (!empty($message)) { echo "<p style='color: red;'>$message</p>"; } ?>
            <form id="signup-form" action="signup.php" method="POST">
                <input type="text" name="first_name" placeholder="First Name" required>
                <input type="text" name="last_name" placeholder="Last Name" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" id="signup-button">Sign Up</button>
                <p>Already have an account? <a href="login.html">Login</a></p>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const searchIcon = document.getElementById('search-icon');
            const searchBar = document.getElementById('search-bar');
            let isSearchOpen = false;

            searchIcon.addEventListener('click', (event) => {
                event.stopPropagation();
                if (isSearchOpen) {
                    searchBar.style.width = '0';
                    searchBar.classList.remove('open');
                } else {
                    searchBar.style.width = '200px';
                    searchBar.classList.add('open');
                }
                isSearchOpen = !isSearchOpen;
            });

            document.addEventListener('click', (event) => {
                if (!searchBar.contains(event.target) && !searchIcon.contains(event.target)) {
                    searchBar.style.width = '0';
                    searchBar.classList.remove('open');
                    isSearchOpen = false;
                }
            });

            // Change button appearance on input
            const inputs = document.querySelectorAll('#signup-form input');
            const signupButton = document.getElementById('signup-button');

            function toggleSignupButtonState() {
                const allFieldsFilled = Array.from(inputs).every(input => input.value.trim() !== "");
                signupButton.classList.toggle('active', allFieldsFilled);
            }

            inputs.forEach(input => input.addEventListener('input', toggleSignupButtonState));
        });
    </script>
</body>
</html>
