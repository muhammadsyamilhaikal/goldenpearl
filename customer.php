<?php
session_start();
include "config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

/* GET BOOKINGS */

$sql = "SELECT * FROM bookings
        WHERE status != 'Rejected'";

$result = $conn->query($sql);

$events = [];

while($row = $result->fetch_assoc()) {

    $events[] = [
        'title' => 'Booked',
        'start' => $row['event_date'],
        'description' => $row['event_details']
    ];
}
?>

<!DOCTYPE html>
<html>
<head>

    <title>Customer Dashboard</title>

    <link rel="stylesheet" href="style.css">

    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css' rel='stylesheet'>

    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>

</head>

<body>

<div class="dashboard-container">

    <?php include "sidebar_customer.php"; ?>

    <div class="main-content">

    <!-- MAIN CONTENT -->
    <div class="main-content">

        <h1>Welcome, <?php echo $_SESSION['name']; ?></h1>

        <div id='calendar'></div>

        <br>

        <a href="book_hall.php" class="book-btn">
            Book Hall
        </a>

    </div>

</div>

<script>

document.addEventListener('DOMContentLoaded', function() {

    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {

        initialView: 'dayGridMonth',

        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: '',
        },

        events: <?php echo json_encode($events); ?>,

        eventClick: function(info) {

            alert(
                info.event.title + "\n" +
                info.event.extendedProps.description
            );
        }
    });

    calendar.render();
});

</script>

</body>
</html>