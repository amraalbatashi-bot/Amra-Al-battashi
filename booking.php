<?php
session_start();
include 'db.php';

if(!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit;
}

$event_id = $_GET['event_id'];
$user_id = $_SESSION['userid'];

$stmt = $conn->prepare("INSERT INTO BOOKING (User_ID, Event_ID, Booking_Date) VALUES (:uid, :eid, NOW())");
$stmt->bindParam(':uid', $user_id);
$stmt->bindParam(':eid', $event_id);
$stmt->execute();

header("Location: userinfo.php");
?>
