<?php
session_start();
include "config.php";

// Pastikan hanya admin sahaja yang boleh mengakses halaman ini
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// LOGIC UNTUK DELETE STAFF
if (isset($_POST['delete_staff'])) {
    $staff_id = $_POST['staff_id'];

    // Pastikan kita hanya delete user yang role='staff' sahaja untuk keselamatan
    $delete_sql = "DELETE FROM users WHERE user_id='$staff_id' AND role='staff'";
    
    if ($conn->query($delete_sql)) {
        echo "<script>alert('Staff successfully removed!');</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
}

// AMBIL SEMUA DATA STAFF
$sql = "SELECT * FROM users WHERE role='staff' ORDER BY name ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Staff</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .action-form {
            background: none;
            padding: 0;
            box-shadow: none;
            margin: 0;
            max-width: 100%;
        }
        .delete-btn {
            background-color: #e74c3c;
            color: white;
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            width: auto;
        }
        .delete-btn:hover {
            background-color: #c0392b;
        }
    </style>
</head>
<body>

<div class="dashboard-container">

    <?php include "sidebar_admin.php"; ?>

    <div class="main-content">

        <h2>Manage Staff (Admin)</h2>

<table border="1" cellpadding="10">

<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Email</th>
    <th>Action</th>
</tr>

<?php 
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) { 
?>

<tr>
    <td><?php echo $row['user_id']; ?></td>
    <td><?php echo $row['name']; ?></td>
    <td><?php echo $row['email']; ?></td>
    <td>
        <form method="POST" class="action-form" onsubmit="return confirm('Are you sure you want to remove this staff? This action cannot be undone.');">
            <input type="hidden" name="staff_id" value="<?php echo $row['user_id']; ?>">
            <button type="submit" name="delete_staff" class="delete-btn">Remove</button>
        </form>
    </td>
</tr>

<?php 
    }
} else {
    echo "<tr><td colspan='4'>No staff found.</td></tr>";
}
?>

</table>

    </div>

</div>

</body>
</html>