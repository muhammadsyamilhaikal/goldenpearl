<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}
include "config.php";

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM bookings WHERE user_id = ? ORDER BY event_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - Golden Pearl</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="dashboard-container">
    <?php include "sidebar_customer.php"; ?>
    <div class="main-content">
        <h2>My Booking Records</h2>
        <div style="overflow-x: auto; margin-top: 20px;">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Event Details</th>
                        <th>Date & Time</th>
                        <th>Total Price</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0) { ?>
                        <?php while ($row = $result->fetch_assoc()) { ?>
                            <tr>
                                <td>#<?php echo (int)$row['booking_id']; ?></td>
                                <td><?php echo htmlspecialchars($row['event_details']); ?></td>
                                <td>
                                    <?php echo date('d/m/Y', strtotime($row['event_date'])); ?><br>
                                    <small><?php echo htmlspecialchars($row['event_time']); ?></small>
                                </td>
                                <td>RM<?php echo number_format($row['total_price'], 2); ?></td>
                                <td>
                                    <?php $color = ($row['status'] == 'Approved' || $row['status'] == 'Paid') ? '#27ae60' : (($row['status'] == 'Rejected' || $row['status'] == 'Cancelled') ? '#e74c3c' : '#f39c12'); ?>
                                    <span style="color: white; background: <?php echo $color; ?>; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold;">
                                        <?php echo htmlspecialchars($row['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="booking_details.php?id=<?php echo (int)$row['booking_id']; ?>" style="background: #3498db; color: white; padding: 6px 10px; border-radius: 4px; text-decoration: none; font-size: 12px;">Details</a>
                                    <?php if ($row['status'] == 'Approved' && $row['payment_status'] == 'Unpaid') { ?>
                                        <a href="payment.php?id=<?php echo (int)$row['booking_id']; ?>" style="background: #27ae60; color: white; padding: 6px 10px; border-radius: 4px; text-decoration: none; font-size: 12px; margin-left: 4px;">Pay</a>
                                    <?php } elseif ($row['payment_status'] == 'Paid' || $row['status'] == 'Paid') { ?>
                                        <a href="receipt.php?id=<?php echo (int)$row['booking_id']; ?>" style="background: #8e44ad; color: white; padding: 6px 10px; border-radius: 4px; text-decoration: none; font-size: 12px; margin-left: 4px;">Receipt</a>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr><td colspan="6" style="text-align: center; padding: 20px;">No booking records found.</td></tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>