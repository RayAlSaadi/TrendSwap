<?php
session_start();
require 'db_connect.php';

if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['Super Admin', 'Manager'])) {
    header("Location: admin_dashboard.php");
    exit();
}

if (isset($_POST["submit"])) {
    $username = mysqli_real_escape_string($conn, $_POST["username"]);
    $password = $_POST["password"];
    
    $adminQuery = $conn->prepare("SELECT * FROM admins WHERE username = ?");
    $adminQuery->bind_param("s", $username);
    $adminQuery->execute();
    $result = $adminQuery->get_result();
    
    if ($result->num_rows > 0) {
        $adminRow = $result->fetch_assoc();
        if (password_verify($password, $adminRow["password"])) {
            $_SESSION["login"] = true;
            $_SESSION["user_id"] = $adminRow["admin_id"];
            $_SESSION['role'] = $adminRow['role'];
            $_SESSION['username'] = $adminRow['username'];
            $_SESSION['login_success'] = "Admin login successful! Redirecting to dashboard...";
            
            // Redirect after delay
            header("Refresh: 2; URL=admin_dashboard.php");
            exit();
        } else {
            $error_message = "Wrong password. Please try again.";
        }
    } else {
        $error_message = "Admin not found. Please check your credentials.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - TrendSwap</title>
    <link rel="stylesheet" type="text/css" href="css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #f9f9f9;
        }
        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 80vh;
            padding: 20px;
        }
        .login-box {
            background-color: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        .login-box h2 {
            text-align: center;
            margin-bottom: 20px;
            font-weight: 700;
        }
        .login-box .form-group {
            margin-bottom: 20px;
        }
        .login-box .btn-primary {
            background-color: #000;
            border-color: #000;
            width: 100%;
            padding: 10px;
            font-weight: 600;
        }
        .login-box .btn-primary:hover {
            background-color: #333;
            border-color: #333;
        }
        .error-message {
            color: #dc3545;
            margin-bottom: 15px;
            text-align: center;
        }
        .success-message {
            color: #28a745;
            margin-bottom: 15px;
            text-align: center;
        }
    </style>
</head>
<body>
    <?php include 'phpLogic/header.php'; ?>
    
    <div class="login-container">
        <div class="login-box">
            <h2>Admin Login</h2>
            
            <?php if (isset($_SESSION['login_success'])): ?>
                <p class="success-message"><?php echo $_SESSION['login_success']; ?></p>
                <?php unset($_SESSION['login_success']); ?>
            <?php endif; ?>
            
            <?php if (isset($error_message)): ?>
                <p class="error-message"><?php echo $error_message; ?></p>
            <?php endif; ?>
            
            <form action="" method="post">
                <div class="form-group">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" name="username" id="username" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>
                
                <button type="submit" name="submit" class="btn btn-primary">Login</button>
            </form>
        </div>
    </div>
    
    <?php include 'phpLogic/footer.php'; ?>
</body>
</html>