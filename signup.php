<?php
session_start();

// Database connection
include('db_connect.php');

// Check database connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

if (isset($_POST['submit'])) {
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    $errors = [];
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    	$errors[] = "Invalid email format";
    }
    
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match";
    }
    
    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters";
    }

    // Check for existing email using prepared statement
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $errors[] = "Email already exists";
    }
    $stmt->close();

    // Proceed if no errors
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $first_name, $last_name, $email, $hashed_password);
        
        if ($stmt->execute()) {
            // Success, store the success message in session
            $_SESSION['signup_success'] = "Registration successful! Please log in.";
            unset($_SESSION['form_data']);
            unset($_SESSION['signup_errors']);
        } else {
            error_log("Database error: " . $stmt->error);
            $errors[] = "Registration failed. Please try again.";
        }
        $stmt->close();
    }
    
    // If errors, preserve them in session and reload the form
    if (!empty($errors)) {
        $_SESSION['signup_errors'] = $errors;
        $_SESSION['form_data'] = $_POST;
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
</head>
<style>
    /* Sign-Up Page Styles */
    .signup-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 80vh;
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
        width: 89%;
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

.signup-box button:hover {
    background: black;
    color: white;
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

    /* Success message style */
    .success-message {
        color: green;
        background-color: #e6f7e6;
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid green;
        border-radius: 5px;
        font-weight: bold;
        text-align: center;
    }

    /* Error messages style */
    .error-messages {
        color: red;
        background-color: #f8d7da;
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid red;
        border-radius: 5px;
        font-weight: bold;
        text-align: center;
    }

    .error-messages li {
        margin: 5px 0;
    }
</style>
<body>
    <?php include 'phpLogic/header.php'; ?>
    <div class="signup-container">
        <div class="signup-box">
            <h2>Sign Up</h2>
            
            <!-- Display success message if set -->
            <?php
            if (isset($_SESSION['signup_success'])) {
                echo '<p class="success-message">' . htmlspecialchars($_SESSION['signup_success']) . '</p>';
                unset($_SESSION['signup_success']);  // Clear success message after displaying
            }
                
            // Display errors if set
            if (isset($_SESSION['signup_errors']) && !empty($_SESSION['signup_errors'])) {
                echo '<ul class="error-messages">';
                foreach ($_SESSION['signup_errors'] as $error) {
                    echo '<li>' . htmlspecialchars($error) . '</li>';
                }
                echo '</ul>';
                unset($_SESSION['signup_errors']);  // Clear errors after displaying them
            }
            ?>
            
            <form id="signup-form" action="signup.php" method="POST">
                <input type="text" name="first_name" placeholder="First name" required value="<?= isset($_SESSION['form_data']['first_name']) ? htmlspecialchars($_SESSION['form_data']['first_name']) : ''; ?>">
                <input type="text" name="last_name" placeholder="Last name" required value="<?= isset($_SESSION['form_data']['last_name']) ? htmlspecialchars($_SESSION['form_data']['last_name']) : ''; ?>">
                <input type="email" name="email" placeholder="Email" required value="<?= isset($_SESSION['form_data']['email']) ? htmlspecialchars($_SESSION['form_data']['email']) : ''; ?>">
                <input type="password" name="password" placeholder="Password" required>
                <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                <button type="submit" id="signup-button" name="submit">Sign Up</button>
                <p>Already have an account? <a href="login.php">Login</a></p>
            </form>
        </div>
    </div>
    <?php include 'phpLogic/footer.php'; ?>
    
        <?php include 'moonoverlay.php'; ?>

</body>
</html>