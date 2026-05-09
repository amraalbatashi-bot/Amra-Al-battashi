<?php
session_start();
include 'db.php';

// Fetch all feedback
$feedbacks = $conn->query("
    SELECT Feedback_ID, Comments, Rating
    FROM FEEDBACK
    ORDER BY Feedback_ID DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin - Feedback</title>

<style>
body {
    margin: 0;
    background: #eef2f5;
    font-family: Arial, sans-serif;
}

/* Header */
.admin-header {
    background-color: #007BFF;
    padding: 15px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.admin-header img {
    height: 110px;
}
.admin-title {
    color: white;
    font-size: 28px;
    font-weight: bold;
    flex-grow: 1;
    text-align: center;
    margin-left: -80px;
}
.back-btn {
    background-color: #0056b3;
    color: white;
    padding: 10px 18px;
    text-decoration: none;
    font-weight: bold;
    border-radius: 6px;
    transition: 0.2s;
}
.back-btn:hover {
    background-color: #003e80;
}

/* Container */
.container {
    width: 90%;
    max-width: 1100px;
    margin: 35px auto;
    background: white;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.1);
}
h3 {
    text-align: center;
    font-size: 24px;
    color: #333;
    margin-bottom: 25px;
}

/* Modern Table */
table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0 10px;
}

thead th {
    background-color: #007BFF;
    color: white;
    padding: 14px;
    text-align: left;
    border-radius: 8px 8px 0 0;
    font-size: 16px;
}

tbody tr {
    background: #ffffff;
    box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    border-radius: 8px;
}

tbody td {
    padding: 12px 14px;
    font-size: 15px;
}

tbody tr:hover {
    background-color: #f5f9ff;
    cursor: pointer;
}
</style>

</head>
<body>

<!-- HEADER -->
<div class="admin-header">
    <img src="utas-hct.png" alt="University Logo">
    <div class="admin-title">Admin – View Feedback</div>
    <a href="admin.php" class="back-btn">Back</a>
</div>

<!-- CONTENT -->
<div class="container">
    <h3>All Feedback</h3>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Comments</th>
                <th>Rating</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($feedbacks as $fb): ?>
            <tr>
                <td><?= $fb['Feedback_ID'] ?></td>
                <td><?= htmlspecialchars($fb['Comments']) ?></td>
                <td><?= htmlspecialchars($fb['Rating']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>
