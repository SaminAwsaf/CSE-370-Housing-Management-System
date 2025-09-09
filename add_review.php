<?php
session_start();
require_once('DBconnect.php');

// Check if logged in & role is customer
if (!isset($_SESSION['role']) || $_SESSION['role'] !== "customer") {
    header("Location: login.php");
    exit();
}

$customer_id = $_SESSION['user_id'];
$property_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$error = "";
$success = "";

// Check if property exists
$sql = "SELECT title FROM Property WHERE property_id = ? AND status = 'available'";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $property_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$property = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$property) {
    $error = "Invalid or unavailable property.";
}

// Check if customer has a transaction for this property
if (!$error) {
    $sql = "SELECT transaction_id FROM History WHERE customer_id = ? AND property_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $customer_id, $property_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if (mysqli_num_rows($result) === 0) {
        $error = "You can only review properties you have booked.";
    }
    mysqli_stmt_close($stmt);
}

// Check if customer already reviewed this property
if (!$error) {
    $sql = "SELECT review_id FROM Review_Ratings WHERE customer_id = ? AND property_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $customer_id, $property_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if (mysqli_num_rows($result) > 0) {
        $error = "You have already reviewed this property.";
    }
    mysqli_stmt_close($stmt);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$error) {
    $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
    $comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';

    // Validate rating
    if ($rating < 1 || $rating > 5) {
        $error = "Please select a valid rating (1–5).";
    } else {
        // Insert review into Review_Ratings
        $sql = "INSERT INTO Review_Ratings (property_id, customer_id, rating, review_date) VALUES (?, ?, ?, NOW())";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "iii", $property_id, $customer_id, $rating);
        if (mysqli_stmt_execute($stmt)) {
            $review_id = mysqli_insert_id($conn);
            mysqli_stmt_close($stmt);

            // Insert comment if provided
            if ($comment !== '') {
                $sql = "INSERT INTO Comments (review_id, comment_text) VALUES (?, ?)";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "is", $review_id, $comment);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }

            $success = "Review submitted successfully!";
        } else {
            $error = "Failed to submit review: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Review</title>
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
            max-width: 600px;
        }
        h2 {
            margin-top: 0;
            color: #333;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        select, textarea, button {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
        }
        textarea {
            resize: vertical;
            min-height: 100px;
        }
        button {
            background: #3498db;
            color: #fff;
            border: none;
            cursor: pointer;
            font-weight: bold;
        }
        button:hover {
            background: #2980b9;
        }
        .error {
            color: #e74c3c;
            margin-bottom: 10px;
        }
        .success {
            color: #2ecc71;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>Add Review</h1>
        <a href="customer_dashboard.php">Back to Dashboard</a>
    </div>
    <div class="card">
        <h2>Review for <?php echo htmlspecialchars($property['title'] ?? 'Property'); ?></h2>
        <?php if ($error): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php elseif ($success): ?>
            <p class="success"><?php echo htmlspecialchars($success); ?></p>
        <?php endif; ?>
        <?php if (!$error && !$success): ?>
            <form method="POST">
                <div class="form-group">
                    <label for="rating">Rating (1–5):</label>
                    <select name="rating" id="rating" required>
                        <option value="">Select rating</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="comment">Comment (optional):</label>
                    <textarea name="comment" id="comment"></textarea>
                </div>
                <button type="submit">Submit Review</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
<?php mysqli_close($conn); ?>