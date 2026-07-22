<?php
session_start();
include "config.php";

$error_msg = "";

if (isset($_POST['register'])) {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];
    $role     = 'customer';

    if (empty($name) || empty($email) || empty($password)) {
        $error_msg = "Please fill in all required fields!";
    } elseif ($password !== $confirm) {
        $error_msg = "Passwords do not match!";
    } elseif (strlen($password) < 6) {
        $error_msg = "Password must be at least 6 characters long!";
    } else {
        $check_stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        
        if ($check_stmt->get_result()->num_rows > 0) {
            $error_msg = "This email is already registered!";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $insert_stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            $insert_stmt->bind_param("ssss", $name, $email, $hashed_password, $role);

            if ($insert_stmt->execute()) {
                echo "<script>alert('Registration successful! Please login.'); window.location='login.php';</script>";
                exit();
            } else {
                $error_msg = "System error. Registration failed.";
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
    <title>Register - Golden Pearl</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Register New Account</h2>
    <form action="register.php" method="POST">
        <?php if (!empty($error_msg)) { ?>
            <p style="background: #f8d7da; color: #721c24; border-left: 4px solid #f5c6cb;"><?php echo htmlspecialchars($error_msg); ?></p>
        <?php } ?>

        <label for="name">Full Name:</label>
        <input type="text" id="name" name="name" required>

        <label for="email">Email Address:</label>
        <input type="email" id="email" name="email" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>

        <label for="confirm_password">Confirm Password:</label>
        <input type="password" id="confirm_password" name="confirm_password" required>

        <input type="submit" name="register" value="Register Now">
        <div style="text-align: center; margin-top: 15px;">
            <a href="login.php">Back to Login</a>
        </div>
    </form>
</body>
</html>