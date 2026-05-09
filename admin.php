<?php
session_start();

// Only allow logged-in admins
if (!isset($_SESSION['adminid'])) {
    header("Location: adminlogin.php");
    exit;
}

$admin_id = $_SESSION['adminid'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background: #f0f2f5;
        margin: 0;
        padding: 0;
    }
    header {
        background: #007BFF;
        color: white;
        padding: 15px 30px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    header .logo img {
        height: 80px;
    }
    header .title {
        font-size: 28px;
        font-weight: bold;
        text-align: center;
        flex: 1;
        margin: 0 20px;
    }
    header .back-btn {
        background: #555;
        color: white;
        text-decoration: none;
        padding: 10px 20px;
        border-radius: 5px;
        font-weight: bold;
        transition: 0.3s;
    }
    header .back-btn:hover {
        background: #333;
    }

    .container {
        display: flex;
        justify-content: center;
        margin-top: 50px;
    }
    .btn-box {
        width: 350px;
        padding: 20px;
        background: white;
        border-radius: 10px;
        box-shadow: 0 0 15px rgba(0,0,0,0.1);
        text-align: center;
    }
    .btn-box a {
        display: block;
        padding: 15px;
        background: #007BFF;
        color: white;
        text-decoration: none;
        border-radius: 8px;
        margin-bottom: 15px;
        font-size: 18px;
        transition: 0.3s;
    }
    .btn-box a:hover { background: #0056b3; }
    .btn-box a.logout {
        background: #dc3545;
    }
    .btn-box a.logout:hover {
        background: #a71d2a;
    }
</style>
</head>
<body>

<header>
    <div class="logo">
        <img src="utas-hct.png" alt="University Logo">
    </div>
    <div class="title">Admin Dashboard</div>
    <a href="homepage.php" class="back-btn">Back to Homepage</a>
</header>

<div class="container">
    <div class="btn-box">
        <a href="eventadmin.php">Manage Events</a>
        <a href="feedbackadmin.php">View Feedback</a>
        <a href="resources.php">Manage Resources</a>
        <a href="logout.php" class="logout">Logout</a>
    </div>
</div>

</body>
</html>
