<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<?php
session_start();
include "config.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'staff') {
    header("Location: login.php");
    exit();
}

$pending = $conn->query(
    "SELECT * FROM bookings WHERE status='Pending'"
)->num_rows;

$approved = $conn->query(
    "SELECT * FROM bookings WHERE status='Approved'"
)->num_rows;

$rejected = $conn->query(
    "SELECT * FROM bookings WHERE status='Rejected'"
)->num_rows;

?>

<!DOCTYPE html>
<html>
<head>
    <title>Staff</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>

<div class="dashboard-container">

    <!-- SIDEBAR -->
    <?php include "sidebar_staff.php"; ?>

    <!-- MAIN CONTENT -->
    <div class="main-content">

        <h1>Welcome, <?php echo $_SESSION['name']; ?></h1>

        <h3>Staff Dashboard</h3>

<p><strong>Role:</strong> Staff</p>

<p>
    Review booking requests, approve or reject bookings,
    and manage hall schedules.
</p>

<h3>Responsibilities</h3>

<ul>
    <li>Review booking requests</li>
    <li>Approve or reject bookings</li>
    <li>Manage hall schedule</li>
</ul>

<h3>Booking Summary</h3>

<p>Pending Bookings : <?php echo $pending; ?></p>

<p>Approved Bookings : <?php echo $approved; ?></p>

<p>Rejected Bookings : <?php echo $rejected; ?></p>

<a href="manage_bookings.php" class="book-btn">
    Manage Bookings
</a>

    </div>

</div>

</body>
</html>