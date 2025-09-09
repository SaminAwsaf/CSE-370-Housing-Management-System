<?php
session_start();
require_once('DBconnect.php');
if (!isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
    header("Location: login.php"); exit();
}
?>
<!DOCTYPE html>
<html>
<head><title>Manage Users</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f7fa;
            margin: 0;
            padding: 0;
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
            font-size: 20px;
        }
        .navbar a {
            background: #e74c3c;
            color: #fff;
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
        }
        .navbar a:hover {
            background: #c0392b;
        }
        .card {
            background: #fff;
            padding: 20px;
            margin: 20px auto;
            border-radius: 10px;
            box-shadow: 0px 5px 20px rgba(0,0,0,0.1);
            width: 90%;
        }
        h2 {
            margin-top: 0;
            color: #333;
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
            background: #2c3e50;
            color: white;
        }
        a {
            display: inline-block;
            padding: 8px 12px;
            margin: 5px 0;
            background: #3498db;
            color: #fff;
            text-decoration: none;
            border-radius: 6px;
            border: none;
            cursor: pointer;
        }
        a:hover {
            background: #2980b9;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>Manage Users</h1>
        <a href="logout.php">Logout</a>
    </div>
    <div class="card">
        <h2>Users List</h2>
        <table>
            <tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th></tr>
            <?php
            $sql = "SELECT u.user_id, u.name, u.email, 
                    CASE 
                        WHEN o.owner_id IS NOT NULL THEN 'Owner'
                        WHEN c.customer_id IS NOT NULL THEN 'Customer'
                    END AS role
                    FROM User u
                    LEFT JOIN Owner o ON u.user_id = o.owner_id
                    LEFT JOIN Customer c ON u.user_id = c.customer_id";
            $result = mysqli_query($conn, $sql);
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>
                        <td>{$row['user_id']}</td>
                        <td>{$row['name']}</td>
                        <td>{$row['email']}</td>
                        <td>{$row['role']}</td>
                      </tr>";
            }
            ?>
        </table>
    </div>
    <a href="admin_dashboard.php">Back</a>
</body>
</html>