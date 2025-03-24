<?php
session_start();
require 'db_connect.php';

if (isset($_POST["submit"])) {
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $password = $_POST["password"];

    $result = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        if (password_verify($password, $row["password"])) {
            $_SESSION["login"] = true;
            $_SESSION["user_id"] = $row["user_id"];
            $_SESSION['login_success'] = "Login successful! Redirecting in ....";
        } else {
            $_SESSION['login_error'] = "Wrong password. Please try again.";
        }
    } else {
        $_SESSION['login_error'] = "User not registered. Please check your credentials.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login</title>
  <link rel="stylesheet" href="css/styles.css" />
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
  <style>
    .login-container {
        display: flex;
        justify-content: center;
        align-items: center;
        flex-grow: 1;
        width: 100%;
        min-height: calc(100vh - 200px);
        background-color: var(--base-color);
        padding: 0;
        margin: 0;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    .login-box {
        background-color: var(--card-bg);
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        text-align: center;
        width: 350px;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    .login-box h2 {
        font-size: 24px;
        margin-bottom: 20px;
        font-weight: 700;
    }

    .login-box input {
        width: 90%;
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #ccc;
        border-radius: 0;
        font-family: 'Montserrat', sans-serif;
        font-size: 14px;
    }

    .login-box button {
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

    .login-box button:hover {
        background: black;
        color: white;
    }

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

    .signup-section {
        font-size: 14px;
        margin-top: 15px;
    }

    .signup-section a {
        font-weight: bold;
        text-decoration: none;
        color: var(--text-color);
    }

    .signup-section a:hover {
        color: gray;
    }

    body.darkmode .login-box {
        background-color: var(--card-bg) !important;
        color: var(--text-color) !important;
    }

    body.darkmode .login-box input,
    body.darkmode .login-box button {
        background-color: var(--base-color) !important;
        color: var(--text-color) !important;
        border: 1px solid var(--border-color) !important;
    }


  </style>
</head>
<body>
    <?php include 'phpLogic/header.php'; ?>
    <div class="login-container">
        <div class="login-box">
            <h2>Login</h2>
            <?php
            if (isset($_SESSION['login_success'])) {
                echo '<p class="success-message">' . htmlspecialchars($_SESSION['login_success']) . '</p>';
                unset($_SESSION['login_success']);
                echo "<script>
                    setTimeout(function() {
                        window.location.href = 'index.php';
                    }, 2500);
                </script>";
                exit();
            }

            if (isset($_SESSION['login_error'])) {
                echo '<p class="error-messages">' . htmlspecialchars($_SESSION['login_error']) . '</p>';
                unset($_SESSION['login_error']);
            }
            ?>
            <form action="" method="post" autocomplete="off">
                <input type="email" name="email" placeholder="Email" required />
                <input type="password" name="password" placeholder="Password" required />
                <button type="submit" name="submit">Login</button>
            </form>
            <div class="signup-section">
                <p>Don't have an account? <a href="signup.php">Sign Up</a></p>
            </div>
        </div>
    </div>
    <?php include 'moonoverlay.php'; ?>
    <?php include 'phpLogic/footer.php'; ?>
</body>
</html>
