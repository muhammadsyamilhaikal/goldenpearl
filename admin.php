<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('ACCESS DENIED! You are not an Administrator.'); window.location='login.php';</script>";
    exit();
}
include "config.php";

$total_bookings = $conn->query("SELECT COUNT(*) as total FROM bookings")->fetch_assoc()['total'];
$pending_bookings = $conn->query("SELECT COUNT(*) as total FROM bookings WHERE status = 'Pending'")->fetch_assoc()['total'];
$total_users = $conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'customer'")->fetch_assoc()['total'];
$total_staff = $conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'staff'")->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Golden Pearl</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="dashboard-container">
    <?php include "sidebar_admin.php"; ?>
    <div class="main-content">
        <h2>Welcome, Administrator <?php echo htmlspecialchars($_SESSION['name']); ?>!</h2>
        <p>Main control center for the Golden Pearl Hall Booking System.</p>

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

            <div class="menu-box" style="flex: 1; min-width: 200px; border-top: 4px solid #27ae60;">
                <h3>Registered Customers</h3>
                <p style="font-size: 28px; font-weight: bold; margin: 0; box-shadow: none; padding: 0;"><?php echo (int)$total_users; ?></p>
            </div>

            <div class="menu-box" style="flex: 1; min-width: 200px; border-top: 4px solid #5e0023;">
                <h3>Total Staff</h3>
                <p style="font-size: 28px; font-weight: bold; margin: 0; box-shadow: none; padding: 0;"><?php echo (int)$total_staff; ?></p>
                <a href="manage_staff.php" style="font-size: 13px; margin: 10px 0 0 0;">Manage Staff &rarr;</a>
            </div>
        </div>

        <h3 style="margin-top: 40px;">Recent Bookings</h3>
        <div style="overflow-x: auto; margin-top: 15px;">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Event Details</th>
                        <th>Event Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $recent_sql = "SELECT b.booking_id, b.event_date, b.event_details, b.status, u.name as customer_name 
                                   FROM bookings b JOIN users u ON b.user_id = u.user_id 
                                   ORDER BY b.booking_id DESC LIMIT 5";
                    $recent_res = $conn->query($recent_sql);
                    if ($recent_res->num_rows > 0) {
                        while ($row = $recent_res->fetch_assoc()) { 
                    ?>
                        <tr>
                            <td>#<?php echo (int)$row['booking_id']; ?></td>
                            <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['event_details']); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($row['event_date'])); ?></td>
                            <td>
                                <?php $color = ($row['status'] == 'Approved' || $row['status'] == 'Paid') ? '#27ae60' : (($row['status'] == 'Rejected' || $row['status'] == 'Cancelled') ? '#e74c3c' : '#f39c12'); ?>
                                <span style="color: white; background: <?php echo $color; ?>; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold;">
                                    <?php echo htmlspecialchars($row['status']); ?>
                                </span>
                            </td>
                            <td><a href="booking_details.php?id=<?php echo (int)$row['booking_id']; ?>" style="background: #3498db; color: white; padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 13px;">View / Verify</a></td>
                        </tr>
                    <?php } } else { ?>
                        <tr><td colspan="6" style="text-align: center; padding: 20px;">No recent records.</td></tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>