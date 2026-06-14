<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include "config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id = $_SESSION['user_id'];
$role = $_SESSION['role']; // Ambil role dari session untuk tentukan sidebar

/* UPDATE PROFILE */
if (isset($_POST['update'])) {

    $name = $_POST['name'];
    $email = $_POST['email'];

    $update = "UPDATE users 
               SET name='$name', email='$email'
               WHERE user_id='$id'";

    if ($conn->query($update)) {

        // update session name sekali
        $_SESSION['name'] = $name;

        echo "<script>alert('Profile updated successfully!');</script>";

    } else {
        echo "Error: " . $conn->error;
    }
}

/* GET USER DATA */
$sql = "SELECT * FROM users WHERE user_id='$id'";
$result = $conn->query($sql);
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Profile</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>

<div class="dashboard-container">

    <?php 
        if ($role == 'admin') {
            include "sidebar_admin.php";
        } elseif ($role == 'staff') {
            include "sidebar_staff.php";
        } else {
            include "sidebar_customer.php";
        }
    ?>

    <div class="main-content">

        <h2>Profile</h2>

<form method="POST">

    <label>Name:</label><br>
    <input type="text" name="name"
    value="<?php echo $user['name']; ?>" required>
    <br><br>

    <label>Email:</label><br>
    <input type="email" name="email"
    value="<?php echo $user['email']; ?>" required>
    <br><br>

    <label>Role:</label><br>
    <input type="text"
    value="<?php echo ucfirst($user['role']); ?>" readonly style="background-color: #f2f2f2; cursor: not-allowed;">
    <br><br>

    <button type="submit" name="update">
        Update Profile
    </button>

</form>

    </div>

</div>

</body>
</html>