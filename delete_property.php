<?php
session_start();
require_once('DBconnect.php');
if (!isset($_SESSION['role']) || $_SESSION['role'] !== "owner") {
    header("Location: login.php"); exit();
}
$owner_id = $_SESSION['user_id'];
$property_id = intval($_GET['id']);

$sql = "DELETE FROM Property WHERE property_id=? AND owner_id=?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $property_id, $owner_id);
mysqli_stmt_execute($stmt);

header("Location: owner_dashboard.php"); exit();
?>