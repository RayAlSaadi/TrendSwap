<?php
session_start(); 
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit(); 
}
require 'db_connect.php';
$user_id = $_SESSION['user_id'];
$sql = "SELECT first_name FROM users WHERE user_id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($first_name);
    if ($stmt->fetch()) {
    } else {
        echo "Error fetching user details!";
    }
    $stmt->close();
} else {
    echo "Error preparing the query: " . $conn->error;
}
if (isset($_POST['update_details'])) {
    $email = $_POST['email'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $sql = "SELECT password FROM users WHERE user_id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $stmt->bind_result($hashed_password);
        $stmt->fetch();
        $stmt->close();
        if (password_verify($current_password, $hashed_password)) {
            $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET email = ?, password = ? WHERE user_id = ?";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("ssi", $email, $new_hashed_password, $_SESSION['user_id']);
                if ($stmt->execute()) {
                    echo "Details updated successfully!";
                } else {
                    echo "Error updating details: " . $stmt->error;
                }
                $stmt->close();
            } else {
                echo "Error preparing the SQL query: " . $conn->error;
            }
        } else {
            echo "Current password is incorrect!";
        }
    } else {
        echo "Error preparing the query: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Page</title>
	 <link rel="stylesheet" href="css/styles.css">

</head>

<style>

/* ======================== */
/*        DARK MODE FIX    */
/* ======================== */

body.darkmode .user-page .change-details-container {
  background-color: var(--card-bg) !important;
  color: var(--text-color) !important;
  border: 1px solid var(--border-color) !important;
}

body.darkmode .user-page .box {
  background-color: var(--base-color) !important;
  color: var(--text-color) !important;
  border: 1px solid var(--border-color) !important;
  font-family: 'Montserrat', sans-serif;
  font-size: 14px;
  padding: 10px;
  border-radius: 0;
}

body.darkmode .user-page .buttons,
body.darkmode .user-page .button-logout {
  width: 95%;
  padding: 10px;
  font-family: 'Montserrat', sans-serif;
  font-weight: bold;
  border: 2px solid black;
  background: var(--base-color) !important;
  color: var(--text-color) !important;
  cursor: pointer;
  transition: background 0.3s ease, color 0.3s ease;
  border-radius: 0;
}

/* ================= USER PAGE DARK MODE FIX ================= */

/* Make background of .change-details-container same as login box in darkmode */
body.darkmode .user-page .change-details-container {
  background-color: var(--card-bg) !important;
  color: var(--text-color) !important;
  border: 1px solid var(--border-color) !important;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2) !important;
  transition: background-color 0.3s ease, color 0.3s ease;
}

/* Center the form and buttons */
.user-page form {
  display: flex;
  flex-direction: column;
  align-items: center;
}

/* Input styles matching login/signup */
.user-page .box {
  width: 90%;
  padding: 10px;
  margin-bottom: 15px;
  border: 1px solid #ccc;
  border-radius: 0;
  font-family: 'Montserrat', sans-serif;
  font-size: 14px;
}

/* Buttons matching login/signup */
.user-page .buttons,
.user-page .button-logout {
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
  text-align: center;
}

.user-page .buttons:hover,
.user-page .button-logout:hover {
  background-color: black;
  color: white;
}

/* Apply dark mode background and text color to buttons */
body.darkmode .user-page .box,
body.darkmode .user-page .buttons,
body.darkmode .user-page .button-logout {
  background-color: var(--base-color) !important;
  color: var(--text-color) !important;
  border: 1px solid var(--border-color) !important;
}



  /* NEW DARKMODE FIXES BELOW */
body.darkmode .user-page .container .welcome-message {
  color: var(--text-color) !important;
}

body.darkmode .user-page .container .profile-icon-container {
  background-color: var(--card-bg) !important;
  padding: 15px;
  display: flex;
  justify-content: center;
  border-radius: 10px;
}

body.darkmode .user-page .container .profile-image {
  filter: brightness(1.2);
}



body.darkmode .welcome-message,
body.darkmode .profile-icon-container {
  filter: invert(1);
}


</style>
<body class="user-page">
<?php include 'phpLogic/header.php'; ?>
    <div class="container">
        <div class="profile-icon-container">
            <img src="Images/user-profile.png" alt="Profile Icon" class="profile-image">
        </div>
        <div class="welcome-message">
            <?php
            if (isset($first_name)) {
                echo "Welcome, " . htmlspecialchars($first_name) . "!";
            }
            ?>
        </div>
        <div class="change-details-container">
            <h2 id="user-change">CHANGE DETAILS</h2>
            <form action="" method="POST">
    <input type="text" name="email" class="box" placeholder="Email" value="<?php echo isset($email) ? $email : ''; ?>">
    <input type="password" name="current_password" class="box" placeholder="Current Password" required>
    <input type="password" name="new_password" class="box" placeholder="New Password" required>
    <button type="submit" name="update_details" class="buttons">Update Details</button>
</form>
        </div>
        <div class="recently-purchased">
			<a href="previous_orders.php" style="text-decoration: none; color: inherit;">
            <h2 id="user-change">Previous Ordered Items</h2>
            </a>
        </div>
            <div class="logout-button-container">
    	<form action="logout.php" method="POST">
        <button type="submit" name="logout" class="button-logout">Log Out</button>
    </form>
</div>
    </div>
<?php include 'phpLogic/footer.php'; ?>
    <?php include 'moonoverlay.php'; ?>

<script>
    const searchIcon = document.getElementById('search-icon');
    const searchBar = document.getElementById('search-bar');
    searchIcon.addEventListener('click', () => {
        searchBar.classList.toggle('active');
    });
</script>
</body>
</html>













