<?php
session_start();
require_once('DBconnect.php');
if (!isset($_SESSION['role']) || $_SESSION['role'] !== "customer") {
    header("Location: login.php"); exit();
}
$customer_id = $_SESSION['user_id'];
$property_id = intval($_GET['id']);

// Fetch property + owner
$sql = "SELECT owner_id FROM Property WHERE property_id=? AND status='available'";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $property_id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
if (!$row = mysqli_fetch_assoc($res)) {
    header("Location: customer_dashboard.php?error=unavailable"); exit();
}
$owner_id = $row['owner_id'];

// Create history record
$sql = "INSERT INTO History (transaction_type, pay_status, owner_id, customer_id, property_id) 
        VALUES ('booking', 'pending', ?, ?, ?)";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "iii", $owner_id, $customer_id, $property_id);
mysqli_stmt_execute($stmt);

// Update property status
$sql = "UPDATE Property SET status='booked', customer_id=? WHERE property_id=?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $customer_id, $property_id);
mysqli_stmt_execute($stmt);

header("Location: customer_dashboard.php"); exit();
?>