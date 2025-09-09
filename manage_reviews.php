<?php
session_start();
require_once('DBconnect.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
    header("Location: login.php");
    exit();
}

// Check database connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Reviews</title>
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
            margin: 5px 2px;
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
        .action-delete {
            background: #e74c3c;
        }
        .action-delete:hover {
            background: #c0392b;
        }
        @media screen and (max-width: 600px) {
            table {
                display: block;
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>Manage Reviews</h1>
        <a href="logout.php">Logout</a>
    </div>
    <div class="card">
        <h2>Reviews List</h2>
        <table>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Property</th>
                <th scope="col">Rating</th>
                <th scope="col">Customer</th>
                <th scope="col">Comment</th>
                <th scope="col">Review Date</th>
                <th scope="col">Actions</th>
            </tr>
            <?php
            $sql = "SELECT r.review_id, p.title, r.rating, u.name AS customer_name, c.comment_text, r.review_date
                    FROM Review_Ratings r
                    JOIN Property p ON r.property_id = p.property_id
                    JOIN Customer c ON r.customer_id = c.customer_id
                    JOIN User u ON c.customer_id = u.user_id
                    LEFT JOIN Comments c ON r.review_id = c.review_id";
            $result = mysqli_query($conn, $sql);

            if (!$result) {
                echo "<tr><td colspan='7'>Error: " . htmlspecialchars(mysqli_error($conn)) . "</td></tr>";
            } elseif (mysqli_num_rows($result) === 0) {
                echo "<tr><td colspan='7'>No reviews found.</td></tr>";
            } else {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>
                            <td>" . htmlspecialchars($row['review_id']) . "</td>
                            <td>" . htmlspecialchars($row['title']) . "</td>
                            <td>" . htmlspecialchars($row['rating']) . "</td>
                            <td>" . htmlspecialchars($row['customer_name']) . "</td>
                            <td>" . htmlspecialchars($row['comment_text'] ?? "—") . "</td>
                            <td>" . htmlspecialchars($row['review_date']) . "</td>
                            <td>
                                <a href='edit_review.php?id={$row['review_id']}'>Edit</a>
                                <a href='delete_review.php?id={$row['review_id']}' class='action-delete' onclick='return confirm(\"Are you sure you want to delete this review?\")'>Delete</a>
                            </td>
                          </tr>";
                }
            }
            mysqli_free_result($result);
            mysqli_close($conn);
            ?>
        </table>
    </div>
    <a href="admin_dashboard.php">Back</a>
</body>
</html>