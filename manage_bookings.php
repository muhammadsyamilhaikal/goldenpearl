<?php
session_start();
include "config.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'staff') {
    header("Location: login.php");
    exit();
}

if (isset($_POST['update_status'])) {
    $booking_id = $_POST['booking_id'];
    $new_status = $_POST['status'];

    $update_sql = "UPDATE bookings SET status='$new_status' WHERE booking_id='$booking_id'";
    
    if ($conn->query($update_sql)) {
        echo "<script>alert('Booking status updated successfully!');</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
}

// LOGIC UNTUK VERIFY PAYMENT
if (isset($_POST['verify_payment'])) {
    $booking_id = $_POST['booking_id'];
    $update_sql = "UPDATE bookings SET payment_status='Paid' WHERE booking_id='$booking_id'";
    $conn->query($update_sql);
    echo "<script>alert('Payment verified!'); window.location='manage_bookings.php';</script>";
}

if (isset($_POST['delete_booking'])) {
    $booking_id = $_POST['booking_id'];
    $delete_sql = "DELETE FROM bookings WHERE booking_id='$booking_id'";
    if ($conn->query($delete_sql)) {
        echo "<script>alert('Booking deleted successfully!'); window.location='manage_bookings.php';</script>";
        exit();
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
}

$sql = "SELECT bookings.*, users.name AS customer_name 
        FROM bookings 
        JOIN users ON bookings.user_id = users.user_id 
        ORDER BY bookings.event_date ASC, bookings.event_time ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Bookings</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .action-form {
            background: none;
            padding: 0;
            box-shadow: none;
            margin: 0;
            max-width: 100%;
        }
        .action-select {
            width: auto;
            margin-bottom: 5px;
            padding: 5px;
        }
        .action-btn {
            width: auto;
            padding: 6px 12px;
            font-size: 14px;
        }
    </style>
</head>
<body>

<div class="dashboard-container">

    <?php include "sidebar_staff.php"; ?>

    <div class="main-content">

        <h2>Manage Bookings (Staff)</h2>

<table border="1" cellpadding="10">

<tr>
    <th>ID</th>
    <th>Customer</th>
    <th>Date/Time</th>
    <th>Price</th>
    <th>Booking Status</th>
    <th>Payment Status</th>
    <th>Action</th>
</tr>

<?php while($row = $result->fetch_assoc()) { ?>
<tr>
    <td><?php echo $row['booking_id']; ?></td>
    <td><?php echo $row['customer_name']; ?></td>
    <td><?php echo $row['event_date']; ?> <br> <?php echo $row['event_time']; ?></td>
    <td>RM <?php echo $row['total_price']; ?></td>
    
    <td>
        <form method="POST" class="action-form">
            <input type="hidden" name="booking_id" value="<?php echo $row['booking_id']; ?>">
            <select name="status" class="action-select">
                <option value="Pending" <?php if($row['status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                <option value="Approved" <?php if($row['status'] == 'Approved') echo 'selected'; ?>>Approved</option>
                <option value="Rejected" <?php if($row['status'] == 'Rejected') echo 'selected'; ?>>Rejected</option>
            </select><br>
            <button type="submit" name="update_status" class="action-btn">Update</button>
        </form>
    </td>

    <td>
        <strong><?php echo $row['payment_status']; ?></strong><br>
        <?php if($row['payment_proof'] != NULL) { ?>
            <a href="uploads/<?php echo $row['payment_proof']; ?>" target="_blank" style="font-size:12px;">View Receipt</a>
        <?php } ?>
    </td>
    
    <td>
        <form method="POST" class="action-form" onsubmit="return confirm('Delete this booking?');" style="margin-bottom: 5px;">
            <input type="hidden" name="booking_id" value="<?php echo $row['booking_id']; ?>">
            <button type="submit" name="delete_booking" class="action-btn" style="background: #e74c3c;">Delete</button>
        </form>

        <?php if($row['payment_status'] == 'Pending Verification') { ?>
            <form method="POST" class="action-form" style="margin-top:5px;">
                <input type="hidden" name="booking_id" value="<?php echo $row['booking_id']; ?>">
                <button type="submit" name="verify_payment" class="action-btn" style="background:#27ae60; color:white;">Verify Payment</button>
            </form>
        <?php } ?>
    </td>
</tr>
<?php } ?>
</table>

    </div>
</div>
</body>
</html>