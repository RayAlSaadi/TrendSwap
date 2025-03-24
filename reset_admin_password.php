<?php
// Include your database connection
require 'db_connect.php';

// The plain password we want to set
$new_password = "12345678";

// Generate the password hash using PHP's built-in function
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

// Display the generated hash for reference
echo "Generated hash for password '$new_password': $hashed_password<br><br>";

// Update existing admin password
$admin_username = "admin_user";
$update_sql = "UPDATE admins SET password = ? WHERE username = ?";
$stmt = $conn->prepare($update_sql);
$stmt->bind_param("ss", $hashed_password, $admin_username);

if ($stmt->execute()) {
    echo "Password for '$admin_username' has been reset to '$new_password'<br>";
} else {
    echo "Error updating password for '$admin_username': " . $conn->error . "<br>";
}

// Check existing roles in the database to avoid the truncation error
$roles_query = "SELECT DISTINCT role FROM admins";
$roles_result = $conn->query($roles_query);
$valid_roles = [];

if ($roles_result->num_rows > 0) {
    echo "<br>Valid roles in the database:<br>";
    while($row = $roles_result->fetch_assoc()) {
        $valid_roles[] = $row['role'];
        echo "- " . $row['role'] . "<br>";
    }
} else {
    echo "<br>No existing roles found in the database.<br>";
}

// Create new admin with proper role
if (count($valid_roles) > 0) {
    $new_admin_username = "new_admin";
    $new_admin_role = $valid_roles[0]; // Use the first valid role
    
    // First check if this admin already exists
    $check_sql = "SELECT * FROM admins WHERE username = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $new_admin_username);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        echo "<br>Admin '$new_admin_username' already exists. Updating password only.<br>";
        $update_sql = "UPDATE admins SET password = ? WHERE username = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ss", $hashed_password, $new_admin_username);
        
        if ($update_stmt->execute()) {
            echo "Password for '$new_admin_username' has been updated to '$new_password'<br>";
        } else {
            echo "Error updating password: " . $conn->error . "<br>";
        }
    } else {
        // Create new admin
        $insert_sql = "INSERT INTO admins (username, password, role, created_at) VALUES (?, ?, ?, NOW())";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("sss", $new_admin_username, $hashed_password, $new_admin_role);
        
        if ($insert_stmt->execute()) {
            echo "<br>Created new admin '$new_admin_username' with role '$new_admin_role' and password '$new_password'<br>";
        } else {
            echo "<br>Error creating new admin: " . $conn->error . "<br>";
        }
    }
} else {
    echo "<br>Cannot create new admin without knowing valid roles.<br>";
}

echo "<br><a href='admin_login.php'>Go to admin login page</a>";

// Close connection
$conn->close();
?>