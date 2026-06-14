<?php
session_start();
include "config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_POST['cancel_booking'])) {
    $booking_id = $_POST['booking_id'];

    $update_sql = "UPDATE bookings SET status='Cancelled' WHERE booking_id='$booking_id' AND user_id='$user_id'";
    
    if ($conn->query($update_sql)) {
        echo "<script>alert('Booking successfully cancelled!');</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
}

$sql = "SELECT * FROM bookings
        WHERE user_id='$user_id'
        ORDER BY booking_id DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Bookings</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .action-container {
            display: flex;
            gap: 10px;
            justify-content: center;
            align-items: center;
        }
        .cancel-form {
            background: none;
            padding: 0;
            box-shadow: none;
            margin: 0;
            width: auto;
        }
        .cancel-btn {
            background-color: #e74c3c;
            color: white;
            padding: 6px 12px;
            font-size: 14px;
            width: auto;
            border-radius: 4px;
        }
        .cancel-btn:hover {
            background-color: #c0392b;
        }
    </style>
</head>
<body>

<div class="dashboard-container">

    <?php include "sidebar_customer.php"; ?>

    <div class="main-content">

        <h2>My Bookings</h2>

        <table border="1" cellpadding="10">

<tr>
    <th>ID</th>
    <th>Date</th>
    <th>Time</th>
    <th>Booking Status</th>
    <th>Payment Status</th>
    <th>Action</th>
</tr>

<?php 
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) { 
?>

<tr>
    <td><?php echo $row['booking_id']; ?></td>
    <td><?php echo $row['event_date']; ?></td>
    <td><?php echo $row['event_time']; ?></td>
    <td><strong><?php echo $row['status']; ?></strong></td>
    <td><strong><?php echo $row['payment_status']; ?></strong></td>
    
    <td>
        <div class="action-container">
            <a href="booking_details.php?id=<?php echo $row['booking_id']; ?>">View</a>

            <?php if($row['status'] == 'Pending' || $row['status'] == 'Approved') { ?>
                <form method="POST" class="cancel-form" onsubmit="return confirm('Are you sure you want to cancel this booking?');">
                    <input type="hidden" name="booking_id" value="<?php echo $row['booking_id']; ?>">
                    <button type="submit" name="cancel_booking" class="cancel-btn">Cancel</button>
                </form>
            <?php } ?>

            <?php if($row['status'] == 'Approved' && $row['payment_status'] == 'Unpaid') { ?>
                <a href="payment.php?id=<?php echo $row['booking_id']; ?>" style="background: #27ae60; color: white; padding: 6px 12px; border-radius: 4px;">Pay Now</a>
            <?php } elseif ($row['payment_status'] == 'Paid') { ?>
                <a href="receipt.php?id=<?php echo $row['booking_id']; ?>" style="background: #2980b9; color: white; padding: 6px 12px; border-radius: 4px;" target="_blank">Receipt</a>
            <?php } ?>
        </div>
    </td>
</tr>

<?php 
    } 
} else {
    echo "<tr><td colspan='6'>You have no bookings yet.</td></tr>";
}
?>

</table>

    </div>

</div>

</body>
</html>