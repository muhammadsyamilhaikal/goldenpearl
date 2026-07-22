<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include "config.php";

$role = $_SESSION['role'];
$selected_date = isset($_GET['check_date']) ? trim($_GET['check_date']) : date('Y-m-d');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check Hall Availability - Golden Pearl</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="dashboard-container">
    <?php 
        if ($role === 'admin') include "sidebar_admin.php";
        elseif ($role === 'staff') include "sidebar_staff.php";
        else include "sidebar_customer.php"; 
    ?>

    <div class="main-content">
        <h2>Check Hall Availability</h2>
        <p>Select a date to view the schedule and availability of Golden Pearl halls.</p>

        <form action="availability.php" method="GET" style="max-width: 400px; margin-bottom: 30px; box-shadow: none; padding: 0; background: transparent;">
            <label for="check_date">Select Date:</label>
            <div style="display: flex; gap: 10px;">
                <input type="date" id="check_date" name="check_date" value="<?php echo htmlspecialchars($selected_date, ENT_QUOTES, 'UTF-8'); ?>" required style="margin-bottom: 0;">
                <button type="submit" class="book-btn" style="width: auto;">Check</button>
            </div>
        </form>

        <h3>Hall Usage Schedule on: <?php echo date('d/m/Y', strtotime($selected_date)); ?></h3>
        
        <div style="overflow-x: auto; margin-top: 15px;">
            <table>
                <thead>
                    <tr>
                        <th>Package Name</th>
                        <th>Capacity</th>
                        <th>Status On This Date</th>
                        <th>Booked Slots</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    foreach ($HALL_PACKAGES as $package_name => $details) { 
                        // Check approved/paid bookings for this package on the selected date
                        $sql_check = "SELECT event_time, event_details FROM bookings 
                                      WHERE event_date = ? AND status IN ('Approved', 'Paid') AND event_details LIKE ? 
                                      ORDER BY event_time ASC";
                        $stmt = $conn->prepare($sql_check);
                        $like_pkg = "%" . $package_name . "%";
                        $stmt->bind_param("ss", $selected_date, $like_pkg);
                        $stmt->execute();
                        $res = $stmt->get_result();
                        
                        $is_booked = ($res->num_rows > 0);
                    ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($package_name, ENT_QUOTES, 'UTF-8'); ?></strong></td>
                            <td><?php echo (int)$details['capacity']; ?> Pax</td>
                            <td>
                                <?php if (!$is_booked) { ?>
                                    <span style="color: white; background: #27ae60; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold;">Available All Day</span>
                                <?php } else { ?>
                                    <span style="color: white; background: #e67e22; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold;">Booked</span>
                                <?php } ?>
                            </td>
                            <td>
                                <?php 
                                if (!$is_booked) {
                                    echo "-";
                                } else {
                                    $booked_slots = array();
                                    while ($slot = $res->fetch_assoc()) {
                                        $booked_slots[] = htmlspecialchars($slot['event_time']) . ' (' . htmlspecialchars($slot['event_details']) . ')';
                                    }
                                    echo implode("<br><br>", $booked_slots);
                                }
                                ?>
                            </td>
                            <td>
                                <?php if ($role === 'customer') { ?>
                                    <a href="book_hall.php?package=<?php echo urlencode($package_name); ?>&date=<?php echo $selected_date; ?>" style="background: #5e0023; color: white; padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 13px;">Book Now</a>
                                <?php } else { ?>
                                    <a href="manage_bookings.php?date=<?php echo $selected_date; ?>" style="background: #3498db; color: white; padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 13px;">Manage Bookings</a>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php 
                        $stmt->close();
                    } 
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>