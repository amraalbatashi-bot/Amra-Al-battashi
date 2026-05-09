<?php
session_start();
include 'db.php';

// CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(24));
}
$csrf = $_SESSION['csrf_token'];

$messages = [];
function msg($type, $text){
    global $messages;
    $messages[] = ['type'=>$type, 'text'=>$text];
}
function show_msgs(){
    global $messages;
    if (!$messages) return "";
    $o = "<div class='msgs'>";
    foreach ($messages as $m){
        $cls = $m['type'] === 'success' ? 'msg-success' :
               ($m['type'] === 'error' ? 'msg-error' : 'msg-info');
        $o .= "<div class='$cls'>".htmlspecialchars($m['text'])."</div>";
    }
    return $o."</div>";
}
function esc($v){ return htmlspecialchars($v ?? "", ENT_QUOTES, "UTF-8"); }

// ---- POST ACTIONS ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($csrf, $_POST['csrf'] ?? "")) {
        msg("error", "Invalid request token.");
    } else {
        $action = $_POST['action'] ?? "";

        // ADD
        if ($action === "add") {
            $event_id = intval($_POST['event_id']);
            $name = trim($_POST['resource_name']);

            if ($event_id <= 0) msg("error","Select a valid event.");
            elseif ($name === "") msg("error","Resource name is required.");
            else {
                $check = $conn->prepare("SELECT COUNT(*) FROM RESOURCES WHERE Event_ID=? AND Resource_Name=?");
                $check->execute([$event_id,$name]);

                if ($check->fetchColumn() > 0) {
                    msg("info","Resource already exists for this event.");
                } else {
                    $add = $conn->prepare("INSERT INTO RESOURCES (Event_ID, Resource_Name) VALUES (?,?)");
                    $add->execute([$event_id,$name]);
                    msg("success","Resource added.");
                }
            }
        }

        // DELETE
        if ($action === "delete") {
            $rid = intval($_POST['resource_id']);
            if ($rid <= 0) msg("error","Invalid deletion.");
            else {
                $del = $conn->prepare("DELETE FROM RESOURCES WHERE Resource_ID=?");
                $del->execute([$rid]);
                msg("success","Resource deleted.");
            }
        }

        // UPDATE
        if ($action === "update") {
            $rid = intval($_POST['resource_id']);
            $eid = intval($_POST['event_id']);
            $name = trim($_POST['resource_name']);

            if ($rid <= 0 || $eid <= 0 || $name === "") {
                msg("error","Fill all update fields.");
            } else {
                $up = $conn->prepare("UPDATE RESOURCES SET Resource_Name=?, Event_ID=? WHERE Resource_ID=?");
                $up->execute([$name,$eid,$rid]);
                msg("success", "Resource updated.");
            }
        }
    }
}

// ---- FETCH EVENTS (ORDERED) ----
$events = $conn->query("SELECT Event_ID, Title, DateE FROM EVENT ORDER BY Event_ID ASC")->fetchAll(PDO::FETCH_ASSOC);

// ---- FETCH RESOURCES ORDERED BY EVENT ASC ----
$sql = "SELECT r.Resource_ID, r.Resource_Name, r.Event_ID, e.Title, e.DateE
        FROM RESOURCES r
        JOIN EVENT e ON r.Event_ID = e.Event_ID
        ORDER BY r.Event_ID ASC, r.Resource_ID ASC";
$resources = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Manage Resources</title>
<style>
body {
    margin:0;
    font-family:Arial, sans-serif;
    background:#f4f6fa;
}

/* HEADER */
.header {
    background:#0056b3;
    color:#fff;
    padding:14px 20px;
    display:flex;
    align-items:center;
    justify-content:space-between;
}
.header img {
    height:60px;
    background:#fff;
    padding:6px;
    border-radius:8px;
}
.header h1 {
    flex:1;
    text-align:center;
    margin:0;
    font-size:22px;
}
.header a {
    color:#0056b3;
    background:#fff;
    padding:8px 14px;
    text-decoration:none;
    border-radius:6px;
    font-weight:bold;
}

/* LAYOUT */
.container {
    max-width:1100px;
    margin:25px auto;
    background:#fff;
    padding:25px;
    border-radius:12px;
    box-shadow:0 0 10px rgba(0,0,0,0.08);
}

