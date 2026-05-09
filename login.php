<?php
session_start();
include 'db.php'; // Make sure db.php creates $conn (PDO)

$error_message = "";

// Check if form submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userid_input = $_POST['username'];
    $password_input = $_POST['password'];

    // Prepare statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM USERt WHERE User_ID = :userid OR Email = :userid LIMIT 1");
    $stmt->bindParam(':userid', $userid_input);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password_input, $user['Password'])) {
        $_SESSION['userid'] = $user['User_ID'];
        header("Location: event.php"); // redirect to events page
        exit;
    } else {
        $error_message = "Invalid Username/Password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Login Page</title>
    <style>
        /* existing styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .container-wrapper {
            position: relative;
            width: 100%;
            height: 100vh;
        }
        /* Removed top-left back button styling */

        .container {
            width: 300px;
            margin: 100px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #fff;
            position: relative;
        }
        .error {
            color: red;
            margin-bottom: 10px;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 8px;
            margin: 8px 0;
            box-sizing: border-box;
        }
        /* Style the login button to be blue */
        input[type="submit"] {
            width: 100%;
            padding: 8px;
            background-color: #007BFF;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
            border-radius: 4px;
        }
        input[type="submit"]:hover {
            background-color: #0069d9;
        }
        /* Style for the back button after login button */
        .post-login-back {
            display: block;
            margin-top: 10px;
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            border-radius: 4px;
            text-align: center;
            text-decoration: none;
            font-weight: bold;
            font-size: 14px;
            transition: background-color 0.3s;
        }
        .post-login-back:hover {
            background-color: #0069d9;
        }
        p {
            margin-top: 10px;
        }
        a {
            color: blue;
            text-decoration: none;
        }
    </style>
</head>
<body>
<div class="container-wrapper">
    <div class="container">
        <h2>Login</h2>

        <?php
        if (!empty($error_message)) {
            echo "<div class='error'>{$error_message}</div>";
            echo "<script>alert('Invalid Username/Password. Would you like to register now?'); window.location.href='register.php';</script>";
        }
        ?>
        <form method="post" action="">
            <label for="username">Username or Email:</label><br>
            <input type="text" id="username" name="username" placeholder="Enter Username or Email" required><br>
            <label for="password">Password:</label><br>
            <input type="password" id="password" name="password" placeholder="Enter Password" required><br><br>
            <input type="submit" value="Login">
        </form>
        <!-- Back to Home button after login button -->
        <a href="homePage.php" class="post-login-back">Back to Home</a>
        <!-- "Don't have an account?" paragraph -->
        <p>Don’t have an account? <a href="register.php">Register here</a></p>
    </div>
</div>
</body>
</html>