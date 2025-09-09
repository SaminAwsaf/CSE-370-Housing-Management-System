<?php
require_once('DBconnect.php');

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name']) && isset($_POST['email']) && isset($_POST['password']) 
    && isset($_POST['phone_number']) && isset($_POST['nid']) && isset($_POST['role'])) {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $phone = trim($_POST['phone_number']);
    $nid = trim($_POST['nid']);
    $role = $_POST['role'];

    // Validate inputs
    if (strlen($password) < 8) {
        $error = "Password must be at least 8 characters long.";
    } elseif (!in_array($role, ['owner', 'customer'])) {
        $error = "Invalid role selected.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Insert into User table
        $sql_user = "INSERT INTO User (name, email, password, phone_number, nid) VALUES (?, ?, ?, ?, ?)";
        $stmt_user = mysqli_prepare($conn, $sql_user);
        mysqli_stmt_bind_param($stmt_user, "sssss", $name, $email, $hashed_password, $phone, $nid);

        if (mysqli_stmt_execute($stmt_user)) {
            $user_id = mysqli_insert_id($conn);

            // Insert into Owner or Customer table based on role
            if ($role == "owner") {
                $sql_owner = "INSERT INTO Owner (owner_id) VALUES (?)";
                $stmt_owner = mysqli_prepare($conn, $sql_owner);
                mysqli_stmt_bind_param($stmt_owner, "i", $user_id);
                mysqli_stmt_execute($stmt_owner);
                mysqli_stmt_close($stmt_owner);
            } else {
                $sql_customer = "INSERT INTO Customer (customer_id) VALUES (?)";
                $stmt_customer = mysqli_prepare($conn, $sql_customer);
                mysqli_stmt_bind_param($stmt_customer, "i", $user_id);
                mysqli_stmt_execute($stmt_customer);
                mysqli_stmt_close($stmt_customer);
            }

            $success = "Registration successful! Please log in.";
            // Optionally, redirect to login.php after a short delay
            // header("Refresh: 2; url=login.php");
        } else {
            if (mysqli_errno($conn) == 1062) { // Duplicate entry error
                $error = "Email already exists.";
            } else {
                $error = "Registration failed: " . mysqli_error($conn);
            }
        }
        mysqli_stmt_close($stmt_user);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
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
        .error {
            color: #e74c3c;
            margin-bottom: 10px;
        }
        .success {
            color: #2ecc71;
            margin-bottom: 10px;
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
        <h2>Register</h2>
        <?php if ($error): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php elseif ($success): ?>
            <p class="success"><?php echo htmlspecialchars($success); ?></p>
        <?php endif; ?>
        <form method="post" action="">
            <input type="text" name="name" placeholder="Full Name" required value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
            <input type="email" name="email" placeholder="Email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            <input type="password" name="password" placeholder="Password (min 8 characters)" required>
            <input type="text" name="phone_number" placeholder="Phone Number" value="<?php echo isset($_POST['phone_number']) ? htmlspecialchars($_POST['phone_number']) : ''; ?>">
            <input type="text" name="nid" placeholder="National ID" value="<?php echo isset($_POST['nid']) ? htmlspecialchars($_POST['nid']) : ''; ?>">
            <select name="role" required>
                <option value="">Select Role</option>
                <option value="owner" <?php echo (isset($_POST['role']) && $_POST['role'] == 'owner') ? 'selected' : ''; ?>>Owner</option>
                <option value="customer" <?php echo (isset($_POST['role']) && $_POST['role'] == 'customer') ? 'selected' : ''; ?>>Customer</option>
            </select>
            <button type="submit">Register</button>
        </form>
        <a href="login.php">Back to Login</a>
    </div>
</body>
</html>
<?php mysqli_close($conn); ?>