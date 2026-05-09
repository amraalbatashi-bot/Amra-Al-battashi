<?php
// privacy.php – Privacy Policy Page
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #007BFF;
            color: white;
            padding: 15px 20px;
            text-align: center;
        }

        .container {
            max-width: 900px;
            margin: 30px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        h2 {
            color: #007BFF;
            margin-top: 0;
        }

        h3 {
            margin-top: 30px;
            color: #333;
        }

        p {
            line-height: 1.6;
            color: #444;
        }

        ul {
            margin-left: 20px;
            color: #444;
        }

        a.back-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 16px;
            background-color: #007BFF;
            color: #fff;
            text-decoration: none;
            border-radius: 6px;
        }

        a.back-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<header>
    <h1>Privacy Policy</h1>
</header>

<div class="container">

    <h2>Your Privacy Matters</h2>
    <p>
        This Privacy Policy explains how we collect, use, and protect your information when you use the
        Event Management System of the University. By using this system, you agree to the terms described here.
    </p>

    <h3>1. Information We Collect</h3>
    <p>We may collect the following information when you use the system:</p>
    <ul>
        <li>Full Name</li>
        <li>Email Address</li>
        <li>Phone Number</li>
        <li>Account Credentials (securely stored)</li>
        <li>Event Bookings & Attendance Records</li>
        <li>Feedback or Comments submitted by you</li>
    </ul>

    <h3>2. How We Use Your Information</h3>
    <p>Your information is used for purposes such as:</p>
    <ul>
        <li>Managing event registrations and bookings</li>
        <li>Improving event organization</li>
        <li>Sending important updates or confirmations</li>
        <li>Ensuring system security and user verification</li>
        <li>Enhancing user experience</li>
    </ul>

    <h3>3. Data Protection</h3>
    <p>
        Your data is stored securely and protected using industry-standard measures.  
        Passwords are encrypted using strong hashing algorithms.  
        Only authorized staff can access required information.
    </p>

    <h3>4. Sharing of Information</h3>
    <p>
        We <strong>do not</strong> sell or share your personal data with external organizations.  
        Information may only be shared internally for:
    </p>
    <ul>
        <li>Event management and coordination</li>
        <li>University administrative purposes</li>
        <li>Legal compliance when required</li>
    </ul>

    <h3>5. Cookies</h3>
    <p>
        The system may use cookies for login sessions and security.  
        These cookies do not store personal information.
    </p>

    <h3>6. Your Rights</h3>
    <p>You have the right to:</p>
    <ul>
        <li>Request correction of inaccurate data</li>
        <li>Request deletion of your account</li>
        <li>Access your personal information</li>
        <li>Withdraw consent at any time</li>
    </ul>

    <h3>7. Changes to This Policy</h3>
    <p>
        The Privacy Policy may be updated occasionally.  
        Continued use of the system means you accept the updated terms.
    </p>

    <h3>8. Contact Information</h3>
    <p>
        If you have any questions regarding this policy, please contact the University IT Support or Event Management Office.
    </p>

    <a href="homepage.php" class="back-btn">Home Page</a>
</div>

</body>
</html>
