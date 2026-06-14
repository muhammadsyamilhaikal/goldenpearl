<?php
session_start();
include "config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$booking_id = $_GET['id'];

$sql = "SELECT * FROM bookings WHERE booking_id='$booking_id' AND user_id='{$_SESSION['user_id']}'";
$result = $conn->query($sql);
$booking = $result->fetch_assoc();

if (isset($_POST['submit_payment'])) {
    $target_dir = "uploads/";
    
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $file_name = time() . "_" . basename($_FILES["receipt"]["name"]);
    $target_file = $target_dir . $file_name;
    
    if (move_uploaded_file($_FILES["receipt"]["tmp_name"], $target_file)) {
        $update_sql = "UPDATE bookings SET payment_status='Pending Verification', payment_proof='$file_name' WHERE booking_id='$booking_id'";
        if ($conn->query($update_sql)) {
            echo "<script>alert('Payment proof uploaded successfully! Please wait for staff verification.'); window.location='my_bookings.php';</script>";
        } else {
            echo "<script>alert('Database Error.');</script>";
        }
    } else {
        echo "<script>alert('Failed to upload file.');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Make Payment</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="dashboard-container">
    <?php include "sidebar_customer.php"; ?>
    <div class="main-content">
        <h2>Make Payment</h2>
        
        <p><strong>Booking ID:</strong> <?php echo $booking['booking_id']; ?></p>
        <p><strong>Total Amount to Pay:</strong> RM <?php echo $booking['total_price']; ?></p>
        <p><strong>Bank Details:</strong> Maybank (Golden Pearl Sdn Bhd) - 5142 3322 1100</p>
        <br>

        <form method="POST" enctype="multipart/form-data">
            <label>Upload Payment Proof (JPG, PNG, PDF):</label><br>
            <input type="file" name="receipt" required><br><br>
            <button type="submit" name="submit_payment" style="background:#27ae60;">Submit Payment</button>
        </form>
    </div>
</div>
</body>
</html>