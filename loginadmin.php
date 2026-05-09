<?php
session_start();
include 'db.php'; // Make sure this contains your PDO connection as $conn

$error_message = "";

// If form submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $adminid = trim($_POST['adminname']);
    $password = trim($_POST['password']);

    // Fetch admin by ID or Email
    $stmt = $conn->prepare("SELECT * FROM ADMINt WHERE Admin_ID = :adminid OR Email = :adminid LIMIT 1");
    $stmt->bindParam(':adminid', $adminid);
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin) {
        // Plain text password check (for now)
        if ($password === $admin['Password']) {
            $_SESSION['adminid'] = $admin['Admin_ID'];
            $_SESSION['adminname'] = $admin['Admin_Name'];
            header("Location: admin.php");
            exit;
        } 
    }
    $error_message = "Invalid username/email or password.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Admin Login</title>
<style>
body { font-family: Arial; background:#f5f5f5; display:flex; justify-content:center; align-items:center; height:100vh; }
.container { background:white; padding:30px 40px; border-radius:10px; box-shadow:0 0 15px rgba(0,0,0,0.1); width:350px; }
h2 { text-align:center; margin-bottom:20px; color:#333; }
label { display:block; margin-bottom:5px; font-weight:bold; }
input { width:100%; padding:10px; margin-bottom:15px; border:1px solid #ccc; border-radius:5px; }
input:focus { border-color:#007BFF; }
button { width:100%; padding:10px; background:#007BFF; color:white; border:none; border-radius:5px; cursor:pointer; }
button:hover { background:#0056b3; }
.error { color:red; text-align:center; margin-bottom:10px; }
</style>
</head>
<body>
<div class="container">
    <h2>Admin Login</h2>
    <?php if($error_message) echo "<div class='error'>$error_message</div>"; ?>
    <form method="post">
        <label>Username or Email</label>
        <input type="text" name="adminname" placeholder="Enter ID or Email" required>

        <label>Password</label>
        <input type="password" name="password" placeholder="Enter password" required>

        <button type="submit">Login</button>
    </form>
    <!-- Back to Home button -->
    <a href="homePage.php" class="back-home-button">Back to Home</a>
</div>
<style>
/* Style for the Back to Home button similar to previous example */
.back-home-button {
    display: block;
    margin-top: 15px;
    padding: 10px;
    background-color: #007BFF;
    color: white;
    border-radius: 5px;
    text-align: center;
    text-decoration: none;
    font-weight: bold;
    font-size: 14px;
    transition: background-color 0.3s;
}
.back-home-button:hover {
    background-color: #0069d9;
}
</style>
</body>
</html>