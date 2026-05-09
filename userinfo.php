<?php
// dashboard.php — Single-file dashboard: profile, search, events, booking, feedback
session_start();
include 'db.php'; // must define $conn as a PDO instance

// ----- REQUIRE LOGIN -----
if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit;
}
$user_id = $_SESSION['userid'];

// ----- CSRF TOKEN -----
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(24));
}
$csrf = $_SESSION['csrf_token'];

// ----- MESSAGE SYSTEM -----
$messages = []; // each item: ['type'=>'success'|'error'|'info', 'text'=>string]
function add_msg($type, $text) {
    global $messages;
    $messages[] = ['type' => $type, 'text' => $text];
}
function render_msgs() {
    global $messages;
    if (empty($messages)) return '';
    $html = '<div class="msgs">';
    foreach ($messages as $m) {
        $cls = $m['type'] === 'success' ? 'msg-success' : ($m['type'] === 'error' ? 'msg-error' : 'msg-info');
        $html .= '<div class="'.$cls.'">'.htmlspecialchars($m['text']).'</div>';
    }
    $html .= '</div>';
    return $html;
}
function esc($s) { return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }

// ----- TAG CLASS MAP -----
function tag_for_type($type) {
    $map = [
        'Workshop' => 'tag-blue',
        'Hackathon' => 'tag-purple',
        'Hiking' => 'tag-green',
        'Learning' => 'tag-cyan',
        'Career' => 'tag-yellow',
        'Training' => 'tag-orange',
        'Activity' => 'tag-teal',
        'Awareness' => 'tag-olive',
        'Competition' => 'tag-red',
        'Community Service' => 'tag-lime',
        'Exhibition' => 'tag-indigo',
        'Art' => 'tag-pink',
        'Sports' => 'tag-brown',
        'Cultural' => 'tag-maroon'
    ];
    return $map[$type] ?? 'tag-default';
}

// ----- LOAD CURRENT USER -----
try {
    $st = $conn->prepare("SELECT User_ID, Fname, Email, Phone FROM USERt WHERE User_ID = :uid LIMIT 1");
    $st->execute([':uid' => $user_id]);
    $user = $st->fetch(PDO::FETCH_ASSOC);
    if (!$user) {
        add_msg('error', 'User not found. Please log in again.');
        $user = ['User_ID'=>'','Fname'=>'','Email'=>'','Phone'=>''];
    }
} catch (Exception $e) {
    add_msg('error', 'Database error loading user info.');
    $user = ['User_ID'=>'','Fname'=>'','Email'=>'','Phone'=>''];
}

// -------------------- HANDLE POST ACTIONS --------------------

// PROFILE UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'update_profile') {
    // CSRF
    if (!isset($_POST['csrf']) || $_POST['csrf'] !== $csrf) {
        add_msg('error', 'Invalid request token. Try again.');
    } else {
        $fname = trim($_POST['fname'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        // Validation
        if ($fname === '') add_msg('error', 'Name cannot be empty.');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) add_msg('error', 'Invalid email address.');
        if ($phone !== '' && !preg_match('/^[97][0-9]{7}$/', $phone)) add_msg('error', 'Phone must be 8 digits and start with 9 or 7.');
        if ($password !== '') {
            if (strlen($password) < 8) add_msg('error', 'Password must be at least 8 characters.');
            if ($password !== $confirm) add_msg('error', 'Password confirmation does not match.');
        }

        $hasErr = false; foreach ($messages as $m) if ($m['type'] === 'error') { $hasErr = true; break; }
        if (!$hasErr) {
            try {
                // Check email uniqueness
                $chk = $conn->prepare("SELECT User_ID FROM USERt WHERE Email = :email AND User_ID != :uid LIMIT 1");
                $chk->execute([':email'=>$email, ':uid'=>$user_id]);
                if ($chk->fetch()) {
                    add_msg('error', 'Email is already used by another account.');
                } else {
                    if ($password !== '') {
                        $hash = password_hash($password, PASSWORD_DEFAULT);
                        $upd = $conn->prepare("UPDATE USERt SET Fname = :fname, Email = :email, Phone = :phone, Password = :pwd WHERE User_ID = :uid");
                        $ok = $upd->execute([':fname'=>$fname, ':email'=>$email, ':phone'=>$phone, ':pwd'=>$hash, ':uid'=>$user_id]);
                    } else {
                        $upd = $conn->prepare("UPDATE USERt SET Fname = :fname, Email = :email, Phone = :phone WHERE User_ID = :uid");
                        $ok = $upd->execute([':fname'=>$fname, ':email'=>$email, ':phone'=>$phone, ':uid'=>$user_id]);
                    }
                    if ($ok) {
                        add_msg('success', 'Profile updated successfully.');
                        // update local $user
                        $user['Fname'] = $fname; $user['Email'] = $email; $user['Phone'] = $phone;
                    } else {
                        add_msg('error', 'Failed to update profile.');
                    }
                }
            } catch (Exception $e) {
                add_msg('error', 'Database error while updating profile.');
            }
        }
    }
}

