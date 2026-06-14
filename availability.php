<?php
session_start();
include "config.php";

// TUKAR DI SINI: Gunakan NOT IN untuk tolak kedua-dua status
$sql = "SELECT * FROM bookings
        WHERE status NOT IN ('Rejected', 'Cancelled')
        ORDER BY event_date ASC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Hall Availability</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h2>Hall Availability</h2>

<table border="1" cellpadding="10">

<tr>
    <th>Date</th>
    <th>Time</th>
    <th>Status</th>
</tr>

<?php 
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) { 
?>

<tr>
    <td><?php echo $row['event_date']; ?></td>
    <td><?php echo $row['event_time']; ?></td>
    <td>Booked</td>
</tr>

<?php 
    }
} else {
    echo "<tr><td colspan='3'>No bookings yet. All slots are available!</td></tr>";
}
?>

</table>

<br>

<a href="customer.php">Back</a>

</body>
</html>