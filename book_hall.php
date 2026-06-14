<?php
session_start();
include "config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$message = ""; 

    if (isset($_POST['submit_booking'])) {

        $user_id = $_SESSION['user_id'];
        $event_date = $_POST['event_date'];
        $requested_start = $_POST['event_time'];

        $package = $_POST['package'];
        $raw_details = $_POST['event_details'];
        $event_details = "Pakej: " . $package . " | Detail: " . $raw_details;

        $duration_hours = 4;
        $requested_end = date('H:i', strtotime($requested_start . " +$duration_hours hours"));

        $check_sql = "SELECT event_time FROM bookings
                      WHERE event_date='$event_date'
                      AND status NOT IN ('Rejected', 'Cancelled')";

        $result = $conn->query($check_sql);
        $is_clash = false;

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $existing_start = $row['event_time']; 
                $existing_end = date('H:i', strtotime($existing_start . " +$duration_hours hours")); 

                if ($requested_start < $existing_end && $requested_end > $existing_start) {
                    $is_clash = true;
                    break;
                }
            }
        }

        if ($is_clash) {
            $message = "<p style='color: red; font-weight: bold;'>Maaf, masa bertindih! Slot pada tarikh ni (jarak 4 jam dari tempahan sedia ada) telah ditempah.</p>";
        } else {
            
            $status = 'Pending';
            $payment_status = 'Unpaid';
            $total_price = 0;
            
            if ($package == "Dewan Standard (100 Pax)") {
                $total_price = 1000.00;
            } elseif ($package == "Dewan VIP (300 Pax)") {
                $total_price = 2500.00;
            } elseif ($package == "Bilik Mesyuarat (30 Pax)") {
                $status = 'Approved';
                $total_price = 300.00;
            }

            $sql = "INSERT INTO bookings (user_id, event_date, event_time, event_details, status, total_price, payment_status)
                    VALUES ('$user_id', '$event_date', '$requested_start', '$event_details', '$status', '$total_price', '$payment_status')";

            if ($conn->query($sql)) {
                if ($status == 'Approved') {
                    $message = "<p style='color: green; font-weight: bold;'>Booking berjaya dan diluluskan secara automatik!</p>";
                } else {
                    $message = "<p style='color: green; font-weight: bold;'>Booking berjaya! Sila tunggu kelulusan staff.</p>";
                }
            } else {
                $message = "<p style='color: red; font-weight: bold;'>Error: " . $conn->error . "</p>";
            }
        }
    }
?>

<!DOCTYPE html>
<html>
<head>
    <title>Book Hall</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="dashboard-container">

    <?php include "sidebar_customer.php"; ?>

    <div class="main-content">
        <h2>Book a Hall</h2>

        <?php if($message != "") echo $message; ?>

        <form method="POST" action="">
            <label>Event Date:</label><br>
            <input type="date" name="event_date" min="<?php echo date('Y-m-d'); ?>" required><br><br>

            <label>Event Time:</label><br>
            <input type="time" name="event_time" required><br><br>

        <label>Select Package:</label><br>
        <select name="package" required>
         <option value="" disabled selected>Pilih Pakej / Dewan</option>
         <option value="Dewan Standard (100 Pax)">Dewan Standard (100 Pax)</option>
         <option value="Dewan VIP (300 Pax)">Dewan VIP (300 Pax)</option>
          <option value="Bilik Mesyuarat (30 Pax)">Bilik Mesyuarat (30 Pax)</option>
        </select><br><br>

            <label>Event Details (Purpose, Pax, etc):</label><br>
            <textarea name="event_details" required placeholder="Describe your event here..."></textarea><br><br>

            <button type="submit" name="submit_booking">Submit Booking</button>
        </form>

    </div>

</div>

</body>
</html>