<?php
session_start();
require_once('DBconnect.php');

// Check if logged in as admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
    header("Location: login.php");
    exit();
}

$admin_id = $_SESSION['admin_id'];

// Fetch counts
$totalUsers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM User"))['c'];
$totalOwners = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM Owner"))['c'];
$totalCustomers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM Customer"))['c'];
$totalProperties = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM Property"))['c'];
$totalReviews = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM Review_Ratings"))['c'];
$totalTrans = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM History"))['c'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <style>
        body {
            margin: 0;
            font-family: "Segoe UI", Tahoma, sans-serif;
            background: #f5f7fb;
            color: #333;
        }
        .navbar {
            background: #2c3e50;
            padding: 15px 20px;
            color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar h1 {
            margin: 0;
            font-size: 24px;
        }
        .navbar a {
            color: #fff;
            text-decoration: none;
            background: #e74c3c;
            padding: 8px 15px;
            border-radius: 5px;
            transition: 0.3s;
        }
        .navbar a:hover {
            background: #c0392b;
        }
        .card {
            background: #fff;
            border-radius: 12px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            margin: 20px auto;
            width: 90%;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.15);
        }
        h2 {
            margin-top: 0;
            color: #2980b9;
            font-size: 32px;
        }
        p {
            margin: 10px 0 0;
            font-size: 16px;
            color: #555;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background: #2980b9;
            color: white;
        }
        .summary {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
        }
        .summary div {
            margin: 10px;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>Admin Dashboard</h1>
        <a href="logout.php">Logout</a>
    </div>

    <div class="card">
        <h2>Summary</h2>
        <div class="summary">
            <div><h2><?php echo $totalUsers; ?></h2><p>Total Users</p></div>
            <div><h2><?php echo $totalOwners; ?></h2><p>Total Owners</p></div>
            <div><h2><?php echo $totalCustomers; ?></h2><p>Total Customers</p></div>
            <div><h2><?php echo $totalProperties; ?></h2><p>Total Properties</p></div>
            <div><h2><?php echo $totalReviews; ?></h2><p>Total Reviews</p></div>
            <div><h2><?php echo $totalTrans; ?></h2><p>Total Transactions</p></div>
        </div>
    </div>

    <div class="card">
        <h2>All Listed Properties</h2>
        <table>
            <tr>
                <th>Title</th>
                <th>Type</th>
                <th>Location</th>
                <th>Price</th>
                <th>Status</th>
                <th>Owner</th>
                <th>Actions</th>
            </tr>
            <?php
            $sql = "SELECT p.property_id, p.title, p.property_type, p.location, p.price, p.status, u.name AS owner_name
                    FROM Property p
                    JOIN Owner o ON p.owner_id = o.owner_id
                    JOIN User u ON o.owner_id = u.user_id";
            $result = mysqli_query($conn, $sql);

            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>
                        <td>{$row['title']}</td>
                        <td>{$row['property_type']}</td>
                        <td>{$row['location']}</td>
                        <td>{$row['price']}</td>
                        <td>{$row['status']}</td>
                        <td>{$row['owner_name']}</td>
                        <td><a href='property_details.php?id={$row['property_id']}'>View</a></td>
                      </tr>";
            }
            ?>
        </table>
    </div>
    <div class="card">
        <h2>Navigation</h2>
        <a href="manage_users.php" style="display: inline-block; padding: 8px 12px; margin: 5px; background: #3498db; color: #fff; text-decoration: none; border-radius: 6px;">Manage Users</a>
        <a href="manage_properties.php" style="display: inline-block; padding: 8px 12px; margin: 5px; background: #3498db; color: #fff; text-decoration: none; border-radius: 6px;">Manage Properties</a>
        <a href="manage_reviews.php" style="display: inline-block; padding: 8px 12px; margin: 5px; background: #3498db; color: #fff; text-decoration: none; border-radius: 6px;">Manage Reviews</a>
        <a href="manage_transactions.php" style="display: inline-block; padding: 8px 12px; margin: 5px; background: #3498db; color: #fff; text-decoration: none; border-radius: 6px;">Manage Transactions</a>
    </div>
</body>
</html>