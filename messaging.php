<?php
session_start();
require 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['admin_username'])) {
    header('Location: admin_login.php');
    exit;
}

// Fetch admin ID from session
$admin_username = $_SESSION['admin_username'];
$stmt = $pdo->prepare("SELECT id FROM admin_accounts WHERE username = ?");
$stmt->execute([$admin_username]);
$admin = $stmt->fetch();
$admin_id = $admin['id'];

// Fetch all messages for the logged-in admin
$query = "SELECT m.id, m.subject, m.message, m.is_read, m.created_at, 
                 CONCAT(u.last_name, ', ', u.first_name) AS sender_name
          FROM messages m
          JOIN users u ON m.sender_id = u.id
          WHERE m.receiver_id = ?
          ORDER BY m.created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute([$admin_id]);
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messaging</title>
    <link rel="stylesheet" href="css/bootstrap.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/bootstrap.js"></script>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="admin_dashboard.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="js/bootstrap.js"></script>


<style>
    .sidebar {
    position: fixed;
    top: 0;
    left: 0;
    height: 100%;
    width: 250px;
    background-color: #001f3f; /* Navy blue background */
    color: #fff;
    padding: 20px;
    box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
    z-index: 1000; /* Ensure the sidebar is above other elements */
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
<?php include 'includes/sidebar.php'; ?>
    <!-- Content -->
    <div class="content">
        <div class="container mt-5 messaging-container">
            <h2>Inbox</h2>
            <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#sendMessageModal">Send New Message</button>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Subject</th>
                        <th>Sender</th>
                        <th>Sent At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($paginated_messages as $message): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($message['subject']); ?></td>
                            <td><?php echo htmlspecialchars($message['sender_name']); ?></td>
                            <td><?php echo htmlspecialchars($message['created_at']); ?></td>
                            <td>
                                <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#viewMessageModal<?php echo $message['id']; ?>">View</button>
                            </td>
                        </tr>

                        <!-- View Message Modal -->
                        <div class="modal fade" id="viewMessageModal<?php echo $message['id']; ?>" tabindex="-1" aria-labelledby="viewMessageModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="viewMessageModalLabel">Message Details</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p><strong>Subject:</strong> <?php echo htmlspecialchars($message['subject']); ?></p>
                                        <p><strong>Message:</strong> <?php echo nl2br(htmlspecialchars($message['message'])); ?></p>
                                        <p><strong>Sender:</strong> <?php echo htmlspecialchars($message['sender_name']); ?></p>
                                        <p><strong>Sent At:</strong> <?php echo htmlspecialchars($message['created_at']); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    <li class="page-item <?php if($current_page == 1) echo 'disabled'; ?>">
                        <a class="page-link" href="?page=<?php echo $current_page - 1; ?>" tabindex="-1">Previous</a>
                    </li>
                    <?php for($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php if($current_page == $i) echo 'active'; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?php if($current_page == $total_pages) echo 'disabled'; ?>">
                        <a class="page-link" href="?page=<?php echo $current_page + 1; ?>">Next</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>

    <!-- Send Message Modal -->
    <div class="modal fade" id="sendMessageModal" tabindex="-1" aria-labelledby="sendMessageModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="send_message.php" method="POST">
                    <div class="modal-body">
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        <div class="mb-3">
                            <label for="receiver_id" class="form-label">Recipient</label>
                            <select name="receiver_id" id="receiver_id" class="form-control" required>
                                <?php
                                // Fetch all users
                                $stmt = $pdo->query("SELECT id, CONCAT(last_name, ', ', first_name) AS full_name FROM users");
                                while ($user = $stmt->fetch()): ?>
                                    <option value="<?php echo $user['id']; ?>">
                                        <?php echo htmlspecialchars($user['full_name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="subject" class="form-label">Subject</label>
                            <input type="text" name="subject" id="subject" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea name="message" id="message" class="form-control" rows="5" required></textarea>
                        </div>
                        <!-- Add the source parameter -->
                        <input type="hidden" name="source" value="messaging">
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Send</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

        <!-- Logout Modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content"> 
                <div class="modal-header">
                    <h5 class="modal-title" id="logoutModalLabel">Confirm Logout</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to log out?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a href="logout.php" class="btn btn-danger">Logout</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>