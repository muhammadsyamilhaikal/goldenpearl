<?php
session_start();
include "config.php";

$id = $_GET['id'];

$sql = "SELECT * FROM bookings
        WHERE booking_id='$id'";

$result = $conn->query($sql);

$booking = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Booking Details</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="dashboard-container">

    <?php include "sidebar_customer.php"; ?>

    <div class="main-content">

        <h2>Booking Details</h2>

<table border="1" cellpadding="10">

<tr>
    <th>Booking ID</th>
    <td><?php echo $booking['booking_id']; ?></td>
</tr>

<tr>
    <th>Event Date</th>
    <td><?php echo $booking['event_date']; ?></td>
</tr>

<tr>
    <th>Event Time</th>
    <td><?php echo $booking['event_time']; ?></td>
</tr>

<tr>
    <th>Event Details</th>
    <td><?php echo $booking['event_details']; ?></td>
</tr>

<tr>
    <th>Status</th>
    <td><?php echo $booking['status']; ?></td>
</tr>

</table>

<br>

<button onclick="window.print()">
    Print Booking Details
</button>
    </div>

</div>

</body>
</html>