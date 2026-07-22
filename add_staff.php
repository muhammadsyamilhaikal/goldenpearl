<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('ACCESS DENIED!'); window.location='login.php';</script>";
    exit();
}
include "config.php";

$error_msg = "";

if (isset($_POST['add_staff'])) {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $role     = 'staff';

    if (empty($name) || empty($email) || empty($password)) {
        $error_msg = "Please fill in all fields!";
    } elseif (strlen($password) < 6) {
        $error_msg = "Password must be at least 6 characters!";
    } else {
        $check_stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        
        if ($check_stmt->get_result()->num_rows > 0) {
            $error_msg = "This email is already in use!";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $insert_stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            $insert_stmt->bind_param("ssss", $name, $email, $hashed_password, $role);

            if ($insert_stmt->execute()) {
                echo "<script>alert('New staff account successfully registered!'); window.location='manage_staff.php';</script>";
                exit();
            } else {
                $error_msg = "System error. Failed to register staff.";
            }
            $insert_stmt->close();
        }
        $check_stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Staff - Golden Pearl</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="dashboard-container">
    <?php include "sidebar_admin.php"; ?>
    <div class="main-content">
        <h2>Add New Staff Account</h2>
        <form action="add_staff.php" method="POST" style="max-width: 500px;">
            <?php if (!empty($error_msg)) { ?><p style="background: #f8d7da; color: #721c24; border-left: 4px solid #f5c6cb;"><?php echo htmlspecialchars($error_msg); ?></p><?php } ?>
            <label for="name">Staff Full Name:</label>
            <input type="text" id="name" name="name" required>
            <label for="email">Staff Email Address:</label>
            <input type="email" id="email" name="email" required>
            <label for="password">Temporary Password:</label>
            <input type="password" id="password" name="password" required>
            <input type="submit" name="add_staff" value="Register Staff" class="book-btn">
            <a href="manage_staff.php" style="display: block; text-align: center; margin-top: 15px;">Back</a>
        </form>
    </div>
</div>
</body>
</html>