<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('ACCESS DENIED!'); window.location='login.php';</script>";
    exit();
}
include "config.php";

if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    if ($delete_id === (int)$_SESSION['user_id']) {
        echo "<script>alert('You cannot delete your own account!'); window.location='manage_staff.php';</script>";
    } else {
        $del_stmt = $conn->prepare("DELETE FROM users WHERE user_id = ? AND role = 'staff'");
        $del_stmt->bind_param("i", $delete_id);
        if ($del_stmt->execute()) echo "<script>alert('Staff account successfully deleted.'); window.location='manage_staff.php';</script>";
        $del_stmt->close();
    }
}
$result = $conn->query("SELECT user_id, name, email FROM users WHERE role = 'staff' ORDER BY name ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Staff - Golden Pearl</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="dashboard-container">
    <?php include "sidebar_admin.php"; ?>
    <div class="main-content">
        <h2>Staff Management</h2>
        <div style="margin: 20px 0;"><a href="add_staff.php" class="book-btn">+ Add New Staff</a></div>
        <div style="overflow-x: auto;">
            <table>
                <thead><tr><th>ID</th><th>Full Name</th><th>Email Address</th><th>Action</th></tr></thead>
                <tbody>
                    <?php if ($result->num_rows > 0) { ?>
                        <?php while ($row = $result->fetch_assoc()) { ?>
                            <tr>
                                <td>#<?php echo (int)$row['user_id']; ?></td>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td><a href="manage_staff.php?delete_id=<?php echo (int)$row['user_id']; ?>" class="delete-btn" onclick="return confirm('Delete this staff account?');">Delete</a></td>
                            </tr>
                        <?php } } else { ?>
                        <tr><td colspan="4" style="text-align: center; padding: 20px;">No registered staff found.</td></tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>