<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<?php
session_start();
include "config.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}
$total_customers = $conn->query(
    "SELECT * FROM users WHERE role='customer'"
)->num_rows;

$total_staff = $conn->query(
    "SELECT * FROM users WHERE role='staff'"
)->num_rows;

$total_bookings = $conn->query(
    "SELECT * FROM bookings"
)->num_rows;

$pending_bookings = $conn->query(
    "SELECT * FROM bookings WHERE status='Pending'"
)->num_rows;

?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>

<div class="dashboard-container">

    <!-- SIDEBAR -->
    <?php include "sidebar_admin.php"; ?>

    <!-- MAIN CONTENT -->
    <div class="main-content">

        <h1>Welcome, <?php echo $_SESSION['name']; ?></h1>

        <h3>Administrator Dashboard</h3>

<p><strong>Role:</strong> Administrator</p>

<p>
    Manage staff accounts and monitor the hall booking system.
</p>

<h3>System Summary</h3>

<p>Total Customers : <?php echo $total_customers; ?></p>

<p>Total Staff : <?php echo $total_staff; ?></p>

<p>Total Bookings : <?php echo $total_bookings; ?></p>

<p>Pending Requests : <?php echo $pending_bookings; ?></p>

<br>

<a href="add_staff.php" class="book-btn">
    Add Staff
</a>

<a href="manage_staff.php" class="book-btn">
    Manage Staff
</a>

    </div>

</div>

</body>
</html>