<?php
session_start();
include "config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$booking_id = $_GET['id'];
$sql = "SELECT bookings.*, users.name, users.email FROM bookings JOIN users ON bookings.user_id = users.user_id WHERE booking_id='$booking_id'";
$result = $conn->query($sql);
$booking = $result->fetch_assoc();

if($booking['payment_status'] != 'Paid') {
    die("Receipt not available. Payment not cleared yet.");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Official Receipt - #<?php echo $booking['booking_id']; ?></title>
    <style>
        body { font-family: Arial, sans-serif; padding: 40px; }
        .receipt-box { border: 2px solid #000; padding: 30px; max-width: 600px; margin: auto; }
        h1 { text-align: center; margin-bottom: 5px; }
        .text-center { text-align: center; }
        .details { margin-top: 30px; }
        .details th, .details td { padding: 10px; text-align: left; }
        .paid-stamp { color: green; font-size: 30px; font-weight: bold; text-align: center; margin-top: 20px; border: 3px solid green; padding: 10px; display: inline-block; transform: rotate(-10deg); }
        .print-btn { display: block; margin: 30px auto; padding: 10px 20px; font-size: 16px; cursor: pointer; }
        @media print { .print-btn { display: none; } }
    </style>
</head>
<body>

<div class="receipt-box">
    <h1>GOLDEN PEARL HALL</h1>
    <p class="text-center">Official Payment Receipt</p>
    <hr>
    
    <table class="details" width="100%">
        <tr>
            <th>Receipt No:</th> <td>#<?php echo $booking['booking_id']; ?></td>
        </tr>
        <tr>
            <th>Customer Name:</th> <td><?php echo $booking['name']; ?></td>
        </tr>
        <tr>
            <th>Event Date:</th> <td><?php echo $booking['event_date']; ?> (<?php echo $booking['event_time']; ?>)</td>
        </tr>
        <tr>
            <th>Event Details:</th> <td><?php echo $booking['event_details']; ?></td>
        </tr>
        <tr>
            <th>Total Paid:</th> <td><strong>RM <?php echo $booking['total_price']; ?></strong></td>
        </tr>
    </table>

    <div class="text-center">
        <div class="paid-stamp">PAID IN FULL</div>
    </div>
</div>

<button class="print-btn" onclick="window.print()">Print Receipt</button>

</body>
</html>