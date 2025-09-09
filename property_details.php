<?php
session_start();
require_once('DBconnect.php');
if (!isset($_GET['id'])) {
    header("Location: customer_dashboard.php");
    exit();
}

$property_id = intval($_GET['id']);

// Fetch property details
$sql = "SELECT p.*, u.name AS owner_name 
        FROM Property p
        JOIN Owner o ON p.owner_id = o.owner_id
        JOIN User u ON o.owner_id = u.user_id
        WHERE p.property_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $property_id);
mysqli_stmt_execute($stmt);
$property = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
if (!$property) {
    echo "Property not found";
    exit();
}

// Fetch property photos
$sql_photos = "SELECT photo_url FROM Photos WHERE property_id = ?";
$stmt_photos = mysqli_prepare($conn, $sql_photos);
mysqli_stmt_bind_param($stmt_photos, "i", $property_id);
mysqli_stmt_execute($stmt_photos);
$photos = mysqli_stmt_get_result($stmt_photos);

// Fetch reviews
$sql_reviews = "SELECT r.rating, r.review_date, c.comment_text, u.name AS customer_name
                FROM Review_Ratings r
                JOIN Customer cu ON r.customer_id = cu.customer_id
                JOIN User u ON cu.customer_id = u.user_id
                LEFT JOIN Comments c ON r.review_id = c.review_id
                WHERE r.property_id = ?
                ORDER BY r.review_date DESC";
$stmt_reviews = mysqli_prepare($conn, $sql_reviews);
mysqli_stmt_bind_param($stmt_reviews, "i", $property_id);
mysqli_stmt_execute($stmt_reviews);
$reviews = mysqli_stmt_get_result($stmt_reviews);
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo htmlspecialchars($property['title']); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f7fa;
            margin: 0;
            padding: 0;
        }
        .card {
            background: #fff;
            padding: 20px;
            margin: 20px auto;
            border-radius: 10px;
            box-shadow: 0px 5px 20px rgba(0,0,0,0.1);
            width: 90%;
        }
        h2, h3 {
            margin-top: 0;
            color: #333;
        }
        img {
            max-width: 250px;
            margin: 10px;
            border-radius: 8px;
        }
        p {
            margin: 5px 0;
        }
        a {
            display: inline-block;
            padding: 10px 15px;
            margin: 5px;
            border-radius: 6px;
            color: #fff;
            text-decoration: none;
        }
        .wishlist { background: #27ae60; }
        .book { background: #2980b9; }
        .back { background: #e74c3c; }
        a:hover {
            opacity: 0.9;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background: #2c3e50;
            color: white;
        }
    </style>
</head>
<body>
    <div class="card">
        <h2><?php echo $property['title']; ?></h2>
        <p><b>Type:</b> <?php echo $property['property_type']; ?></p>
        <p><b>Location:</b> <?php echo $property['location']; ?></p>
        <p><b>Utility Cost:</b> <?php echo number_format($property['utility_cost'], 2); ?> ৳</p>
        <p><b>Price:</b> <?php echo number_format($property['price'], 2); ?> ৳</p>
        <p><b>Status:</b> <?php echo ucfirst($property['status']); ?></p>
        <p><b>Owner:</b> <?php echo $property['owner_name']; ?></p>
        <p><b>Furnished:</b> <?php echo $property['furnished'] ? "Yes" : "No"; ?></p>
        <p><b>Parking:</b> <?php echo $property['parking'] ? "Yes" : "No"; ?></p>
    </div>

    <div class="card">
        <h3>Photos</h3>
        <?php while ($row = mysqli_fetch_assoc($photos)) { echo "<img src='{$row['photo_url']}'>"; } ?>
    </div>

    <?php if (isset($_SESSION['role']) && $_SESSION['role'] == "customer"): ?>
    <div class="card">
        <h3>Actions</h3>
        <a class="wishlist" href="add_to_wishlist.php?id=<?php echo $property_id; ?>">Add to Wishlist</a>
        <a class="book" href="book_property.php?id=<?php echo $property_id; ?>">Book Property</a>
    </div>
    <?php endif; ?>

    <div class="card">
        <h3>Reviews</h3>
        <table>
            <tr><th>Customer</th><th>Rating</th><th>Date</th><th>Comment</th></tr>
            <?php while ($r = mysqli_fetch_assoc($reviews)) {
                echo "<tr>
                        <td>{$r['customer_name']}</td>
                        <td>{$r['rating']}</td>
                        <td>{$r['review_date']}</td>
                        <td>".($r['comment_text'] ?? "—")."</td>
                      </tr>";
            } ?>
        </table>
    </div>
    <a class="back" href="customer_dashboard.php">⬅ Back</a>
</body>
</html>