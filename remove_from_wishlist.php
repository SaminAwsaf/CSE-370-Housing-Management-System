<?php
session_start();
require_once('DBconnect.php');
if (!isset($_SESSION['role']) || $_SESSION['role'] !== "customer") {
    header("Location: login.php"); exit();
}
$customer_id = $_SESSION['user_id'];
$property_id = intval($_GET['id']);

// Remove from wishlist
$sql = "DELETE wh FROM Wishlist_Has wh
        JOIN Wishlist w ON wh.wishlist_id = w.wishlist_id
        WHERE wh.property_id=? AND w.customer_id=?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $property_id, $customer_id);
mysqli_stmt_execute($stmt);

header("Location: customer_dashboard.php"); exit();
?>