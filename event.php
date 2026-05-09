<?php
include 'db.php';

// Fetch all events from the database
$query = $conn->prepare("
    SELECT Event_ID, Title, Description, Event_Type, Location, DateE, TimeE, End_TimeE FROM EVENT
");
$query->execute();
$events = $query->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Events</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #007BFF;
            color: white;
            padding: 15px 20px;
            display: flex;
            align-items: center;
            position: relative;
        }

        header img {
            height: 120px;
            width: auto;
        }

        header h1 {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            margin: 0;
            font-size: 26px;
        }

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

        .container {
            padding: 40px 20px;
            max-width: 1000px;
            margin: 0 auto;
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }

        #searchInput {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        /* Event cards layout */
        .event-list {
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
            font-size: 18px;
            font-weight: bold;
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
    </style>

</head>
<body>

<header>
    <img src="utas-hct.png" alt="University Logo">
    <h1>Event Management System</h1>
</header>

<nav>
    <a href="homepage.php">Home</a>
    <a href="Aboutus.php">About Us</a> <!-- Added About Us -->
    <a href="userinfo.php">Booking</a>
    <a href="logout.php">Logout</a>
</nav>

<div class="container">
    <h2>Explore the Events</h2> <!-- Updated text -->

    <!-- Search box -->
    <input type="text" id="searchInput" placeholder="Search for an event...">

    <!-- Event cards list -->
    <div class="event-list" id="eventList">

        <?php foreach ($events as $event): ?>
            <div class="event-card">

                <!-- Title -->
                <div class="event-title">
                    <?php echo htmlspecialchars($event['Title']); ?>
                </div>

                <!-- Date and Location -->
                <div class="event-info">
                    📅 <?php echo htmlspecialchars($event['DateE']); ?> |
                    ⏰ <?php echo htmlspecialchars($event['TimeE']); ?> - <?php echo htmlspecialchars($event['End_TimeE']); ?> |
                    📍 <?php echo htmlspecialchars($event['Location']); ?>
                </div>

                <!-- Description -->
                <div class="event-description">
                    <?php echo htmlspecialchars($event['Description']); ?>
                </div>

                <!-- View details button -->
                <a href="userinfo.php?id=<?php echo $event['Event_ID']; ?>" class="view-btn">
                    Booking
                </a>

            </div>
        <?php endforeach; ?>

    </div>
</div>

<script>
// Search filter JS
const searchInput = document.getElementById('searchInput');
const eventList = document.getElementById('eventList');
const events = eventList.getElementsByClassName('event-card');

searchInput.addEventListener('keyup', function() {
    const filter = searchInput.value.toLowerCase();
    Array.from(events).forEach(function(eventCard) {
        const title = eventCard.getElementsByClassName('event-title')[0].innerText.toLowerCase();
        eventCard.style.display = title.includes(filter) ? '' : 'none';
    });
});
</script>

</body>
</html>
