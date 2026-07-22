<?php
session_start();
include "config.php";

if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT user_id, name, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password']) || $password === $user['password']) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['name']    = $user['name'];
            $_SESSION['role']    = $user['role'];

            if ($user['role'] == 'admin') header("Location: admin.php");
            elseif ($user['role'] == 'staff') header("Location: staff.php");
            else header("Location: customer.php");
            exit();
        } else {
            echo "<script>alert('Incorrect password!'); window.location='login.php';</script>";
            exit();
        }
    } else {
        echo "<script>alert('Email does not exist!'); window.location='login.php';</script>";
        exit();
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Golden Pearl</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>System Login</h2>
    <form action="login.php" method="POST">
        <label for="email">Email Address:</label>
        <input type="email" id="email" name="email" required placeholder="email@example.com">

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required placeholder="Enter password">

        <input type="submit" name="login" value="Login">
        
        <div style="text-align: center; margin-top: 15px;">
            <span>Don't have an account? </span>
            <a href="register.php">Register New Account</a>
        </div>
    </form>
</body>
</html>