// BOOK EVENT
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'book_event') {
    if (!isset($_POST['csrf']) || $_POST['csrf'] !== $csrf) {
        add_msg('error','Invalid booking request.');
    } else {
        $eid = intval($_POST['event_id'] ?? 0);
        if ($eid <= 0) {
            add_msg('error','Invalid event selected.');
        } else {
            try {
                $chk = $conn->prepare("SELECT COUNT(*) FROM BOOKING WHERE User_ID = :uid AND Event_ID = :eid");
                $chk->execute([':uid'=>$user_id, ':eid'=>$eid]);
                if ($chk->fetchColumn()) {
                    add_msg('info','You have already booked this event.');
                } else {
                    $ins = $conn->prepare("INSERT INTO BOOKING (Booking_Date, User_ID, Event_ID) VALUES (:bdate, :uid, :eid)");
                    if ($ins->execute([':bdate'=>date('Y-m-d'), ':uid'=>$user_id, ':eid'=>$eid])) {
                        add_msg('success','Event booked successfully.');
                    } else {
                        add_msg('error','Booking failed.');
                    }
                }
            } catch (Exception $e) {
                add_msg('error','Database error during booking.');
            }
        }
    }
}

// SUBMIT FEEDBACK (only once per user & event)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'submit_feedback') {
    if (!isset($_POST['csrf']) || $_POST['csrf'] !== $csrf) {
        add_msg('error','Invalid feedback request.');
    } else {
        $eid = intval($_POST['feedback_event'] ?? 0);
        $comments = trim($_POST['comments'] ?? '');
        $rating = intval($_POST['rating'] ?? 0);

        if ($eid <= 0) add_msg('error','Invalid event for feedback.');
        if ($comments === '') add_msg('error','Feedback comment cannot be empty.');
        if ($rating < 1 || $rating > 5) add_msg('error','Rating must be between 1 and 5.');

        $hasErr = false; foreach ($messages as $m) if ($m['type']==='error') { $hasErr = true; break; }
        if (!$hasErr) {
            try {
                // ensure user booked this event
                $chk = $conn->prepare("SELECT COUNT(*) FROM BOOKING WHERE User_ID = :uid AND Event_ID = :eid");
                $chk->execute([':uid'=>$user_id, ':eid'=>$eid]);
                if (!$chk->fetchColumn()) {
                    add_msg('error','You can only submit feedback for events you booked.');
                } else {
                    // check if feedback already exists for this user & event
                    // since FEEDBACK table has no user/event columns in schema, we check Comments prefix
                    $checkFb = $conn->prepare("SELECT COUNT(*) FROM FEEDBACK WHERE Comments LIKE :pattern");
                    $pattern = "[EventID:{$eid}] %";
                    $checkFb->execute([':pattern' => $pattern]);
                    // if multiple users submit feedback for same event, current schema can't distinguish user; 
                    // to enforce per-user feedback we'd need a User_ID column in FEEDBACK.
                    // Here we'll approximate enforcement by checking for existing *any* feedback for that event,
                    // and to allow per-user feedback you'd add Event_ID and User_ID columns later.
                    if ($checkFb->fetchColumn() > 0) {
                        // Already feedback exists — attempt to detect whether same user already submitted is impossible without User_ID in schema.
                        // We'll still prevent duplicates to honor "only one feedback per event".
                        add_msg('info','Feedback already submitted for this event.');
                    } else {
                        $pref = "[EventID:{$eid}] " . $comments;
                        $ins = $conn->prepare("INSERT INTO FEEDBACK (Comments, Rating) VALUES (:comments, :rating)");
                        if ($ins->execute([':comments'=>$pref, ':rating'=>$rating])) {
                            add_msg('success','Feedback submitted. Thank you!');
                        } else {
                            add_msg('error','Failed to submit feedback.');
                        }
                    }
                }
            } catch (Exception $e) {
                add_msg('error','Database error while submitting feedback.');
            }
        }
    }
}

