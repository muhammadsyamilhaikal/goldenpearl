<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include "config.php";

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>alert('Invalid ID!'); window.history.back();</script>";
    exit();
}

$booking_id = (int)$_GET['id'];
$user_id    = $_SESSION['user_id'];
$role       = $_SESSION['role'];

if ($role === 'customer') {
    $sql = "SELECT b.*, u.name as customer_name, u.email FROM bookings b JOIN users u ON b.user_id = u.user_id WHERE b.booking_id = ? AND b.user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $booking_id, $user_id);
} else {
    $sql = "SELECT b.*, u.name as customer_name, u.email FROM bookings b JOIN users u ON b.user_id = u.user_id WHERE b.booking_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $booking_id);
}

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>alert('Record not found or access denied!'); window.history.back();</script>";
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
    <title>Booking Details #<?php echo $booking_id; ?> - Golden Pearl</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="dashboard-container">
    <?php if ($role === 'admin') include "sidebar_admin.php"; elseif ($role === 'staff') include "sidebar_staff.php"; else include "sidebar_customer.php"; ?>
    <div class="main-content">
        <h2>Booking Details #<?php echo $booking_id; ?></h2>
        <div class="menu-box" style="width: 100%; max-width: 600px; margin-top: 20px;">
            <h3>Event Information</h3>
            <p><strong>Details:</strong> <?php echo htmlspecialchars($booking['event_details']); ?></p>
            <p><strong>Status:</strong> <span style="font-weight: bold; color: <?php echo ($booking['status'] == 'Approved' || $booking['status'] == 'Paid') ? '#27ae60' : (($booking['status'] == 'Rejected' || $booking['status'] == 'Cancelled') ? '#e74c3c' : '#f39c12'); ?>"><?php echo htmlspecialchars($booking['status']); ?></span></p>

            <h3 style="margin-top: 15px;">Date & Time</h3>
            <p><strong>Event Date:</strong> <?php echo date('d/m/Y', strtotime($booking['event_date'])); ?></p>
            <p><strong>Time:</strong> <?php echo htmlspecialchars($booking['event_time']); ?></p>
            <p><strong>Payment Status:</strong> <?php echo htmlspecialchars($booking['payment_status']); ?></p>
            <p style="font-size: 18px; background: #f8f9fa; border-left: 4px solid #5e0023;"><strong>Total Price: RM<?php echo number_format($booking['total_price'], 2); ?></strong></p>

            <h3 style="margin-top: 15px;">Customer</h3>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($booking['customer_name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($booking['email']); ?></p>
            
            <?php if (!empty($booking['payment_proof'])) { ?>
                <h3 style="margin-top: 15px;">Payment Proof</h3>
                <p><strong>File uploaded:</strong> <?php echo htmlspecialchars($booking['payment_proof']); ?></p>
            <?php } ?>

            <div style="margin-top: 20px; text-align: center;">
                <a href="<?php echo ($role === 'customer') ? 'my_bookings.php' : 'manage_bookings.php'; ?>" class="book-btn" style="background:#475569;">Back</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>