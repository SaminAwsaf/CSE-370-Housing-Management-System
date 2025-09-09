<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@300..700&display=swap" rel="stylesheet" />
    <title>XYZ School</title>
    <style>
        body {
            margin: 0;
            font-family: 'Fredoka', sans-serif;
            background: #f4f4f9;
            color: #333;
        }
        header {
            background: #2c3e50;
            padding: 10px 20px;
        }
        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
        }
        .nav_logo h1 a {
            color: #fff;
            text-decoration: none;
            font-weight: 700;
            font-size: 24px;
        }
        .nav_link {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
        }
        .nav_link li {
            margin-left: 20px;
        }
        .nav_link a {
            color: #fff;
            text-decoration: none;
            font-weight: 400;
            transition: color 0.3s;
        }
        .nav_link a:hover {
            color: #3498db;
        }
        main {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: calc(100vh - 60px);
            background: #3498db;
        }
        .home {
            text-align: center;
            color: #fff;
        }
        .home h1 {
            font-weight: 400;
            margin: 0;
        }
        footer {
            text-align: center;
            padding: 10px;
            background: #2c3e50;
            color: #fff;
            position: relative;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <div class="nav_logo">
                <h1><a href="index.php">XYZ SCHOOL</a></h1>
            </div>
            <ul class="nav_link">
                <li><a href="show_students.php">Students</a></li>
                <li><a href="#">Teachers</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <section class="home">
            <h1>Home Contents Will Be Here</h1>
        </section>
    </main>
    <footer>
        <p>&copy; 2025 XYZ School. All rights reserved.</p>
    </footer>
</body>
</html>