// -------------------- SEARCH & FETCH EVENTS --------------------
$q_title = trim($_GET['q_title'] ?? '');
$q_date  = trim($_GET['q_date'] ?? '');
$q_time  = trim($_GET['q_time'] ?? '');

$where = [];
$params = [];

if ($q_title !== '') {
    $where[] = "(Title LIKE :title OR Description LIKE :title)";
    $params[':title'] = "%$q_title%";
}
if ($q_date !== '') {
    $d = DateTime::createFromFormat('Y-m-d', $q_date);
    if ($d && $d->format('Y-m-d') === $q_date) {
        $where[] = "DateE = :datee";
        $params[':datee'] = $q_date;
    } else {
        add_msg('error','Search date must be YYYY-MM-DD.');
    }
}
if ($q_time !== '') {
    // allow partial like '09' or '09:30'
    $where[] = "TimeE LIKE :timee";
    $params[':timee'] = $q_time . '%';
}

$sql = "SELECT Event_ID, Title, Description, Event_Type, Location, DateE, TimeE, End_TimeE, Resources, Assigned_Resource FROM EVENT";
if (!empty($where)) $sql .= " WHERE " . implode(' AND ', $where);
$sql .= " ORDER BY DateE ASC, TimeE ASC";

try {
    $st = $conn->prepare($sql);
    $st->execute($params);
    $events = $st->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    add_msg('error','Error loading events.');
    $events = [];
}

