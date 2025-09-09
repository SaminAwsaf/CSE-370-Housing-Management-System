<?php
session_start();
require_once('DBconnect.php');

// Check if logged in & role is customer
if (!isset($_SESSION['role']) || $_SESSION['role'] !== "customer") {
    header("Location: login.php");
    exit();
}

$customer_id = $_SESSION['user_id'];

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
    <title>Customer Dashboard</title>
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
            padding: 6px 12px;
            margin: 3px;
            background: #3498db;
            color: #fff;
            text-decoration: none;
            border-radius: 6px;
            font-size: 13px;
        }
        a.button:hover {
            background: #2980b9;
        }
        a.button-delete {
            background: #e74c3c;
        }
        a.button-delete:hover {
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
        <h1>Customer Dashboard</h1>
        <a href="logout.php">Logout</a>
    </div>

    <div class="card">
        <h2>Available Properties</h2>
        <table>
            <tr>
                <th scope="col">Title</th>
                <th scope="col">Type</th>
                <th scope="col">Location</th>
                <th scope="col">Price</th>
                <th scope="col">Furnished</th>
                <th scope="col">Actions</th>
            </tr>
            <?php
            $sql = "SELECT * FROM Property WHERE status = 'available'";
            $result = mysqli_query($conn, $sql);

            if (!$result) {
                echo "<tr><td colspan='6'>Error: " . htmlspecialchars(mysqli_error($conn)) . "</td></tr>";
            } elseif (mysqli_num_rows($result) === 0) {
                echo "<tr><td colspan='6'>No properties available.</td></tr>";
            } else {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>
                            <td>" . htmlspecialchars($row['title']) . "</td>
                            <td>" . htmlspecialchars($row['property_type']) . "</td>
                            <td>" . htmlspecialchars($row['location']) . "</td>
                            <td>" . htmlspecialchars($row['price']) . "</td>
                            <td>" . ($row['furnished'] ? "Yes" : "No") . "</td>
                            <td>
                                <a class='button' href='property_details.php?id={$row['property_id']}'>View</a>
                                <a class='button' href='add_to_wishlist.php?id={$row['property_id']}'>Add to Wishlist</a>
                                <a class='button' href='book_property.php?id={$row['property_id']}'>Book</a>
                                <a class='button' href='add_review.php?id={$row['property_id']}'>Review</a>
                            </td>
                          </tr>";
                }
            }
            mysqli_free_result($result);
            ?>
        </table>
    </div>

    <div class="card">
        <h2>My Wishlist</h2>
        <table>
            <tr>
                <th scope="col">Title</th>
                <th scope="col">Location</th>
                <th scope="col">Price</th>
                <th scope="col">Action</th>
            </tr>
            <?php
            $sql = "SELECT p.property_id, p.title, p.location, p.price 
                    FROM Wishlist w
                    JOIN Wishlist_Has wh ON w.wishlist_id = wh.wishlist_id
                    JOIN Property p ON wh.property_id = p.property_id
                    WHERE w.customer_id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $customer_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) === 0) {
                echo "<tr><td colspan='4'>No properties in wishlist.</td></tr>";
            } else {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>
                            <td>" . htmlspecialchars($row['title']) . "</td>
                            <td>" . htmlspecialchars($row['location']) . "</td>
                            <td>" . htmlspecialchars($row['price']) . "</td>
                            <td>
                                <a class='button button-delete' href='remove_from_wishlist.php?id={$row['property_id']}'>Remove</a>
                            </td>
                          </tr>";
                }
            }
            mysqli_stmt_close($stmt);
            ?>
        </table>
    </div>

    <div class="card">
        <h2>My Transactions</h2>
        <table>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Property</th>
                <th scope="col">Type</th>
                <th scope="col">Status</th>
            </tr>
            <?php
            $sql = "SELECT h.transaction_id, p.title, h.transaction_type, h.pay_status
                    FROM History h
                    JOIN Property p ON h.property_id = p.property_id
                    WHERE h.customer_id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $customer_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) === 0) {
                echo "<tr><td colspan='4'>No transactions found.</td></tr>";
            } else {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>
                            <td>" . htmlspecialchars($row['transaction_id']) . "</td>
                            <td>" . htmlspecialchars($row['title']) . "</td>
                            <td>" . htmlspecialchars($row['transaction_type']) . "</td>
                            <td>" . htmlspecialchars($row['pay_status']) . "</td>
                          </tr>";
                }
            }
            mysqli_stmt_close($stmt);
            ?>
        </table>
    </div>

    <div class="card">
        <h2>My Reviews</h2>
        <table>
            <tr>
                <th scope="col">Property</th>
                <th scope="col">Rating</th>
                <th scope="col">Review Date</th>
                <th scope="col">Comment</th>
            </tr>
            <?php
            $sql = "SELECT p.title, r.rating, r.review_date, c.comment_text
                    FROM Review_Ratings r
                    JOIN Property p ON r.property_id = p.property_id
                    LEFT JOIN Comments c ON r.review_id = c.review_id
                    WHERE r.customer_id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $customer_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) === 0) {
                echo "<tr><td colspan='4'>No reviews submitted.</td></tr>";
            } else {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>
                            <td>" . htmlspecialchars($row['title']) . "</td>
                            <td>" . htmlspecialchars($row['rating']) . "</td>
                            <td>" . htmlspecialchars($row['review_date']) . "</td>
                            <td>" . htmlspecialchars($row['comment_text'] ?? "—") . "</td>
                          </tr>";
                }
            }
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            ?>
        </table>
    </div>
</body>
</html>