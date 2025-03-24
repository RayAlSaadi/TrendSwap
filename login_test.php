<?php

include 'db_connect.php';

// Test credentials 
$test_email = "ibrahik@gmail.com";
$test_password = "87654321";

// Initialize message
$message = "";

// login logic
if (!empty($test_email)) {
    $email = mysqli_real_escape_string($conn, $test_email);
      
    // Check database connection
    if (!$conn) {
        die("❌ Database connection failed: " . mysqli_connect_error());
    }
    
 
    $result = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");

	// Check database connection
	if (!$conn) {
    die("❌ Database connection failed: " . mysqli_connect_error());
}

    
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        if (password_verify($test_password, $row["password"])) {
            $_SESSION["login"] = true;
            $_SESSION["user_id"] = $row["user_id"];
            $message = "✅ Login Successful! User Authenticated.";
        } else {
            $message = "❌ Wrong Password.";
        }
    } else {
        $message = "❌ User Not Registered.";
    }
} else {
    $message = "❌ Test Email Not Provided.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Test</title>
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f5f5f5;
        }
        .test-container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
            width: 400px;
        }
        h1 {
            margin-bottom: 20px;
        }
        .message {
            font-size: 18px;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .success {
            background-color: #e6f7e6;
            color: green;
            border: 1px solid green;
        }
        .error {
            background-color: #f8d7da;
            color: red;
            border: 1px solid red;
        }
        .details {
            text-align: left;
            margin-top: 20px;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="test-container">
        <h1>Login Authentication Test</h1>
        
        <?php if(strpos($message, "✅") !== false): ?>
            <div class="message success"><?php echo $message; ?></div>
        <?php else: ?>
            <div class="message error"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <div class="details">
            <p><b>Test Email:</strong> <?php echo htmlspecialchars($test_email); ?></p>
            <p><b>Test Password:</strong> <?php echo str_repeat("*", strlen($test_password)); ?></p>
            <p><b>User ID (if logged in):</strong> <?php echo isset($_SESSION["user_id"]) ? $_SESSION["user_id"] : "None"; ?></p>
        </div>
    </div>
</body>
</html>