<?php
include 'db.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm'];

    // Validate username: first 2 are digits, third is J/j/S/s, rest can be anything
    if (!preg_match('/^\d{2}[JjSs].*$/', $username)) {
        $message = "Username must start with 2 digits, then J/j/S/s.";
    }
    // Validate phone: must be 8 digits starting with 7 or 9
    elseif (!preg_match('/^[79]\d{7}$/', $phone)) {
        $message = "Phone number must be 8 digits and start with 7 or 9.";
    }
    // Check if passwords match
    elseif ($password !== $confirm) {
        $message = "Passwords do not match.";
    }
    // Check password strength server-side
    elseif (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/', $password)) {
        $message = "Password must be at least 8 characters and include letters, numbers, and symbols.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO USERt (Fname, User_ID, Email, Password, Phone) VALUES (:fname, :userid, :email, :password, :phone)");
        $stmt->bindParam(':fname', $name);
        $stmt->bindParam(':userid', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':phone', $phone);

        try {
            $stmt->execute();
            // Redirect to login.php with success message
            header('Location: login.php?registered=1');
            exit();
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) $message = "Username, email, or phone already exists.";
            else $message = "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Registration Page</title>
<style>
body {
    font-family: Arial, sans-serif;
    background-color: #f5f5f5;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}
.container {
    background-color: white;
    padding: 30px 40px;
    border-radius: 10px;
    box-shadow: 0 0 15px rgba(0,0,0,0.1);
    width: 350px;
}
h2 {
    text-align: center;
    margin-bottom: 25px;
    color: #333;
}
label { display: block; margin-bottom: 5px; color: #555; font-weight: bold; }
input {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 5px;
    outline: none;
    font-size: 14px;
}
input:focus { border-color: #007BFF; }
button {
    width: 100%;
    padding: 10px;
    background-color: #007BFF;
    color: white;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    cursor: pointer;
}
button:hover { background-color: #0056b3; }
p { text-align: center; margin-top: 10px; }
a { color: #007BFF; text-decoration: none; }
a:hover { text-decoration: underline; }
.message { text-align: center; margin-bottom: 15px; color: red; }
/* Style for the Back to Home button inside the form */
.back-home-button {
    display: block;
    margin: 20px auto 0 auto;
    padding: 10px 20px;
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
</head>
<body>
<div class="container">
<h2>Registration Form</h2>
<?php if (!empty($message)) echo "<div class='message'>{$message}</div>"; ?>
<form action="" method="post" id="registerForm">
<label for="name">First Name</label>
<input type="text" id="name" name="name" placeholder="Enter your first name" required>

<label for="email">Email</label>
<input type="email" id="email" name="email" placeholder="Enter your email" required>

<label for="username">Username</label>
<input type="text" id="username" name="username" placeholder="Enter your username" required pattern="^\d{2}[JjSs].*$" title="Must start with 2 digits, then J/j/S/s">

<label for="phone">Phone</label>
<input type="text" id="phone" name="phone" placeholder="Enter phone" required pattern="^[79]\d{7}$" title="Phone number must be 8 digits and start with 7 or 9">

<label for="password">Password</label>
<input type="password" id="password" name="password" placeholder="Enter password" required>

<label for="confirm">Confirm Password</label>
<input type="password" id="confirm" name="confirm" placeholder="Re-enter password" required>

<button type="submit">Register</button>
<!-- Back to Home button placed immediately after the Register button inside the form -->
<a href="homePage.php" class="back-home-button">Back to Home</a>
</form>
<p>Already have an account? <a href="login.php">Login here</a></p>
</div>

<!-- JavaScript for client-side password validation -->
<script>
document.getElementById('registerForm').addEventListener('submit', function(e) {
    var password = document.getElementById('password').value.trim();
    var pattern = /^(?=.*[A-Za-z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/;
    if (!pattern.test(password)) {
        alert('Password must be at least 8 characters and include letters, numbers, and symbols.');
        e.preventDefault();
    }
});
</script>
</body>
</html>