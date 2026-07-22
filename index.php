<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hall Booking System - Golden Pearl</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .hero-section {
            background: rgba(255, 255, 255, 0.95);
            padding: 50px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            text-align: center;
            max-width: 700px;
            margin: 40px auto;
        }
        .hero-section h1 { color: #5e0023; font-size: 36px; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="hero-section">
        <h1>Golden Pearl Hall Booking</h1>
        <p style="max-width: 100%; box-shadow: none; font-size: 18px; color: #555;">
            The perfect venue solution for weddings, seminars, conferences, and corporate events. Book your dream hall easily and securely today!
        </p>

        <div style="margin-top: 35px; display: flex; justify-content: center; gap: 15px; flex-wrap: wrap;">
            <?php if (isset($_SESSION['user_id'])) { ?>
                <?php 
                    $dest = "customer.php";
                    if ($_SESSION['role'] === 'admin') $dest = "admin.php";
                    elseif ($_SESSION['role'] === 'staff') $dest = "staff.php";
                ?>
                <a href="<?php echo $dest; ?>" class="book-btn" style="font-size: 18px; padding: 14px 28px;">Go to My Dashboard &rarr;</a>
                <a href="logout.php" class="book-btn" style="background: #e74c3c; font-size: 18px; padding: 14px 28px;">Logout</a>
            <?php } else { ?>
                <a href="login.php" class="book-btn" style="font-size: 18px; padding: 14px 28px;">Login</a>
                <a href="register.php" class="book-btn" style="background: #3498db; font-size: 18px; padding: 14px 28px;">Register New Account</a>
            <?php } ?>
        </div>
    </div>
</body>
</html>