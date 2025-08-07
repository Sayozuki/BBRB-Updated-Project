<?php
session_start();
if (!isset($_SESSION['admin_username'])) {
    header('Location: admin_login.php');
    exit;
}

require 'db.php';

date_default_timezone_set('Asia/Manila'); // or your timezone

// Automatically archive events that have ended (date and time)
$now = date('Y-m-d H:i:s');
$pdo->query("UPDATE announcements 
    SET status = 'archived' 
    WHERE status = 'active' 
      AND CONCAT(event_end_date, ' ', end_time) < '$now'");

// Fetch active announcements
$stmt = $pdo->query("SELECT * FROM announcements WHERE status = 'active' ORDER BY created_at DESC");
$announcements = $stmt->fetchAll();

// Fetch archived and deleted announcements
$stmt = $pdo->query("SELECT * FROM announcements WHERE status IN ('archived', 'deleted') ORDER BY event_end_date DESC, created_at DESC");
$archived_announcements = $stmt->fetchAll();

// Fetch all reservations
$stmt = $pdo->query("SELECT r.*, u.first_name, u.last_name FROM reservations r JOIN users u ON r.user_id = u.id ORDER BY r.created_at DESC");
$reservations = $stmt->fetchAll();

// Fetch all participants grouped by announcement_title
$stmt = $pdo->query("SELECT e.announcement_title, u.first_name, u.last_name, e.full_name, e.age, e.reason, e.note
    FROM events e
    LEFT JOIN users u ON e.user_id = u.id");
$participants = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group participants by announcement_title
$participants_by_announcement = [];
foreach ($participants as $p) {
    $participants_by_announcement[$p['announcement_title']][] = $p;
}

$_SESSION['admin_id'] = 1; // or the actual admin user ID from your database

$admin_id = $_SESSION['admin_id']; // Make sure this is set!

$stmt = $pdo->prepare("SELECT * FROM messages WHERE sender_id = ? OR receiver_id = ? ORDER BY sent_at DESC");
$stmt->execute([$admin_id, $admin_id]);
$admin_messages = $stmt->fetchAll();

// Fetch all users for the dropdown
$users = $pdo->query("SELECT id, first_name, last_name FROM users")->fetchAll();

// Add this function at the top of admin_dashboard.php
function getUserName($pdo, $user_id) {
    if ($user_id == $_SESSION['admin_id']) return 'You (Admin)';
    $stmt = $pdo->prepare("SELECT first_name, last_name FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    return $user ? htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) : 'Unknown';
}

function calculate_price_estimate($data) {
    $total = 0;

    // Equipment pricing
    if (!empty($data['sound_system'])) $total += 1000;
    if (!empty($data['projector'])) $total += 1500;
    if (!empty($data['life_time_table'])) $total += 150 * intval($data['life_time_table']);
    if (!empty($data['life_time_chair'])) $total += 50 * intval($data['life_time_chair']);
    if (!empty($data['long_table'])) $total += 200 * intval($data['long_table']);
    if (!empty($data['monoblock_chair'])) $total += 10 * intval($data['monoblock_chair']);

    // Court pricing (basketball, volleyball, badminton)
    if (!empty($data['court_type']) && (!empty($data['court_day_hours']) || !empty($data['court_night_hours']))) {
        $day_hours = intval($data['court_day_hours'] ?? 0);
        $night_hours = intval($data['court_night_hours'] ?? 0);
        $total += 200 * $day_hours;
        $total += 600 * $night_hours;
    }

    // Bulawagan pricing
    if (!empty($data['bulawagan'])) {
        $hours = intval($data['bulawagan_hours']);
        $with_aircon = !empty($data['bulawagan_aircon']);
        if ($hours <= 4) {
            $total += $with_aircon ? 5000 : 3500;
        } else {
            $total += $with_aircon ? 5000 : 3500;
            $extra = $hours - 4;
            $total += $extra * ($with_aircon ? 1000 : 700);
        }
    }

    // Community Center pricing
    if (!empty($data['community_center'])) {
        $hours = intval($data['community_center_hours']);
        $with_aircon = !empty($data['community_center_aircon']);
        if ($hours <= 4) {
            $total += $with_aircon ? 4000 : 3000;
        } else {
            $total += $with_aircon ? 4000 : 3000;
            $extra = $hours - 4;
            $total += $extra * ($with_aircon ? 800 : 600);
        }
    }

    // Parking
    if (!empty($data['parking'])) {
        $total += intval($data['parking']) * 50;
    }

    // Small Meeting Room
    if (!empty($data['meeting_room_hours'])) {
        $total += intval($data['meeting_room_hours']) * 200;
    }

    // Session Hall
    if (!empty($data['session_hall_hours'])) {
        $total += intval($data['session_hall_hours']) * 600;
    }

    // Conference Room
    if (!empty($data['conference_room_hours'])) {
        $total += intval($data['conference_room_hours']) * 400;
    }

    // Playground (add pricing if needed)

    // Time slot pricing (use your time slot rules)
    if (!empty($data['start_time']) && !empty($data['end_time'])) {
        $time_prices = [
            '6am-7am' => 100, '6am-8am' => 200, '6am-9am' => 300, '6am-10am' => 400, '6am-11am' => 500, '6am-12pm' => 600,
            '7am-8am' => 100, '7am-9am' => 200, '7am-10am' => 300, '7am-11am' => 400, '7am-12pm' => 500, '7am-1pm' => 600,
            '8am-9am' => 100, '8am-10am' => 200, '8am-11am' => 300, '8am-12pm' => 400, '8am-1pm' => 500, '8am-2pm' => 600,
            '9am-10am' => 100, '9am-11am' => 200, '9am-12pm' => 300, '9am-1pm' => 400, '9am-2pm' => 500, '9am-3pm' => 600,
            '10am-11am' => 100, '10am-12pm' => 200, '10am-1pm' => 300, '10am-2pm' => 400, '10am-3pm' => 500, '10am-4pm' => 600,
            '11am-12pm' => 100, '11am-1pm' => 200, '11am-2pm' => 300, '11am-3pm' => 400, '11am-4pm' => 500, '11am-5pm' => 600,
            '12pm-1pm' => 100, '12pm-2pm' => 200, '12pm-3pm' => 300, '12pm-4pm' => 400, '12pm-5pm' => 500, '12pm-6pm' => 600,
            '1pm-2pm' => 100, '1pm-3pm' => 200, '1pm-4pm' => 300, '1pm-5pm' => 400, '1pm-6pm' => 500, '1pm-7pm' => 800,
            '2pm-3pm' => 100, '2pm-4pm' => 200, '2pm-5pm' => 300, '2pm-6pm' => 400, '2pm-7pm' => 700, '2pm-8pm' => 1000,
            '3pm-4pm' => 100, '3pm-5pm' => 200, '3pm-6pm' => 300, '3pm-7pm' => 600, '3pm-8pm' => 900, '3pm-9pm' => 1200, '3pm-10pm' => 1500,
            '4pm-5pm' => 100, '4pm-6pm' => 200, '4pm-7pm' => 500, '4pm-8pm' => 800, '4pm-9pm' => 1100, '4pm-10pm' => 1400,
            '5pm-6pm' => 100, '5pm-7pm' => 400, '5pm-8pm' => 700, '5pm-9pm' => 1000, '5pm-10pm' => 1300,
            '6pm-7pm' => 300, '6pm-8pm' => 600, '6pm-9pm' => 900, '6pm-10pm' => 1200,
            '7pm-8pm' => 300, '7pm-9pm' => 600, '7pm-10pm' => 900,
            '8pm-9pm' => 300, '8pm-10pm' => 600,
            '9pm-10pm' => 300,
        ];
        $slot = strtolower(date('ga', strtotime($data['start_time']))) . '-' . strtolower(date('ga', strtotime($data['end_time'])));
        if (isset($time_prices[$slot])) {
            $total += $time_prices[$slot];
        }
    }

    return $total;
}

$price_estimate = calculate_price_estimate($_POST); // or your data array
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="admin_dashboard.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/bootstrap.js"></script>

    
    <style>
        .sidebar {
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            background-color:rgb(46, 126, 206);
            color: #fff;
            padding-top: 20px;
        }

        .sidebar a {
            color: #fff;
            text-decoration: none;
            display: block;
            padding: 10px 20px;
        }

        .sidebar a:hover {
            background-color:rgb(25, 74, 131);
        }

        .content {
            margin-left: 260px;
            padding: 20px;
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h4 class="text-center">Admin Dashboard</h4>
        <a href="#announcements" class="sidebar-link" onclick="showSection('announcements')">
            <i class="bi bi-megaphone"></i> Announcements
        </a>
        <a href="#reservations" class="sidebar-link" onclick="showSection('reservations')">
            <i class="bi bi-calendar-check"></i> Reservations
        </a>
        <a href="messaging.php" class="sidebar-link" onclick="showSection('messages')">
            <i class="bi bi-envelope"></i> Messages
        </a>
        <a href="#resident-list" class="sidebar-link" onclick="showSection('resident-list')">
            <i class="bi bi-people"></i> Resident List
        </a>
        <a href="#staff-list" class="sidebar-link" onclick="showSection('staff-list')">
            <i class="bi bi-person-badge"></i> Staff List
        </a>
        <a href="#archive" class="sidebar-link" onclick="showSection('archive')">
            <i class="bi bi-archive"></i> Archive
        </a>
        <a href="archived_reservations.php" class="sidebar-link">
            <i class="bi bi-archive"></i> Archived Reservations
        </a>
        <!-- Logout Button -->
        <a href="#" class="sidebar-link" data-toggle="modal" data-target="#logoutModal">
            <i class="bi bi-box-arrow-right"></i> Logout
        </a>
    </div>

    <!-- Content -->
    <div class="content">
        <!-- Announcements Section -->
        <div id="announcements-section">
            <h2>Announcements</h2>
            <button class="btn btn-secondary mb-3" onclick="showSection('archive')">
                <i class="bi bi-archive"></i> Show Archive (Past & Deleted Events)
            </button>
            <ul class="list-group">
                <?php foreach ($announcements as $index => $announcement): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong><?php echo htmlspecialchars($announcement['title']); ?></strong>
                            <p><?php echo htmlspecialchars($announcement['genre']); ?> | <?php echo htmlspecialchars($announcement['date_of_announcement']); ?> - <?php echo htmlspecialchars($announcement['event_end_date']); ?></p>
                            <p><?php echo htmlspecialchars($announcement['content']); ?></p>
                            <small>By: <?php echo htmlspecialchars($announcement['author']); ?> | Created At: <?php echo htmlspecialchars($announcement['created_at']); ?></small>
                        </div>
                        <div>
                            <!-- View Participants Button -->
                            <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#participantsModal_<?php echo $index; ?>">
                                <i class="bi bi-people"></i> View Participants
                            </button>
                            <!-- Archive Button: Only show if event is done -->
                            <?php if (strtotime($announcement['event_end_date']) < strtotime(date('Y-m-d'))): ?>
                                <form method="POST" action="archive_announcement.php" style="display:inline;">
                                    <input type="hidden" name="id" value="<?php echo $announcement['id']; ?>">
                                    <button type="submit" class="btn btn-secondary btn-sm">Archive</button>
                                </form>
                            <?php endif; ?>
                            <!-- Edit Button -->
                            <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editAnnouncementModal" 
                                data-id="<?php echo $announcement['id']; ?>" 
                                data-title="<?php echo htmlspecialchars($announcement['title']); ?>" 
                                data-content="<?php echo htmlspecialchars($announcement['content']); ?>" 
                                data-genre="<?php echo htmlspecialchars($announcement['genre']); ?>" 
                                data-date="<?php echo htmlspecialchars($announcement['date_of_announcement']); ?>">
                                <i class="bi bi-pencil"></i> Edit
                            </button>
                            <!-- Delete Button -->
                            <form method="POST" action="delete_announcement.php" style="display: inline;">
                                <input type="hidden" name="id" value="<?php echo $announcement['id']; ?>">
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                    </li>

                    <!-- Participants Modal -->
                    <div class="modal fade" id="participantsModal_<?php echo $index; ?>" tabindex="-1" role="dialog" aria-labelledby="participantsModalLabel_<?php echo $index; ?>" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="participantsModalLabel_<?php echo $index; ?>">
                                        Participants for <?php echo htmlspecialchars($announcement['title']); ?>
                                    </h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <?php
                                    $title = $announcement['title'];
                                    if (!empty($participants_by_announcement[$title])): ?>
                                        <ul>
                                            <?php foreach ($participants_by_announcement[$title] as $participant): ?>
                                                <li>
                                                    <?php
                                                    echo htmlspecialchars(
                                                        $participant['full_name'] ?: ($participant['first_name'] . ' ' . $participant['last_name'])
                                                    );
                                                    ?> (Age: <?php echo htmlspecialchars($participant['age']); ?>)
                                                    <br>
                                                    Reason: <?php echo htmlspecialchars($participant['reason']); ?>
                                                    <?php if (!empty($participant['note'])): ?>
                                                        <br>Note: <?php echo htmlspecialchars($participant['note']); ?>
                                                    <?php endif; ?>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php else: ?>
                                        <p>No participants yet.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </ul>
            <br>
            <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#createAnnouncementModal">
                <i class="bi bi-file-earmark-plus"></i> New
            </button>
        </div>

        <!-- Reservations Section -->
        <div id="reservations-section" style="display: none;">
            <h2>Reservations</h2>

            <ul class="list-group" id="reservationList">
                <?php foreach ($reservations as $reservation): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong>User:</strong> <?php echo htmlspecialchars($reservation['first_name'] . ' ' . $reservation['last_name']); ?><br>
                            <strong>Type:</strong> <?php echo htmlspecialchars($reservation['type']); ?><br>
                            <strong>Details:</strong> <?php echo htmlspecialchars($reservation['details']); ?><br>
                            <strong>Start:</strong> <?php echo htmlspecialchars($reservation['start_date']); ?><br>
                            <strong>End:</strong> <?php echo htmlspecialchars($reservation['end_date']); ?><br>
                            <strong>Price Estimate:</strong> PHP <?php echo htmlspecialchars($reservation['price_estimate']); ?><br>
                            <strong>Status:</strong> <?php echo htmlspecialchars($reservation['status']); ?><br>
                            <strong>Reason:</strong> <?php echo htmlspecialchars($reservation['reason']); ?><br>
                        </div>
                        <div>
                            <!-- Approve Button -->
                            <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#approveModal-<?php echo $reservation['id']; ?>">
                                <i class="bi bi-check-circle"></i> Approve
                            </button>
                            <!-- Deny Button -->
                            <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#denyModal-<?php echo $reservation['id']; ?>">
                                <i class="bi bi-x-circle"></i> Deny
                            </button>
                        </div>
                    </li>

                    <!-- Approve Modal -->
                    <div class="modal fade" id="approveModal-<?php echo $reservation['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="approveModalLabel-<?php echo $reservation['id']; ?>" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="approveModalLabel-<?php echo $reservation['id']; ?>">Approve Reservation</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    Are you sure you want to approve this reservation?
                                </div>
                                <div class="modal-footer">
                                    <!-- Approve Form -->
                                    <form method="POST" action="update_reservation_status.php?section=reservations">
                                        <input type="hidden" name="id" value="<?php echo $reservation['id']; ?>">
                                        <button type="submit" name="status" value="approved" class="btn btn-success">Yes, Approve</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Deny Modal -->
                    <div class="modal fade" id="denyModal-<?php echo $reservation['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="denyModalLabel-<?php echo $reservation['id']; ?>" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="denyModalLabel-<?php echo $reservation['id']; ?>">Deny Reservation</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-footer">
                                    <!-- Deny Form -->
                                    <form method="POST" action="update_reservation_status.php?section=reservations">
                                        <input type="hidden" name="id" value="<?php echo $reservation['id']; ?>">
                                        <label for="reason-<?php echo $reservation['id']; ?>">Reason for Denial:</label>
                                        <select id="reason-<?php echo $reservation['id']; ?>" name="reason" class="form-control" required>
                                            <option value="Not available">Not available</option>
                                            <option value="Conflict Schedule">Conflict Schedule</option>
                                            <option value="Other">Other</option>
                                        </select>
                                        <button type="submit" name="status" value="denied" class="btn btn-danger mt-3">Deny</button>
                                    </form>
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- Messages Section -->
        <div id="messages-section" style="display: none;">
            <h2>Messages (Admin)</h2>
            
            <!-- Admin Message Form -->
            <form id="admin-message-form" class="mb-4">
                <div class="mb-3">
                    <label for="receiver_id" class="form-label">Send to User</label>
                    <select class="form-control" id="receiver_id" name="receiver_id" required>
                        <option value="">Select User</option>
                        <?php foreach ($users as $user): ?>
                            <option value="<?php echo $user['id']; ?>">
                                <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="subject" class="form-label">Subject</label>
                    <input type="text" class="form-control" id="subject" name="subject" required>
                </div>
                <div class="mb-3">
                    <label for="content" class="form-label">Message</label>
                    <textarea class="form-control" id="content" name="message" rows="3" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Send Message</button>
            </form>
            <div id="message-feedback"></div>

            <h4>Conversation History</h4>
            <ul class="list-group mt-3">
                <?php if (empty($admin_messages)): ?>
                    <li class="list-group-item text-center text-muted">No messages yet.</li>
                <?php else: ?>
                    <?php foreach ($admin_messages as $msg): ?>
                        <li class="list-group-item">
                            <div>
                                <strong><?php echo htmlspecialchars($msg['subject']); ?></strong>
                                <span class="badge bg-<?php echo $msg['sender_id'] == $admin_id ? 'primary' : 'secondary'; ?>">
                                    <?php echo $msg['sender_id'] == $admin_id ? 'Sent' : 'Received'; ?>
                                </span>
                            </div>
                            <div>
                                <span><?php echo nl2br(htmlspecialchars($msg['message'])); ?></span>
                            </div>
                            <small class="text-muted">
                                <?php if ($msg['sender_id'] == $admin_id): ?>
                                    To: <?php echo getUserName($pdo, $msg['receiver_id']); ?>
                                <?php else: ?>
                                    From: <?php echo getUserName($pdo, $msg['sender_id']); ?>
                                <?php endif; ?>
                                | <?php echo $msg['sent_at']; ?>
                            </small>
                            <?php if ($msg['sender_id'] != $admin_id): ?>
                                <button class="btn btn-link btn-sm" onclick="replyToUser('<?php echo $msg['sender_id']; ?>', '<?php echo htmlspecialchars(addslashes($msg['subject'])); ?>')">Reply</button>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>
        
        
        <?php include 'resident_list.php'; ?>

        <?php include 'staff_list.php'; ?>

        <!-- Archive Section -->
        <div id="archive-section" style="display: none;">
            <button class="btn btn-primary mb-3" onclick="showSection('announcements')">
                <i class="bi bi-arrow-left"></i> Back to Announcements
            </button>
            <h3>Archive (Past & Deleted Events)</h3>
            <ul class="list-group">
                <?php if (empty($archived_announcements)): ?>
                    <li class="list-group-item">No archived or deleted events.</li>
                <?php else: ?>
                    <?php foreach ($archived_announcements as $announcement): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong><?php echo htmlspecialchars($announcement['title']); ?></strong>
                                <p>
                                    <?php echo htmlspecialchars($announcement['genre']); ?> |
                                    <?php echo htmlspecialchars($announcement['date_of_announcement']); ?>
                                    <?php if (!empty($announcement['event_end_date'])): ?>
                                        - <?php echo htmlspecialchars($announcement['event_end_date']); ?>
                                    <?php endif; ?>
                                </p>
                                <p><?php echo htmlspecialchars($announcement['content']); ?></p>
                                <small>Status: <?php echo htmlspecialchars($announcement['status']); ?></small>
                            </div>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>
        
    </div>

    <!-- Create Announcement Modal -->
<div class="modal fade" id="createAnnouncementModal" tabindex="-1" role="dialog" aria-labelledby="createAnnouncementModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form method="POST" action="create_announcement.php" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="createAnnouncementModalLabel">Create New Announcement</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" name="title" id="title" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="content">Content</label>
                        <textarea name="content" id="content" class="form-control" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="genre">Genre</label>
                        <input type="text" name="genre" id="genre" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="date_of_announcement">Date of Announcement</label>
                        <input type="date" name="date_of_announcement" id="date_of_announcement" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="start_time">Start Time</label>
                        <input type="time" name="start_time" id="start_time" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="end_time">End Time</label>
                        <input type="time" name="end_time" id="end_time" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="thumbnail">Thumbnail</label>
                        <input type="file" name="thumbnail" id="thumbnail" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Post Announcement</button>
                </div>
            </form>
        </div>
    </div>
</div>

    <!-- Edit Announcement Modal -->
<div class="modal fade" id="editAnnouncementModal" tabindex="-1" role="dialog" aria-labelledby="editAnnouncementModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form method="POST" action="edit_announcement.php" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="editAnnouncementModalLabel">Edit Announcement</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit-id">
                    <div class="form-group">
                        <label for="edit-title">Title</label>
                        <input type="text" name="title" id="edit-title" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-content">Content</label>
                        <textarea name="content" id="edit-content" class="form-control" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="edit-genre">Genre</label>
                        <input type="text" name="genre" id="edit-genre" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-date_of_announcement">Date of Announcement</label>
                        <input type="date" name="date_of_announcement" id="edit-date_of_announcement" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-start_time">Start Time</label>
                        <input type="time" name="start_time" id="edit-start_time" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-end_time">End Time</label>
                        <input type="time" name="end_time" id="edit-end_time" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-thumbnail">Thumbnail</label>
                        <input type="file" name="thumbnail" id="edit-thumbnail" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

    <!-- Logout Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logoutModalLabel">Confirm Logout</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to log out?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <a href="logout.php" class="btn btn-danger">Logout</a>
            </div>
        </div>
    </div>
</div>

    <script src="js/jquery-3.4.1.min.js"></script>
    <script src="js/bootstrap.js"></script>
    <script>
        function showSection(section) {
            document.getElementById('announcements-section').style.display = 'none';
            document.getElementById('reservations-section').style.display = 'none';
            document.getElementById('messages-section').style.display = 'none';
            document.getElementById('resident-list-section').style.display = 'none';
            document.getElementById('staff-list-section').style.display = 'none';
            document.getElementById('archive-section').style.display = 'none';

            document.getElementById(section + '-section').style.display = 'block';
        }

        // Automatically show the section based on the query parameter
        document.addEventListener('DOMContentLoaded', function () {
            const urlParams = new URLSearchParams(window.location.search);
            const section = urlParams.get('section');
            if (section) {
                showSection(section);
            } else {
                showSection('announcements'); // Default section
            }
        });

        function replyToUser(userId, subject) {
            document.getElementById('receiver_id').value = userId;
            document.getElementById('subject').value = 'Re: ' + subject;
            document.getElementById('content').focus();
            showSection('messages');
        }

        $('#admin-message-form').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: 'send_message.php',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    let feedback = '';
                    if (response.success) {
                        feedback = '<div class="alert alert-success">' + response.success + '</div>';
                        $('#admin-message-form')[0].reset();
                    } else if (response.error) {
                        feedback = '<div class="alert alert-danger">' + response.error + '</div>';
                    }
                    $('#message-feedback').html(feedback);
                },
                error: function() {
                    $('#message-feedback').html('<div class="alert alert-danger">An error occurred.</div>');
                }
            });
        });
    </script>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($_GET['msg']); ?></div>
<?php endif; ?>

</body>

</html>
<?php
echo "<!-- Archive Query Ran at $now -->";
?>