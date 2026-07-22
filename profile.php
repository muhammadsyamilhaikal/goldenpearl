<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include "config.php";

$user_id = $_SESSION['user_id'];
$error_msg = "";
$success_msg = "";

$stmt = $conn->prepare("SELECT name, email, password FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (isset($_POST['update_profile'])) {
    $name = trim($_POST['name']);
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];

    if (empty($name)) {
        $error_msg = "Full name cannot be empty!";
    } else {
        if (!empty($old_password) || !empty($new_password)) {
            if (empty($old_password) || empty($new_password)) {
                $error_msg = "Please fill in both password fields if you wish to change your password!";
            } elseif (!password_verify($old_password, $user['password']) && $old_password !== $user['password']) {
                $error_msg = "Your old password is incorrect!";
            } elseif (strlen($new_password) < 6) {
                $error_msg = "New password must be at least 6 characters!";
            } else {
                $hashed_pw = password_hash($new_password, PASSWORD_DEFAULT);
                $up_stmt = $conn->prepare("UPDATE users SET name = ?, password = ? WHERE user_id = ?");
                $up_stmt->bind_param("ssi", $name, $hashed_pw, $user_id);
                if ($up_stmt->execute()) { $_SESSION['name'] = $name; $success_msg = "Profile & password updated successfully!"; }
                $up_stmt->close();
            }
        } else {
            $up_stmt = $conn->prepare("UPDATE users SET name = ? WHERE user_id = ?");
            $up_stmt->bind_param("si", $name, $user_id);
            if ($up_stmt->execute()) { $_SESSION['name'] = $name; $success_msg = "Profile name updated successfully!"; }
            $up_stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Golden Pearl</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="dashboard-container">
    <?php if ($_SESSION['role'] === 'admin') include "sidebar_admin.php"; elseif ($_SESSION['role'] === 'staff') include "sidebar_staff.php"; else include "sidebar_customer.php"; ?>
    <div class="main-content">
        <h2>Update Profile & Password</h2>
        <form action="profile.php" method="POST" style="max-width: 500px;">
            <?php if (!empty($error_msg)) { ?><p style="background: #f8d7da; color: #721c24; border-left: 4px solid #f5c6cb;"><?php echo htmlspecialchars($error_msg); ?></p><?php } ?>
            <?php if (!empty($success_msg)) { ?><p style="background: #d4edda; color: #155724; border-left: 4px solid #c3e6cb;"><?php echo htmlspecialchars($success_msg); ?></p><?php } ?>

            <label for="email">Email Address (Cannot be changed):</label>
            <input type="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled style="background: #eee;">
            <label for="name">Full Name:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($_SESSION['name']); ?>" required>
            <hr style="margin: 20px 0; border: 0; border-top: 1px solid #ccc;">
            <p style="box-shadow: none; padding: 0; font-size: 14px; color: #666; margin-bottom: 15px;">* Leave blank if you do not wish to change your password.</p>
            <label for="old_password">Old Password:</label>
            <input type="password" id="old_password" name="old_password">
            <label for="new_password">New Password:</label>
            <input type="password" id="new_password" name="new_password">
            <input type="submit" name="update_profile" value="Save Changes" class="book-btn">
        </form>
    </div>
</div>
</body>
</html>