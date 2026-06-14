<?php
session_start();
include "config.php";

// Pastikan hanya admin sahaja yang boleh mengakses halaman ini
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

if (isset($_POST['add_staff'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = 'staff'; // Ditetapkan terus sebagai 'staff'

    // Menyemak sekiranya e-mel sudah digunakan
    $check_email = "SELECT * FROM users WHERE email='$email'";
    $run_check = $conn->query($check_email);

    if ($run_check->num_rows > 0) {
        $message = "Email already registered!";
    } else {
        // Memasukkan data staf baharu ke dalam database
        $sql = "INSERT INTO users (name, email, password, role)
                VALUES ('$name', '$email', '$password', '$role')";

        if ($conn->query($sql)) {
            $message = "Staff added successfully!";
        } else {
            $message = "Error: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Staff</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>

<div class="dashboard-container">

    <?php include "sidebar_admin.php"; ?>

    <div class="main-content">

        <h2>Add New Staff</h2>

<?php 
if(isset($message)) { 
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

    <button type="submit" name="add_staff">Add Staff</button>
</form>

    </div>

</div>

</body>
</html>