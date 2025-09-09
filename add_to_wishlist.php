<?php
session_start();
require_once('DBconnect.php');
if (!isset($_SESSION['role']) || $_SESSION['role'] !== "customer") {
    header("Location: login.php"); exit();
}
$customer_id = $_SESSION['user_id'];
$property_id = intval($_GET['id']);

// Ensure wishlist exists
$sql = "SELECT wishlist_id FROM Wishlist WHERE customer_id=?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $customer_id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
if ($row = mysqli_fetch_assoc($res)) {
    $wishlist_id = $row['wishlist_id'];
} else {
    $sql = "INSERT INTO Wishlist (customer_id, saved_date) VALUES (?, CURDATE())";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $customer_id);
    mysqli_stmt_execute($stmt);
    $wishlist_id = mysqli_insert_id($conn);
}

// Insert relation
$sql = "INSERT IGNORE INTO Wishlist_Has (property_id, wishlist_id) VALUES (?, ?)";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $property_id, $wishlist_id);
mysqli_stmt_execute($stmt);

header("Location: customer_dashboard.php"); exit();
?>