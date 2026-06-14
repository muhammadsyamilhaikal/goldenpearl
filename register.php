<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<?php
include "config.php";

if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $sql = "INSERT INTO users (name, email, password, role)
            VALUES ('$name', '$email', '$password', '$role')";

    if ($conn->query($sql)) {
        $message = "Register successful!";
    } else {
        $message = "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<div class="auth-container">

<h2 style="color:white">Register</h2>

<?php
if(isset($message)){
    echo "<p>$message</p>";
}
?>

<form method="POST">
    <label>Name:</label><br>
    <input type="text" name="name" required><br><br>

    <label>Email:</label><br>
    <input type="email" name="email" required><br><br>

    <label>Password:</label><br>
    <input type="password" name="password" required><br><br>

    <label>Role:</label><br>
    <select name="role">
        <option value="customer">Customer</option>
        <option value="staff">Staff</option>
    </select><br><br>

    <button type="submit" name="register">Register</button>
</form>

<p>
    Already have an account?
    <a href="login.php">Login Here</a>
</p>
</div>

</body>
</html>