<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>About Us |  Event Management System</title>

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

    /* Main Content */
    .container {
        max-width: 900px;
        margin: 40px auto;
        background: white;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    h2 {
        text-align: center;
        color: #007BFF;
    }
    p {
        line-height: 1.7;
        color: #333;
        font-size: 16px;
        margin-bottom: 20px;
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
            <img src="utas-hct.png" alt="UTAS Logo">
        </div>

        <div class="header-center">
            <h1>Event Management System - About Us</h1>
        </div>

        <div class="header-right"></div>
    </div>
</header>

<!-- Navigation -->
<nav>
    <a href="homepage.php">Home</a>
    <a href="choose-role.php">Sign In</a>
    <a href="register.php">Sign Up</a>
    <a href="aboutus.php">About Us</a>
</nav>

<!-- Page Content -->
<div class="container">
    <h2>About Event Management System</h2>
    <p>
        Event Management System is for the University of Technology and Applied Sciences (UTAS). 
        It is designed to make it easier for students and staff to discover, organize, and participate in university events.
    </p>

    <p>
        Our goal is to create a smooth and engaging experience where students can register for workshops, 
        seminars, competitions, and activities with just a few clicks. Event organizers can easily manage 
        participant lists, update event information, and communicate with attendees.
    </p>

    <p>
        UniVibe helps build a vibrant university atmosphere by encouraging participation, supporting student 
        growth, and strengthening the connection between students and staff. We aim to make event management 
        faster, smarter, and more enjoyable for everyone.
    </p>
</div>

<!-- Footer -->
<footer>
    &copy; <?= date("Y") ?>  Event Management System | 
    <a href="tell:99999999">Contact Us</a> |
    <a href="privacy.php">Privacy Policy</a>   
</footer>

</body>
</html>
