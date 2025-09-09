<?php
session_start();
require_once('DBconnect.php');
if (!isset($_SESSION['role']) || $_SESSION['role'] !== "owner") {
    header("Location: login.php"); exit();
}
$owner_id = $_SESSION['user_id'];
$property_id = intval($_GET['id']);

// Fetch existing data
$sql = "SELECT * FROM Property WHERE property_id=? AND owner_id=?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $property_id, $owner_id);
mysqli_stmt_execute($stmt);
$property = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title']; $type = $_POST['type'];
    $location = $_POST['location']; $utility_cost = $_POST['utility_cost'] ?? 0.00;
    $price = $_POST['price'];
    $status = $_POST['status'];
    $furnished = isset($_POST['furnished']) ? 1 : 0;
    $parking = isset($_POST['parking']) ? 1 : 0;

    $sql = "UPDATE Property SET title=?, property_type=?, location=?, utility_cost=?, price=?, status=?, furnished=?, parking=? 
            WHERE property_id=? AND owner_id=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssddssiii", $title, $type, $location, $utility_cost, $price, $status, $furnished, $parking, $property_id, $owner_id);
    if (mysqli_stmt_execute($stmt)) {
        header("Location: owner_dashboard.php"); exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Edit Property</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f7fa;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .card {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 5px 20px rgba(0,0,0,0.1);
            width: 350px;
            text-align: center;
        }
        h2 {
            margin-top: 0;
            color: #333;
        }
        input, select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 6px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: none;
            border-radius: 6px;
            background: #3498db;
            color: #fff;
            font-weight: bold;
            cursor: pointer;
        }
        button:hover {
            background: #2980b9;
        }
        a {
            display: inline-block;
            padding: 10px;
            background: #3498db;
            color: #fff;
            text-decoration: none;
            border-radius: 6px;
            margin-top: 10px;
        }
        a:hover {
            background: #2980b9;
        }
    </style>
</head>
<body>
    <div class="card">
        <h2>Edit Property</h2>
        <form method="post">
            <input type="text" name="title" value="<?php echo $property['title']; ?>" required><br>
            <input type="text" name="type" value="<?php echo $property['property_type']; ?>" required><br>
            <input type="text" name="location" value="<?php echo $property['location']; ?>"><br>
            <input type="number" step="0.01" name="utility_cost" value="<?php echo $property['utility_cost']; ?>"><br>
            <input type="number" step="0.01" name="price" value="<?php echo $property['price']; ?>"><br>
            <select name="status">
                <option value="available" <?php if($property['status']=="available") echo "selected"; ?>>Available</option>
                <option value="sold" <?php if($property['status']=="sold") echo "selected"; ?>>Sold</option>
                <option value="rented" <?php if($property['status']=="rented") echo "selected"; ?>>Rented</option>
                <option value="booked" <?php if($property['status']=="booked") echo "selected"; ?>>Booked</option>
            </select><br>
            <label><input type="checkbox" name="furnished" <?php if($property['furnished']) echo "checked"; ?>> Furnished</label><br>
            <label><input type="checkbox" name="parking" <?php if($property['parking']) echo "checked"; ?>> Parking</label><br>
            <button type="submit">Update</button>
        </form>
        <a href="owner_dashboard.php">Back</a>
    </div>
</body>
</html>