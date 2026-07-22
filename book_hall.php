<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}
include "config.php";

$error_msg = "";
$selected_pkg = isset($_GET['package']) ? trim($_GET['package']) : "";
$selected_date = isset($_GET['date']) ? trim($_GET['date']) : date('Y-m-d');

if (isset($_POST['submit_booking'])) {
    $user_id       = $_SESSION['user_id'];
    $package_name  = trim($_POST['package_name']);
    $event_date    = trim($_POST['event_date']);
    $event_time    = trim($_POST['event_time']);
    $notes         = trim($_POST['notes']);
    $status        = 'Pending';
    $payment_status = 'Unpaid';

    if (empty($package_name) || empty($event_date) || empty($event_time)) {
        $error_msg = "Please complete all required fields!";
    } elseif (strtotime($event_date) < strtotime(date('Y-m-d'))) {
        $error_msg = "You cannot book past dates!";
    } else {
        $price = isset($HALL_PACKAGES[$package_name]) ? $HALL_PACKAGES[$package_name]['price'] : 0.00;
        $event_details = "Package: " . $package_name . " | Details: " . ($notes ? $notes : "None");

        $insert_sql = "INSERT INTO bookings (user_id, event_date, event_time, event_details, status, total_price, payment_status) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("issssds", $user_id, $event_date, $event_time, $event_details, $status, $price, $payment_status);

        if ($insert_stmt->execute()) {
            echo "<script>alert('Booking submitted successfully!'); window.location='my_bookings.php';</script>";
            exit();
        } else {
            $error_msg = "System error. Booking failed.";
        }
        $insert_stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a Hall - Golden Pearl</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="dashboard-container">
    <?php include "sidebar_customer.php"; ?>
    <div class="main-content">
        <h2>Hall Booking Form</h2>
        <form action="book_hall.php" method="POST" style="max-width: 600px;">
            <?php if (!empty($error_msg)) { ?>
                <p style="background: #f8d7da; color: #721c24; border-left: 4px solid #f5c6cb;"><?php echo htmlspecialchars($error_msg); ?></p>
            <?php } ?>

            <label for="package_name">Select Hall Package:</label>
            <select id="package_name" name="package_name" required>
                <option value="">-- Please Select Package --</option>
                <?php foreach ($HALL_PACKAGES as $pkg => $details) { ?>
                    <option value="<?php echo htmlspecialchars($pkg); ?>" <?php if ($selected_pkg === $pkg) echo "selected"; ?>>
                        <?php echo htmlspecialchars($pkg); ?> (RM<?php echo number_format($details['price'], 2); ?>)
                    </option>
                <?php } ?>
            </select>

            <label for="event_date">Event Date:</label>
            <input type="date" id="event_date" name="event_date" value="<?php echo htmlspecialchars($selected_date); ?>" required min="<?php echo date('Y-m-d'); ?>">

            <label for="event_time">Event Time / Duration (e.g., 10:00 - 14:00):</label>
            <input type="text" id="event_time" name="event_time" required placeholder="10:00 - 14:00">

            <label for="notes">Additional Notes / Details:</label>
            <textarea id="notes" name="notes" placeholder="PA system requirements, table arrangement, etc..."></textarea>

            <input type="submit" name="submit_booking" value="Submit Booking" class="book-btn">
            <a href="customer.php" style="display: block; text-align: center; margin-top: 15px;">Back</a>
        </form>
    </div>
</div>
</body>
</html>