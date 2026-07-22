<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'staff')) {
    echo "<script>alert('ACCESS DENIED!'); window.location='login.php';</script>";
    exit();
}
include "config.php";

if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $booking_id = (int)$_GET['id'];
    $new_status = "";

    if ($action === 'approve') $new_status = 'Approved';
    elseif ($action === 'reject') $new_status = 'Rejected';

    if (!empty($new_status)) {
        $update_stmt = $conn->prepare("UPDATE bookings SET status = ? WHERE booking_id = ?");
        $update_stmt->bind_param("si", $new_status, $booking_id);
        if ($update_stmt->execute()) {
            echo "<script>alert('Booking status updated to: " . $new_status . "'); window.location='manage_bookings.php';</script>";
            exit();
        }
        $update_stmt->close();
    }
}

$sql = "SELECT b.*, u.name as customer_name FROM bookings b JOIN users u ON b.user_id = u.user_id ORDER BY b.event_date DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings - Golden Pearl</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="dashboard-container">
    <?php if ($_SESSION['role'] === 'admin') include "sidebar_admin.php"; else include "sidebar_staff.php"; ?>
    <div class="main-content">
        <h2>Hall Booking Management</h2>
        <div style="overflow-x: auto; margin-top: 20px;">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Event Details</th>
                        <th>Date & Time</th>
                        <th>Price / Pay Status</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0) { ?>
                        <?php while ($row = $result->fetch_assoc()) { ?>
                            <tr>
                                <td>#<?php echo (int)$row['booking_id']; ?></td>
                                <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['event_details']); ?></td>
                                <td>
                                    <?php echo date('d/m/Y', strtotime($row['event_date'])); ?><br>
                                    <small><?php echo htmlspecialchars($row['event_time']); ?></small>
                                </td>
                                <td>RM<?php echo number_format($row['total_price'], 2); ?><br><small>(<?php echo htmlspecialchars($row['payment_status']); ?>)</small></td>
                                <td>
                                    <?php $color = ($row['status'] == 'Approved' || $row['status'] == 'Paid') ? '#27ae60' : (($row['status'] == 'Rejected' || $row['status'] == 'Cancelled') ? '#e74c3c' : '#f39c12'); ?>
                                    <span style="color: white; background: <?php echo $color; ?>; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold;">
                                        <?php echo htmlspecialchars($row['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="booking_details.php?id=<?php echo (int)$row['booking_id']; ?>" style="background: #3498db; color: white; padding: 6px 10px; border-radius: 4px; text-decoration: none; font-size: 12px; display: inline-block; margin-bottom: 4px;">Details</a>
                                    <?php if ($row['status'] == 'Pending') { ?>
                                        <a href="manage_bookings.php?action=approve&id=<?php echo (int)$row['booking_id']; ?>" onclick="return confirm('Approve this booking?');" style="background: #27ae60; color: white; padding: 6px 10px; border-radius: 4px; text-decoration: none; font-size: 12px; display: inline-block; margin-bottom: 4px;">Approve</a>
                                        <a href="manage_bookings.php?action=reject&id=<?php echo (int)$row['booking_id']; ?>" onclick="return confirm('Reject this booking?');" class="delete-btn" style="padding: 6px 10px; font-size: 12px; display: inline-block;">Reject</a>
                                    <?php } elseif ($row['payment_status'] == 'Paid' || $row['status'] == 'Paid') { ?>
                                        <a href="receipt.php?id=<?php echo (int)$row['booking_id']; ?>" style="background: #8e44ad; color: white; padding: 6px 10px; border-radius: 4px; text-decoration: none; font-size: 12px; display: inline-block;">Receipt</a>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } } else { ?>
                        <tr><td colspan="7" style="text-align: center; padding: 20px;">No booking records found.</td></tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>