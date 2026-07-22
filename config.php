<?php
date_default_timezone_set('Asia/Kuala_Lumpur');

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection details
$host = "your_db_host";
$user = "your_db_user";
$pass = "your_db_password";   
$db   = "your_db_name";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

$HALL_PACKAGES = [
    'Meeting Room (30 Pax)' => ['capacity' => 30, 'price' => 300.00],
    'Standard Hall (100 Pax)' => ['capacity' => 100, 'price' => 1000.00],
    'Grand Wedding Hall (500 Pax)' => ['capacity' => 500, 'price' => 3500.00]
];
?>
