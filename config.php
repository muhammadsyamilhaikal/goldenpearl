<?php
$servername = "sql306.infinityfree.com";
$username = "if0_42164453";
$password = "rkKbbG05Q2"; 
$dbname = "if0_42164453_golden_pearl";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>