/* MESSAGES */
.msgs { margin-bottom:15px; }
.msg-success, .msg-error, .msg-info {
    padding:10px;
    border-radius:6px;
    margin-bottom:8px;
}
.msg-success { background:#e7fbe9; border-left:4px solid #2ecc71; }
.msg-error   { background:#fde8e8; border-left:4px solid #e74c3c; }
.msg-info    { background:#fff4d6; border-left:4px solid #f1c40f; }

/* FORM */
input, select {
    width:100%;
    padding:10px;
    margin-top:6px;
    margin-bottom:12px;
    border:1px solid #ccc;
    border-radius:6px;
}
button {
    padding:10px 16px;
    background:#0056b3;
    color:#fff;
    border:none;
    border-radius:6px;
    cursor:pointer;
    font-weight:bold;
}
button.danger { background:#c62828; }
button.ghost {
    background:#fff;
    color:#0056b3;
    border:1px solid #0056b3;
}

/* TABLE */
table {
    width:100%;
    border-collapse:collapse;
    text-align:center;
    margin-top:25px;
}
th {
    background:#0056b3;
    color:white;
    padding:12px;
    text-align:center;
}
td {
    padding:12px;
    border-bottom:1px solid #e5e5e5;
    text-align:center;
}
tr:hover { background:#f3f9ff; }

details {
    margin-top:5px;
}
details summary {
    list-style:none;
    cursor:pointer;
    padding:6px 10px;
    border-radius:6px;
    border:1px solid #0056b3;
    color:#0056b3;
    width:80px;
    text-align:center;
}
</style>
</head>
<body>

<!-- HEADER -->
<div class="header">
    <img src="utas-hct.png" alt="logo">
    <h1>Manage Resources</h1>
    <a href="admin.php">Back</a>
</div>

<div class="container">

<?= show_msgs() ?>

<h2>Add Resource</h2>
<form method="POST">
    <input type="hidden" name="csrf" value="<?= esc($csrf) ?>">
    <input type="hidden" name="action" value="add">

    <label>Event</label>
    <select name="event_id" required>
        <option value="">-- Select Event --</option>
        <?php foreach($events as $ev): ?>
            <option value="<?= $ev['Event_ID'] ?>">
                <?= $ev['Event_ID'] ?> — <?= esc($ev['Title']) ?> (<?= esc($ev['DateE']) ?>)
            </option>
        <?php endforeach; ?>
    </select>

    <label>Resource Name</label>
    <input type="text" name="resource_name" placeholder="e.g., Projector" required>

    <button type="submit">Add Resource</button>
</form>

<h2 style="margin-top:35px;">Resources List (<?= count($resources) ?>)</h2>

<table>
    <tr>
        <th>#</th>
        <th>Event ID</th>
        <th>Event Title</th>
        <th>Resource</th>
        <th>Actions</th>
    </tr>

    <?php foreach($resources as $i=>$r): ?>
    <tr>
        <td><?= $i+1 ?></td>
        <td><?= $r['Event_ID'] ?></td>
        <td><?= esc($r['Title']) ?></td>
        <td><?= esc($r['Resource_Name']) ?></td>

        <td>

            <!-- EDIT -->
            <details>
                <summary>Edit</summary>
                <div style="background:#f9f9f9;padding:10px;border-radius:6px;border:1px solid #ccc;margin-top:6px;">
                    <form method="POST">
                        <input type="hidden" name="csrf" value="<?= esc($csrf) ?>">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="resource_id" value="<?= $r['Resource_ID'] ?>">

                        <select name="event_id" required>
                            <?php foreach($events as $ev): ?>
                                <option value="<?= $ev['Event_ID'] ?>" <?= $ev['Event_ID']==$r['Event_ID']?'selected':'' ?>>
                                    <?= $ev['Event_ID'] ?> — <?= esc($ev['Title']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <input type="text" name="resource_name" value="<?= esc($r['Resource_Name']) ?>" required>

                        <button type="submit">Save</button>
                    </form>
                </div>
            </details>

            <!-- DELETE -->
            <form method="POST" style="display:inline-block;margin-top:5px;" onsubmit="return confirm('Delete this resource?');">
                <input type="hidden" name="csrf" value="<?= esc($csrf) ?>">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="resource_id" value="<?= $r['Resource_ID'] ?>">
                <button class="danger">Delete</button>
            </form>

        </td>
    </tr>
    <?php endforeach; ?>
</table>

</div>

</body>
</html>
