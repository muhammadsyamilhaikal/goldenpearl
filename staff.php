<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    header("Location: login.php");
    exit();
}
include "config.php";

$total_bookings = $conn->query("SELECT COUNT(*) as total FROM bookings")->fetch_assoc()['total'];
$pending_bookings = $conn->query("SELECT COUNT(*) as total FROM bookings WHERE status = 'Pending'")->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard - Golden Pearl</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="dashboard-container">
    <?php include "sidebar_staff.php"; ?>
    <div class="main-content">
        <h2>Welcome, Staff <?php echo htmlspecialchars($_SESSION['name']); ?>!</h2>
        <p>Your task is to monitor and manage customer hall bookings.</p>

        <div style="display: flex; gap: 20px; flex-wrap: wrap; margin-top: 25px;">
            <div class="menu-box" style="flex: 1; min-width: 200px; border-top: 4px solid #3498db;">
                <h3>Total Bookings</h3>
                <p style="font-size: 28px; font-weight: bold; margin: 0; box-shadow: none; padding: 0;"><?php echo (int)$total_bookings; ?></p>
                <a href="manage_bookings.php" style="font-size: 13px; margin: 10px 0 0 0;">Manage Bookings &rarr;</a>
            </div>

            <div class="menu-box" style="flex: 1; min-width: 200px; border-top: 4px solid #f39c12;">
                <h3>Pending Approval</h3>
                <p style="font-size: 28px; font-weight: bold; margin: 0; box-shadow: none; padding: 0; color: #d68910;"><?php echo (int)$pending_bookings; ?></p>
                <a href="manage_bookings.php?status=Pending" style="font-size: 13px; margin: 10px 0 0 0;">Review Now &rarr;</a>
            </div>
        </div>

        <div style="margin-top: 30px;">
            <a href="manage_bookings.php" class="book-btn">Open Bookings Management Page</a>
        </div>
    </div>
</div>
</body>
</html>