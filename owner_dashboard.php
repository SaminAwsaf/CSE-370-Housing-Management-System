<?php
session_start();
require_once('DBconnect.php');

// Check if logged in & role is owner
if (!isset($_SESSION['role']) || $_SESSION['role'] !== "owner") {
    header("Location: login.php");
    exit();
}

$owner_id = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Owner Dashboard</title>
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
        a.button {
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
        a.button:hover {
            background: #2980b9;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>Owner Dashboard</h1>
        <a href="logout.php">Logout</a>
    </div>

    <div class="card">
        <h2>My Properties</h2>
        <a href="add_property.php" class="button">+ Add Property</a>
        <table>
            <tr>
                <th>Title</th>
                <th>Type</th>
                <th>Location</th>
                <th>Price</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            <?php
            $sql = "SELECT * FROM Property WHERE owner_id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $owner_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>
                        <td>{$row['title']}</td>
                        <td>{$row['property_type']}</td>
                        <td>{$row['location']}</td>
                        <td>{$row['price']}</td>
                        <td>{$row['status']}</td>
                        <td>
                            <a class='button' href='edit_property.php?id={$row['property_id']}'>Edit</a>
                            <a class='button' style='background:#e74c3c;' href='delete_property.php?id={$row['property_id']}'>Delete</a>
                        </td>
                      </tr>";
            }
            ?>
        </table>
    </div>

    <div class="card">
        <h2>Transaction History</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Property</th>
                <th>Type</th>
                <th>Status</th>
                <th>Customer</th>
            </tr>
            <?php
            $sql = "SELECT h.transaction_id, p.title, h.transaction_type, h.pay_status, u.name AS customer_name
                    FROM History h
                    JOIN Customer c ON h.customer_id = c.customer_id
                    JOIN User u ON c.customer_id = u.user_id
                    JOIN Property p ON h.property_id = p.property_id
                    WHERE h.owner_id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $owner_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>
                        <td>{$row['transaction_id']}</td>
                        <td>{$row['title']}</td>
                        <td>{$row['transaction_type']}</td>
                        <td>{$row['pay_status']}</td>
                        <td>{$row['customer_name']}</td>
                      </tr>";
            }
            ?>
        </table>
    </div>

    <div class="card">
        <h2>Reviews on My Properties</h2>
        <table>
            <tr>
                <th>Property</th>
                <th>Rating</th>
                <th>Review Date</th>
                <th>Comment</th>
                <th>Customer</th>
            </tr>
            <?php
            $sql = "SELECT p.title, r.rating, r.review_date, cmt.comment_text, u.name AS customer_name
                    FROM Review_Ratings r
                    JOIN Property p ON r.property_id = p.property_id
                    JOIN Customer cu ON r.customer_id = cu.customer_id
                    JOIN User u ON cu.customer_id = u.user_id
                    LEFT JOIN Comments cmt ON r.review_id = cmt.review_id
                    WHERE p.owner_id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $owner_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>
                        <td>{$row['title']}</td>
                        <td>{$row['rating']}</td>
                        <td>{$row['review_date']}</td>
                        <td>".($row['comment_text'] ?? "—")."</td>
                        <td>{$row['customer_name']}</td>
                      </tr>";
            }
            ?>
        </table>
    </div>
</body>
</html>