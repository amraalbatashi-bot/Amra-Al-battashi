<?php
session_start();
include 'db.php';

$message = "";

/* ---- ADD EVENT ---- */
if (isset($_POST['add'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $date = $_POST['dateE'];
    $time = $_POST['timeE'];
    $end_time = $_POST['end_timeE'];
    $location = trim($_POST['location']);

    if ($title && $description && $date && $time && $end_time && $location) {
        $stmt = $conn->prepare("
            INSERT INTO EVENT (Title, Description, DateE, TimeE, End_TimeE, Location)
            VALUES (:title, :description, :date, :time, :end_time, :location)
        ");
        $stmt->execute([
            ':title' => $title,
            ':description' => $description,
            ':date' => $date,
            ':time' => $time,
            ':end_time' => $end_time,
            ':location' => $location
        ]);
        $message = "Event added successfully!";
    }
}

/* ---- UPDATE EVENT ---- */
if (isset($_POST['update'])) {
    $id = $_POST['event_id'];

    if ($id) {
        $stmt = $conn->prepare("
            UPDATE EVENT SET 
                Title=:title,
                Description=:description,
                DateE=:date,
                TimeE=:time,
                End_TimeE=:end_time,
                Location=:location
            WHERE Event_ID=:id
        ");
        $stmt->execute([
            ':title' => $_POST['title'],
            ':description' => $_POST['description'],
            ':date' => $_POST['dateE'],
            ':time' => $_POST['timeE'],
            ':end_time' => $_POST['end_timeE'],
            ':location' => $_POST['location'],
            ':id' => $id
        ]);
        $message = "Event updated successfully!";
    }
}

/* ---- DELETE EVENT ---- */
if (isset($_POST['delete'])) {
    $id = $_POST['event_id'];
    $conn->prepare("DELETE FROM EVENT WHERE Event_ID=:id")->execute([':id' => $id]);
    $message = "Event deleted successfully!";
}

/* ---- FETCH ALL EVENTS ---- */
$events = $conn->query("SELECT * FROM EVENT ORDER BY DateE ASC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin - Manage Events</title>

<style>
body {
    margin: 0;
    background: #f5f5f5;
    font-family: Arial, sans-serif;
}

/* Header */
.admin-header {
    background-color: #007BFF;
    padding: 15px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.admin-header img {
    height: 110px;
}
.admin-title {
    color: white;
    font-size: 26px;
    font-weight: bold;
    text-align: center;
    flex-grow: 1;
    margin-left: -80px;
}
.back-btn {
    background-color: #0056b3;
    color: white;
    padding: 10px 18px;
    text-decoration: none;
    border-radius: 6px;
    font-size: 16px;
    font-weight: bold;
}
.back-btn:hover {
    background-color: #003e80;
}

/* Container */
.container {
    width: 95%;
    max-width: 1200px;
    margin: 30px auto;
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
}

h3 {
    margin-bottom: 20px;
    color: #333;
}

/* Table */
table {
    width: 100%;
    border-collapse: collapse;
}
th {
    background-color: #007BFF;
    color: white;
    padding: 10px;
}
td {
    padding: 8px;
    border-bottom: 1px solid #ccc;
}
tr:hover {
    background: #f0f8ff;
}

input[type=text], input[type=date], input[type=time] {
    width: 100%;
    padding: 6px;
}

/* Buttons */
button {
    padding: 6px 12px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}
.add { background: #28a745; color:white; }
.update { background: #ffc107; color:white; }
.delete { background: #dc3545; color:white; }

.message { text-align:center; color:green; margin-bottom:15px; }
</style>
</head>

<body>

<!-- HEADER -->
<div class="admin-header">
    <img src="utas-hct.png" alt="Logo">
    <div class="admin-title">Admin – Manage Events</div>
    <a href="admin.php" class="back-btn">Back</a>
</div>

<!-- MAIN CONTENT -->
<div class="container">

<?php if ($message): ?>
    <div class="message"><?= $message ?></div>
<?php endif; ?>

<!-- ADD EVENT -->
<h3>Add New Event</h3>
<form method="POST">
    <input type="text" name="title" placeholder="Title" required><br><br>
    <input type="text" name="description" placeholder="Description" required><br><br>
    <input type="date" name="dateE" required>
    <input type="time" name="timeE" required>
    <input type="time" name="end_timeE" required><br><br>
    <input type="text" name="location" placeholder="Location" required><br><br>
    <button type="submit" name="add" class="add">Add Event</button>
</form>

<hr><br>

<!-- ALL EVENTS -->
<h3>All Events</h3>
<table>
<tr>
    <th>ID</th>
    <th>Title</th>
    <th>Description</th>
    <th>Date</th>
    <th>Start Time</th>
    <th>End Time</th>
    <th>Location</th>
    <th>Actions</th>
</tr>

<?php foreach ($events as $e): ?>
<tr>
<form method="POST">
    <td><?= $e['Event_ID'] ?></td>
    <td><input type="text" name="title" value="<?= htmlspecialchars($e['Title']) ?>"></td>
    <td><input type="text" name="description" value="<?= htmlspecialchars($e['Description']) ?>"></td>
    <td><input type="date" name="dateE" value="<?= $e['DateE'] ?>"></td>
    <td><input type="time" name="timeE" value="<?= $e['TimeE'] ?>"></td>
    <td><input type="time" name="end_timeE" value="<?= $e['End_TimeE'] ?>"></td>
    <td><input type="text" name="location" value="<?= htmlspecialchars($e['Location']) ?>"></td>

    <td>
        <input type="hidden" name="event_id" value="<?= $e['Event_ID'] ?>">
        <button type="submit" name="update" class="update">Update</button>
        <button type="submit" name="delete" class="delete" onclick="return confirm('Delete this event?')">Delete</button>
    </td>
</form>
</tr>
<?php endforeach; ?>
</table>

</div>
</body>
</html>
