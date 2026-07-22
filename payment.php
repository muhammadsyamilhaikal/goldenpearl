<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}
include "config.php";

$user_id = $_SESSION['user_id'];
$error_msg = "";

if (isset($_POST['pay_now'])) {
    $booking_id = (int)$_POST['booking_id'];
    $payment_method = $_POST['payment_method'];
    
    // Check if booking is valid for payment
    $check_stmt = $conn->prepare("SELECT booking_id FROM bookings WHERE booking_id = ? AND user_id = ? AND status = 'Approved'");
    $check_stmt->bind_param("ii", $booking_id, $user_id);
    $check_stmt->execute();
    if ($check_stmt->get_result()->num_rows === 0) {
        echo "<script>alert('Invalid booking for payment!'); window.location='my_bookings.php';</script>";
        exit();
    }
    $check_stmt->close();

    // Handle file upload for payment proof
    $proof_name = NULL;
    if (isset($_FILES['payment_proof']) && $_FILES['payment_proof']['error'] === 0) {
        $ext = pathinfo($_FILES['payment_proof']['name'], PATHINFO_EXTENSION);
        $proof_name = time() . "_" . $user_id . "." . $ext;
        move_uploaded_file($_FILES['payment_proof']['tmp_name'], "uploads/" . $proof_name);
    } else {
        $proof_name = "Paid_via_" . $payment_method;
    }

    $update_stmt = $conn->prepare("UPDATE bookings SET status = 'Paid', payment_status = 'Paid', payment_proof = ? WHERE booking_id = ?");
    $update_stmt->bind_param("si", $proof_name, $booking_id);
    
    if ($update_stmt->execute()) {
        echo "<script>alert('Payment successful! Thank you.'); window.location='receipt.php?id=" . $booking_id . "';</script>";
        exit();
    } else {
        $error_msg = "System error. Payment failed.";
    }
    $update_stmt->close();
}

if (!isset($_GET['id']) && !isset($_POST['pay_now'])) {
    header("Location: my_bookings.php");
    exit();
}

$booking_id = (int)$_GET['id'];
$sql = "SELECT * FROM bookings WHERE booking_id = ? AND user_id = ? AND status = 'Approved'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $booking_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>alert('Booking not found or not yet approved!'); window.location='my_bookings.php';</script>";
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
    <title>Payment - Golden Pearl</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="dashboard-container">
    <?php include "sidebar_customer.php"; ?>
    <div class="main-content">
        <h2>Payment for Booking #<?php echo $booking_id; ?></h2>
        <div class="menu-box" style="width: 100%; max-width: 500px; margin-top: 20px;">
            <h3>Payment Summary</h3>
            <p><strong>Details:</strong> <?php echo htmlspecialchars($booking['event_details']); ?></p>
            <p><strong>Date:</strong> <?php echo date('d/m/Y', strtotime($booking['event_date'])); ?></p>
            <p><strong>Time:</strong> <?php echo htmlspecialchars($booking['event_time']); ?></p>
            <p style="font-size: 20px; color: #27ae60; font-weight: bold; background: #e8f8f5; border-left: 4px solid #2ecc71;">Total: RM<?php echo number_format($booking['total_price'], 2); ?></p>

            <form action="payment.php" method="POST" enctype="multipart/form-data" style="box-shadow: none; padding: 0; margin-top: 20px; max-width: 100%;">
                <?php if (!empty($error_msg)) { ?><p style="background: #f8d7da; color: #721c24; border-left: 4px solid #f5c6cb;"><?php echo htmlspecialchars($error_msg); ?></p><?php } ?>
                <input type="hidden" name="booking_id" value="<?php echo $booking_id; ?>">
                
                <label for="payment_method">Payment Method:</label>
                <select id="payment_method" name="payment_method" required>
                    <option value="">-- Select Method --</option>
                    <option value="fpx">FPX (Online Banking)</option>
                    <option value="card">Credit / Debit Card</option>
                    <option value="qr">DuitNow QR</option>
                </select>

                <label for="payment_proof">Upload Payment Receipt (Optional):</label>
                <input type="file" id="payment_proof" name="payment_proof" accept="image/*,.pdf" style="margin-bottom: 20px;">

                <input type="submit" name="pay_now" value="Pay Now (RM<?php echo number_format($booking['total_price'], 2); ?>)" class="book-btn" style="background: #27ae60;">
                <a href="my_bookings.php" style="display: block; text-align: center; margin-top: 15px;">Cancel</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>