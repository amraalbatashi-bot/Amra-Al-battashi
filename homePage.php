<?php
session_start();
include 'db.php';

// Fetch upcoming 3 events for homepage highlights
$query = $conn->prepare("SELECT Event_ID, Title, Description, DateE, Location FROM EVENT ORDER BY DateE ASC LIMIT 3");
$query->execute();
$upcoming_events = $query->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Home | Event Management System</title>
<style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        background-color: #f5f5f5;
    }
    /* Header */
    header {
        background-color: #007BFF;
        color: white;
        padding: 15px 20px;
    }
    .header-container {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .header-left img {
        height: 120px;
    }
    .header-center h1 {
        margin: 0;
        font-size: 28px;
        text-align: center;
    }
    .header-right {
        width: 120px;
    }

    /* Navigation */
    nav {
        background-color: #0056b3;
        display: flex;
        justify-content: center;
        padding: 10px 0;
    }
    nav a {
        color: white;
        text-decoration: none;
        padding: 10px 20px;
        margin: 0 5px;
        border-radius: 5px;
        transition: background-color 0.3s;
    }
    nav a:hover {
        background-color: #004494;
    }

    /* Hero Section */
    .hero {
        background: url('hero-bg.jpg') center/cover no-repeat;
        text-align: center;
        padding: 100px 20px;
        color: black;
    }
    .hero h2 {
        font-size: 48px;
        margin-bottom: 20px;
    }
    .hero p {
        font-size: 20px;
        margin-bottom: 30px;
        color: #333;
    }
    .hero a {
        background-color: #007BFF;
        color: white;
        padding: 12px 25px;
        text-decoration: none;
        border-radius: 5px;
        font-size: 16px;
    }
    .hero a:hover {
        background-color: #0056b3;
    }

    /* Event Cards */
    .container {
        max-width: 1000px;
        margin: 40px auto;
        padding: 0 20px;
    }
    h2.section-title {
        text-align: center;
        color: #333;
        margin-bottom: 30px;
    }
    .event-cards {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 20px;
    }
    .event-card {
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        padding: 20px;
        transition: transform 0.2s;
    }
    .event-card:hover {
        transform: translateY(-5px);
    }
    .event-title {
        font-weight: bold;
        font-size: 18px;
        color: #007BFF;
        margin-bottom: 10px;
    }
    .event-info {
        font-size: 14px;
        color: #555;
        margin-bottom: 5px;
    }
    .event-description {
        font-size: 13px;
        color: #666;
        margin: 10px 0;
    }
    .view-btn {
        display: inline-block;
        background-color: #007BFF;
        color: white;
        text-decoration: none;
        padding: 8px 12px;
        border-radius: 5px;
        font-size: 14px;
    }
    .view-btn:hover {
        background-color: #0056b3;
    }

    /* Footer */
    footer {
        background-color: #0056b3;
        color: white;
        text-align: center;
        padding: 20px;
        margin-top: 50px;
    }
    footer a {
        color: #007BFF;
        text-decoration: none;
    }
    footer a:hover {
        text-decoration: underline;
    }
</style>
</head>
<body>

<!-- Header -->
<header>
    <div class="header-container">
        <div class="header-left">
            <img src="utas-hct.png" alt="University Logo">
        </div>

        <div class="header-center">
            <h1>Event Management System</h1>
        </div>

        <div class="header-right"></div>
    </div>
</header>

<!-- Navigation (UPDATED) -->
<nav>
    <a href="login.php">Sign in</a>
	<a href="register.php">Sign up</a>
	<a href="loginadmin.php">Admin</a>
    <a href="Aboutus.php">About Us</a>  
</nav>

<!-- Hero Section -->
<div class="hero">
    <h2>Welcome to Event Management System</h2>
    <p>Discover, explore, and book exciting events happening near you!</p>
    <a href="choose-role.php">Login to Booking</a>
</div>

<!-- Upcoming Events -->
<div class="container">
    <h2 class="section-title">Upcoming Events</h2>
    <div class="event-cards">
        <?php foreach ($upcoming_events as $event): ?>
            <div class="event-card">
                <div class="event-title"><?= htmlspecialchars($event['Title']) ?></div>
                <div class="event-info">📅 <?= date("F j, Y", strtotime($event['DateE'])) ?> | 📍 <?= htmlspecialchars($event['Location']) ?></div>
                <div class="event-description"><?= htmlspecialchars($event['Description']) ?></div>
                <a href="choose-role.php" class="view-btn">Login to Booking</a>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Footer -->
<footer>
    &copy; <?= date("Y") ?> Event Management System. All rights reserved. | 
    <a href="tell:95275536">Contact Us</a> | 
    <a href="privacy.php">Privacy Policy</a>
</footer>

</body>
</html>
