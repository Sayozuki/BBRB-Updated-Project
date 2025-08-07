<?php
session_start();
require 'db.php';

$user_id = $_SESSION['user_id'];

// Fetch all messages where the user is sender or receiver
$stmt = $pdo->prepare("SELECT * FROM messages WHERE sender_id = ? OR receiver_id = ? ORDER BY sent_at DESC");
$stmt->execute([$user_id, $user_id]);
$messages = $stmt->fetchAll();

// Pagination variables
$items_per_page = 5; // Number of messages per page
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Current page number
$offset = ($current_page - 1) * $items_per_page; // Offset for SQL query
$total_messages = count($messages);
$total_pages = ceil($total_messages / $items_per_page);
$paginated_messages = array_slice($messages, $offset, $items_per_page);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Messages</title>
    <link href="css/style.css" rel="stylesheet" />
    <link href="css/bootstrap.css" rel="stylesheet">
         <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

 <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
 <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        body {
            background-color: #f8f9fa;
        }
        .messages-list {
            max-width: 800px;
            margin: auto;
        }
        .messages-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .messages-header h2 {
            margin: 0;
        }
        .list-group-item {
            border-radius: 0.5rem;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?> <!-- Import Navbar -->

    <div class="container mt-4 messages-list">
        <div class="messages-header">
            <h2>Your Messages</h2>
            <a href="services.php" class="btn btn-secondary">&larr; Back to Services</a>
        </div>
        <!-- Button trigger modal -->
        <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#sendMessageModal">
            Send New Message
        </button>
        <ul class="list-group">
            <?php if (empty($paginated_messages)): ?>
                <li class="list-group-item text-center text-muted">No messages yet.</li>
            <?php else: ?>
                <?php foreach ($paginated_messages as $msg): ?>
                    <li class="list-group-item">
                        <strong><?php echo htmlspecialchars($msg['subject']); ?></strong><br>
                        <span><?php echo nl2br(htmlspecialchars($msg['message'])); ?></span><br>
                        <small class="text-muted">
                            From: <?php echo $msg['sender_id'] == $user_id ? 'You' : 'User #' . $msg['sender_id']; ?> |
                            To: <?php echo $msg['receiver_id'] == $user_id ? 'You' : 'User #' . $msg['receiver_id']; ?> |
                            Sent: <?php echo $msg['sent_at']; ?>
                        </small>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>

        <!-- Pagination Links -->
        <nav aria-label="Messages Pagination">
            <ul class="pagination justify-content-center mt-3">
                <?php for ($page = 1; $page <= $total_pages; $page++): ?>
                    <li class="page-item <?php echo $page === $current_page ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $page; ?>"><?php echo $page; ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>

    <!-- Send Message Modal -->
    <div class="modal fade" id="sendMessageModal" tabindex="-1" aria-labelledby="sendMessageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"> <!-- Added modal-dialog-centered -->
            <div class="modal-content">
                <form method="POST" action="send_message.php">
                    <input type="hidden" name="source" value="messages"> <!-- Source indicator -->
                    <div class="modal-header">
                        <h5 class="modal-title" id="sendMessageModalLabel">Send New Message</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="receiver_id" class="form-label">Recipient</label>
                            <select class="form-control" id="receiver_id" name="receiver_id" required>
                                <option value="1">Admin</option>
                                <option value="8">Admin 2</option>
                                <option value="9">Kagawad</option>
                                <option value="10">Secretary</option>
                                <option value="11">Treasurer</option>
                                <!-- Add more options if you want to message other users -->
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="subject" class="form-label">Subject</label>
                            <input type="text" class="form-control" id="subject" name="subject" required>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea class="form-control" id="message" name="message" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Send</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>