<?php
// Start the session to use session variables
session_start();

// Destroy all session variables
session_unset();

// Destroy the session
session_destroy();

// Wait 0.5 seconds before redirecting
echo "<script>
    setTimeout(function() {
        window.location.href = 'login.php';  // Redirect to login page
    }, 500);  // 0.5 seconds delay
    </script>";
exit();
?>