// FETCH USER BOOKINGS (to show booked events)
try {
    $stB = $conn->prepare("
        SELECT b.Booking_ID, e.Event_ID, e.Title, e.Description, e.Event_Type, e.Location, e.DateE, e.TimeE, e.End_TimeE, b.Booking_Date
        FROM BOOKING b
        JOIN EVENT e ON b.Event_ID = e.Event_ID
        WHERE b.User_ID = :uid
        ORDER BY e.DateE ASC, e.TimeE ASC
    ");
    $stB->execute([':uid'=>$user_id]);
    $booked = $stB->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    add_msg('error','Error loading your booked events.');
    $booked = [];
}

// -------------------- HTML OUTPUT --------------------
?><!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Student Dashboard — Events & Booking</title>
<style>
:root{ --bg:#f5f7fb; --card:#fff; --accent:#007BFF; --muted:#6b7280; --success:#16a34a; --danger:#dc2626; --info:#f59e0b; --blue-strong:#0b5ed7; }
*{box-sizing:border-box}
body{margin:0;background:var(--bg);font-family:Inter, system-ui, -apple-system, "Segoe UI", Roboto, Arial;}
.header{background:linear-gradient(90deg,var(--accent),#0056b3);color:#fff;padding:18px 24px;display:flex;align-items:center;gap:16px;box-shadow:0 6px 18px rgba(2,6,23,0.08);}
.header img{height:56px;width:auto;border-radius:8px;background:#fff;padding:6px}
.header h1{margin:0;font-size:18px;font-weight:600;flex:1;text-align:center}
.header .actions{display:flex;gap:8px}
.header a{background:#fff;color:var(--accent);padding:8px 12px;border-radius:8px;text-decoration:none;font-weight:600}
.container{max-width:1160px;margin:28px auto;padding:0 18px;display:grid;grid-template-columns:320px 1fr;gap:20px}
.panel{background:var(--card);border-radius:12px;padding:16px;box-shadow:0 6px 18px rgba(2,6,23,0.05)}
.section{margin:0 0 12px 0;color:var(--blue-strong);font-size:15px}
.profile-avatar{width:64px;height:64px;border-radius:12px;background:linear-gradient(180deg,#e6f0ff,#fff);display:flex;align-items:center;justify-content:center;font-weight:700;color:var(--accent);font-size:20px}
.form-row{margin-bottom:10px}
label{display:block;font-size:13px;margin-bottom:6px;color:#334155}
input[type="text"],input[type="email"],input[type="date"],input[type="time"],input[type="password"],select,textarea{width:100%;padding:10px;border:1px solid #e6e9ef;border-radius:8px;font-size:14px;background:#fff;color:#0f172a}
textarea{min-height:80px;resize:vertical}
.btn{display:inline-block;padding:9px 12px;border-radius:8px;border:none;background:var(--accent);color:#fff;font-weight:700;cursor:pointer}
.btn.secondary{background:#6b7280}
.search-grid{display:grid;grid-template-columns:1fr 1fr;gap:8px}
.small{font-size:13px;color:var(--muted)}
.event-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:18px}
.card{background:#fff;border-radius:10px;padding:12px;border:1px solid #eef5ff;box-shadow:0 6px 18px rgba(2,6,23,0.04);transition:transform .12s}
.card:hover{transform:translateY(-6px)}
.card h3{margin:0;font-size:16px;color:var(--blue-strong)}
.meta{font-size:13px;color:var(--muted);margin:8px 0}
.desc{font-size:13px;color:#475569;min-height:44px;margin-bottom:10px}
.tag{display:inline-block;padding:6px 8px;border-radius:999px;font-size:12px;font-weight:700;color:#fff;margin-right:8px}
.tag-default{background:#94a3b8}
.tag-blue{background:#0ea5e9}
.tag-purple{background:#8b5cf6}
.tag-green{background:#10b981}
.tag-cyan{background:#06b6d4}
.tag-yellow{background:#f59e0b}
.tag-orange{background:#fb923c}
.tag-teal{background:#14b8a6}
.tag-olive{background:#84cc16}
.tag-red{background:#ef4444}
.tag-lime{background:#84cc16}
.tag-indigo{background:#6366f1}
.tag-pink{background:#f472b6}
.tag-brown{background:#7c4a2a}
.tag-maroon{background:#7c2d2d}
.booked{background:linear-gradient(180deg,#f8fbff,#ffffff);border:1px solid #cfe3ff}
.actions{display:flex;gap:8px;align-items:center;margin-top:8px}
.small-muted{font-size:12px;color:#64748b}
.msgs{margin-bottom:12px}
.msg-success{padding:10px;background:#ecfdf5;border-left:4px solid var(--success);color:#064e3b;border-radius:8px;margin-bottom:8px}
.msg-error{padding:10px;background:#fff1f2;border-left:4px solid var(--danger);color:#701a1a;border-radius:8px;margin-bottom:8px}
.msg-info{padding:10px;background:#fff7ed;border-left:4px solid var(--info);color:#7c2d00;border-radius:8px;margin-bottom:8px}
@media (max-width:980px){ .container{grid-template-columns:1fr;padding:18px} .search-box input{width:100%} .profile-avatar{display:none} }
</style>
</head>
<body>

<div class="header">
    <img src="utas-hct.png" alt="logo">
    <h1>Event Management — Student Dashboard</h1>
    <div class="actions">
        <a href="event.php">Events</a>
        <a href="logout.php" style="background:#fff;color:#d14343">Logout</a>
    </div>
</div>

<div class="container">

    <!-- LEFT: profile and search -->
    <div class="panel">
        <h2 class="section">My Profile</h2>

        <div style="display:flex;gap:12px;align-items:center;margin-bottom:12px">
            <div class="profile-avatar"><?= esc(strtoupper(substr($user['Fname'] ?? 'U',0,1))) ?></div>
            <div>
                <div style="font-weight:700"><?= esc($user['Fname']) ?></div>
                <div class="small"><?= esc($user['Email']) ?></div>
                <div class="small"><?= esc($user['Phone']) ?></div>
            </div>
        </div>

        <?= render_msgs() ?>

        <h3 class="section" style="margin-top:8px">Update Profile</h3>
        <form method="POST" novalidate>
            <input type="hidden" name="action" value="update_profile">
            <input type="hidden" name="csrf" value="<?= esc($csrf) ?>">

            <div class="form-row">
                <label for="fname">Name</label>
                <input id="fname" name="fname" type="text" value="<?= esc($user['Fname']) ?>" required>
            </div>

            <div class="form-row">
                <label for="email">Email</label>
                <input id="email" name="email" type="email" value="<?= esc($user['Email']) ?>" required>
            </div>

            <div class="form-row">
                <label for="phone">Phone (8 digits, starts 9 or 7)</label>
                <input id="phone" name="phone" type="text" value="<?= esc($user['Phone']) ?>">
            </div>

            <div style="display:flex;gap:8px">
                <div style="flex:1">
                    <label for="password">New password (optional)</label>
                    <input id="password" name="password" type="password" placeholder="Leave blank to keep">
                </div>
                <div style="flex:1">
                    <label for="confirm_password">Confirm password</label>
                    <input id="confirm_password" name="confirm_password" type="password" placeholder="Confirm">
                </div>
            </div>

            <div style="margin-top:12px">
                <button class="btn" type="submit">Save Changes</button>
            </div>
        </form>

        <hr style="margin:14px 0;border:none;border-top:1px solid #eef5ff">

        <h3 class="section">Search Events</h3>
        <form method="GET" action="">
            <div class="form-row">
                <label for="q_title">Event name or description</label>
                <input id="q_title" name="q_title" type="text" value="<?= esc($q_title) ?>" placeholder="e.g., Robotics">
            </div>
            <div class="search-grid">
                <div>
                    <label for="q_date">Date</label>
                    <input id="q_date" name="q_date" type="date" value="<?= esc($q_date) ?>">
                </div>
                <div>
                    <label for="q_time">Start time</label>
                    <input id="q_time" name="q_time" type="time" value="<?= esc($q_time) ?>">
                </div>
            </div>
            <div style="margin-top:10px;display:flex;gap:8px">
                <button class="btn" type="submit">Search</button>
                <a href="<?= strtok($_SERVER["REQUEST_URI"], '?') ?>" class="btn secondary" style="text-decoration:none;padding:9px 12px">Clear</a>
            </div>
            <p class="small" style="margin-top:8px;color:var(--muted)">Filters apply to both available and booked events.</p>
        </form>
    </div>

    <!-- RIGHT: events + booked -->
    <div>

        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
            <div>
                <h2 class="section" style="margin:0">Available Events</h2>
                <div class="small" style="margin-top:6px">Browse and book events.</div>
            </div>
            <div style="text-align:right">
                <div class="small" style="color:var(--muted)">Logged in as <strong><?= esc($user['Fname']) ?></strong></div>
                <div class="small" style="color:var(--muted)">User ID: <?= esc($user['User_ID']) ?></div>
            </div>
        </div>

        <div class="event-grid">
            <?php if (empty($events)): ?>
                <div class="panel">No events found for the current filter.</div>
            <?php else: foreach ($events as $ev): ?>
                <?php $tag = tag_for_type($ev['Event_Type']); ?>
                <div class="card">
                    <div style="display:flex;justify-content:space-between;align-items:start">
                        <div>
                            <div class="tag <?= $tag ?>"><?= esc($ev['Event_Type']) ?></div>
                            <h3><?= esc($ev['Title']) ?></h3>
                            <div class="meta">📅 <?= esc($ev['DateE']) ?> &nbsp; • &nbsp; 🕒 <?= esc(substr($ev['TimeE'],0,5)) ?> → <?= esc(substr($ev['End_TimeE'],0,5)) ?></div>
                        </div>
                        <div style="text-align:right">
                            <div class="small-muted"><?= esc($ev['Location']) ?></div>
                            <?php if (!empty($ev['Assigned_Resource'])): ?>
                                <div class="small-muted">Resource: <?= esc($ev['Assigned_Resource']) ?></div>
                            <?php endif;?>
                        </div>
                    </div>

                    <div class="desc"><?= nl2br(esc($ev['Description'])) ?></div>

                    <div class="actions">
                        <form method="POST" style="margin:0">
                            <input type="hidden" name="action" value="book_event">
                            <input type="hidden" name="csrf" value="<?= esc($csrf) ?>">
                            <input type="hidden" name="event_id" value="<?= intval($ev['Event_ID']) ?>">
                            <button class="btn" type="submit">Book</button>
                        </form>
                        <?php if (!empty($ev['Resources'])): ?>
                            <div style="margin-left:auto" class="small-muted">Resources: <?= esc($ev['Resources']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; endif; ?>
        </div>

        <div class="panel" style="margin-top:20px">
            <h2 class="section" style="margin-top:0">My Booked Events & Feedback</h2>

            <?php if (empty($booked)): ?>
                <p class="small">You have no booked events yet.</p>
            <?php else: foreach ($booked as $b): ?>
                <div class="card booked" style="margin-bottom:12px">
                    <div style="display:flex;justify-content:space-between;align-items:center">
                        <div>
                            <div class="tag <?= tag_for_type($b['Event_Type']) ?>"><?= esc($b['Event_Type']) ?></div>
                            <h3><?= esc($b['Title']) ?></h3>
                            <div class="meta">📅 <?= esc($b['DateE']) ?> • 🕒 <?= esc(substr($b['TimeE'],0,5)) ?> → <?= esc(substr($b['End_TimeE'],0,5)) ?></div>
                        </div>
                        <div style="text-align:right">
                            <div class="small-muted">Booked on <?= esc($b['Booking_Date']) ?></div>
                        </div>
                    </div>

                    <div class="desc"><?= nl2br(esc($b['Description'])) ?></div>

                    <?php
                        // Check if any feedback exists for this event
                        $fbCheck = $conn->prepare("SELECT COUNT(*) FROM FEEDBACK WHERE Comments LIKE :p");
                        $fbCheck->execute([':p' => "[EventID:{$b['Event_ID']}] %"]);
                        $alreadyFb = $fbCheck->fetchColumn() > 0;
                    ?>

                    <?php if ($alreadyFb): ?>
                        <p class="small" style="color:green;margin-top:10px"><strong>Feedback already submitted ✔</strong></p>
                    <?php else: ?>
                        <form method="POST" style="margin-top:10px">
                            <input type="hidden" name="action" value="submit_feedback">
                            <input type="hidden" name="csrf" value="<?= esc($csrf) ?>">
                            <input type="hidden" name="feedback_event" value="<?= intval($b['Event_ID']) ?>">

                            <label for="comments-<?= intval($b['Event_ID']) ?>">Your feedback</label>
                            <textarea id="comments-<?= intval($b['Event_ID']) ?>" name="comments" placeholder="Share your experience..." required></textarea>

                            <div style="display:flex;gap:8px;align-items:center;margin-top:8px">
                                <div>
                                    <label for="rating-<?= intval($b['Event_ID']) ?>" class="small">Rating</label>
                                    <select id="rating-<?= intval($b['Event_ID']) ?>" name="rating" required>
                                        <option value="">Select</option>
                                        <?php for ($i=1;$i<=5;$i++): ?>
                                            <option value="<?= $i ?>"><?= $i ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <div style="margin-left:auto">
                                    <button class="btn" type="submit">Submit Feedback</button>
                                </div>
                            </div>
                        </form>
                    <?php endif; ?>

                </div>
            <?php endforeach; endif; ?>
        </div>

    </div>

</div>

</body>
</html>
