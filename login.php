<?php
require_once('DBconnect.php');
session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email']) && isset($_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check admin login
    $sql_admin = "SELECT admin_id, email, password FROM Admin WHERE email = ?";
    $stmt_admin = mysqli_prepare($conn, $sql_admin);
    mysqli_stmt_bind_param($stmt_admin, "s", $email);
    mysqli_stmt_execute($stmt_admin);
    $result_admin = mysqli_stmt_get_result($stmt_admin);

    if (!$result_admin) {
        $error = "Database error: " . mysqli_error($conn);
    } elseif ($admin = mysqli_fetch_assoc($result_admin)) {
        if (password_verify($password, $admin['password'])) {
            $_SESSION['role'] = "admin";
            $_SESSION['admin_id'] = $admin['admin_id'];
            $_SESSION['email'] = $admin['email'];
            header("Location: admin_dashboard.php");
            exit();
        } else {
            $error = "Wrong email or password!";
        }
    }
    mysqli_stmt_close($stmt_admin);

    // Check user login (for owners and customers)
    $sql_user = "SELECT user_id, email, password FROM User WHERE email = ?";
    $stmt_user = mysqli_prepare($conn, $sql_user);
    mysqli_stmt_bind_param($stmt_user, "s", $email);
    mysqli_stmt_execute($stmt_user);
    $result_user = mysqli_stmt_get_result($stmt_user);

    if (!$result_user) {
        $error = "Database error: " . mysqli_error($conn);
    } elseif ($user = mysqli_fetch_assoc($result_user)) {
        if (password_verify($password, $user['password'])) {
            $user_id = $user['user_id'];

            // Check if user is an owner
            $sql_owner = "SELECT owner_id FROM Owner WHERE owner_id = ?";
            $stmt_owner = mysqli_prepare($conn, $sql_owner);
            mysqli_stmt_bind_param($stmt_owner, "i", $user_id);
            mysqli_stmt_execute($stmt_owner);
            $result_owner = mysqli_stmt_get_result($stmt_owner);

            if (mysqli_num_rows($result_owner) > 0) {
                $_SESSION['role'] = "owner";
                $_SESSION['user_id'] = $user_id;
                header("Location: owner_dashboard.php");
                exit();
            }
            mysqli_stmt_close($stmt_owner);

            // Check if user is a customer
            $sql_customer = "SELECT customer_id FROM Customer WHERE customer_id = ?";
            $stmt_customer = mysqli_prepare($conn, $sql_customer);
            mysqli_stmt_bind_param($stmt_customer, "i", $user_id);
            mysqli_stmt_execute($stmt_customer);
            $result_customer = mysqli_stmt_get_result($stmt_customer);

            if (mysqli_num_rows($result_customer) > 0) {
                $_SESSION['role'] = "customer";
                $_SESSION['user_id'] = $user_id;
                header("Location: customer_dashboard.php");
                exit();
            }
            mysqli_stmt_close($stmt_customer);

            $error = "User role not found!";
        } else {
            $error = "Wrong email or password!";
        }
    } else {
        $error = "Wrong email or password!";
    }
    mysqli_stmt_close($stmt_user);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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
        input {
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
            color: red;
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="card">
        <h2>🔑 Login</h2>
        <form method="post" action="">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <form action="registration.php" method="get">
            <button type="submit">Register</button>
        </form>
        <?php if (!empty($error)) echo "<p class='error'>" . htmlspecialchars($error) . "</p>"; ?>
    </div>
</body>
</html>
<?php mysqli_close($conn); ?>