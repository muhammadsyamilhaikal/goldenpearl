<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include "config.php";

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: login.php");
    exit();
}

$booking_id = (int)$_GET['id'];
$user_id    = $_SESSION['user_id'];
$role       = $_SESSION['role'];

if ($role === 'customer') {
    $sql = "SELECT b.*, u.name as customer_name FROM bookings b JOIN users u ON b.user_id = u.user_id WHERE b.booking_id = ? AND b.user_id = ? AND (b.payment_status = 'Paid' OR b.status = 'Paid')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $booking_id, $user_id);
} else {
    $sql = "SELECT b.*, u.name as customer_name FROM bookings b JOIN users u ON b.user_id = u.user_id WHERE b.booking_id = ? AND (b.payment_status = 'Paid' OR b.status = 'Paid')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $booking_id);
}

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>alert('Receipt not found or payment not completed!'); window.history.back();</script>";
    exit();
}
$booking = $result->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt #<?php echo $booking_id; ?> - Golden Pearl</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="dashboard-container">
    <?php if ($role === 'admin') include "sidebar_admin.php"; elseif ($role === 'staff') include "sidebar_staff.php"; else include "sidebar_customer.php"; ?>
    <div class="main-content">
        <div class="menu-box" style="width: 100%; max-width: 650px; margin: 0 auto; border: 2px solid #5e0023;">
            <div style="text-align: center; border-bottom: 2px dashed #ccc; padding-bottom: 15px; margin-bottom: 15px;">
                <h2 style="margin: 0; color: #5e0023;">GOLDEN PEARL HALL BOOKING</h2>
                <p style="box-shadow: none; padding: 0; margin: 5px 0 0 0; font-size: 14px;">Official Payment Receipt</p>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 15px;">
                <div>
                    <p style="box-shadow: none; padding: 0; margin: 0;"><strong>Receipt No:</strong> #GP-<?php echo $booking_id; ?></p>
                    <p style="box-shadow: none; padding: 0; margin: 0;"><strong>Print Date:</strong> <?php echo date('d/m/Y'); ?></p>
                </div>
                <div><p style="box-shadow: none; padding: 0; margin: 0;"><strong>Status:</strong> <span style="color: #27ae60; font-weight: bold;">PAID</span></p></div>
            </div>
            <table style="width: 100%; box-shadow: none; border: 1px solid #eee; margin-top: 10px;">
                <thead>
                    <tr style="background: #f8f9fa; color: #333;">
                        <th style="background: #f8f9fa; color: #333; text-align: left;">Description</th>
                        <th style="background: #f8f9fa; color: #333; text-align: right;">Amount (RM)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="text-align: left; padding: 15px;">
                            <strong><?php echo htmlspecialchars($booking['event_details']); ?></strong><br>
                            Date: <?php echo date('d/m/Y', strtotime($booking['event_date'])); ?><br>
                            Time: <?php echo htmlspecialchars($booking['event_time']); ?><br>
                            Customer: <?php echo htmlspecialchars($booking['customer_name']); ?>
                        </td>
                        <td style="text-align: right; padding: 15px; vertical-align: top;"><?php echo number_format($booking['total_price'], 2); ?></td>
                    </tr>
                    <tr style="background: #fdfefe; font-size: 18px; font-weight: bold;">
                        <td style="text-align: right; padding: 15px; border-top: 2px solid #333;">TOTAL PAID:</td>
                        <td style="text-align: right; padding: 15px; border-top: 2px solid #333; color: #27ae60;">RM <?php echo number_format($booking['total_price'], 2); ?></td>
                    </tr>
                </tbody>
            </table>
            <div style="text-align: center; margin-top: 30px; font-size: 13px; color: #777;">
                <p style="box-shadow: none; padding: 0; margin: 0;">Thank you for choosing Golden Pearl!</p>
                <p style="box-shadow: none; padding: 0; margin: 0;">This is a computer-generated receipt, no signature required.</p>
            </div>
            <div style="margin-top: 25px; text-align: center;">
                <button onclick="window.print()" class="book-btn" style="background: #3498db; width: auto; display: inline-block;">Print Receipt</button>
                <a href="<?php echo ($role === 'customer') ? 'my_bookings.php' : 'manage_bookings.php'; ?>" class="book-btn" style="background: #475569; width: auto; display: inline-block; margin-left: 10px;">Back</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>