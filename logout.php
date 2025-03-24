<?php
session_start();

session_unset();

session_destroy();

// loading script
echo "<script>
    setTimeout(function() {
        window.location.href = 'login.php';  // Redirect to login page
    }, 500);  // 0.5 seconds delay
    </script>";
exit();
?>
