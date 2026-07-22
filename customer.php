<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}
include "config.php";

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT name, email FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_data = $stmt->get_result()->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard - Golden Pearl</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="dashboard-container">
    <?php include "sidebar_customer.php"; ?>
    <div class="main-content">
        <h2>Welcome, <?php echo htmlspecialchars($user_data['name']); ?>!</h2>
        <p>You are on the main dashboard of the hall booking system.</p>
        
        <div class="menu-box" style="width: 100%; max-width: 500px; margin-top: 20px;">
            <h3>Account Information</h3>
            <p><strong>Customer ID:</strong> #<?php echo (int)$user_id; ?></p>
            <p><strong>Full Name:</strong> <?php echo htmlspecialchars($user_data['name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user_data['email']); ?></p>
            
            <div style="margin-top: 20px;">
                <a href="book_hall.php" class="book-btn">+ Make New Booking</a>
                <a href="my_bookings.php" class="book-btn" style="background:#475569; margin-left: 5px;">My Bookings</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>