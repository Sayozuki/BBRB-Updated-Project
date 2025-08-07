<?php
session_start();
if (!isset($_SESSION['admin_username'])) {
    header('Location: admin_login.php');
    exit;
}
require 'db.php';
require 'auto_archive.php';

date_default_timezone_set('Asia/Manila');

// Pagination variables
$itemsPerPage = 3; // Maximum announcements per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $itemsPerPage;

// Filter variable
$status = isset($_GET['status']) ? $_GET['status'] : 'active';

// Fetch announcements based on filter and pagination
$stmt = $pdo->query("SELECT * FROM announcements WHERE status = '$status' ORDER BY created_at DESC LIMIT $itemsPerPage OFFSET $offset");
$announcements = $stmt->fetchAll();

// Fetch total count for pagination
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM announcements WHERE status = ?");
$countStmt->execute([$status]);
$totalItems = $countStmt->fetchColumn();
$totalPages = ceil($totalItems / $itemsPerPage);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <link rel="stylesheet" href="admin_dashboard.css">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <?php include 'includes/sidebar.php'; ?>

    <div class="content">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Announcements</h2>
            <form method="GET" action="admin_dashboard.php" class="d-flex">
                <select name="status" class="form-select me-2" onchange="this.form.submit()">
                    <option value="active" <?php echo $status === 'active' ? 'selected' : ''; ?>>Active</option>
                    <option value="archived" <?php echo $status === 'archived' ? 'selected' : ''; ?>>Archived</option>
                </select>
            </form>
        </div>
        <ul class="list-group">
            <?php foreach ($announcements as $announcement): ?>
                <li class="list-group-item d-flex align-items-start">
                    <div class="me-3">
                        <!-- Display Thumbnail -->
                        <img src="<?php echo htmlspecialchars($announcement['thumbnail']); ?>" alt="Thumbnail" class="img-thumbnail">
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="mb-1 fw-bold"><?php echo htmlspecialchars($announcement['title']); ?></h5>
                        <p class="mb-1"><?php echo htmlspecialchars($announcement['content']); ?></p>
                        <p class="mb-1">
                            <span class="text-label"><i class="bi bi-tags"></i> Genre:</span> <?php echo htmlspecialchars($announcement['genre']); ?><br>
                            <span class="text-label"><i class="bi bi-calendar-check"></i> Active Until:</span> <?php echo htmlspecialchars($announcement['active_until']); ?><br>
                            <span class="text-label"><i class="bi bi-calendar-event"></i> Will be held:</span> <?php echo htmlspecialchars($announcement['occuring_at']); ?><br>
                            <span class="text-label"><i class="bi bi-check-circle"></i> Allow Registration:</span> <?php echo $announcement['allow_registrations'] == 1 ? 'True' : 'False'; ?>
                        </p>
                        <small class="text-muted">
                            <span class="text-label"><i class="bi bi-person"></i> By:</span> <?php echo htmlspecialchars($announcement['author']); ?><br>
                            <span class="text-label"><i class="bi bi-clock"></i> Created At:</span> <?php echo htmlspecialchars($announcement['created_at']); ?>
                        </small>
                    </div>
                    <div class="ms-3 d-flex align-items-center">
                        <!-- Edit Button -->
                        <button class="btn btn-warning btn-sm me-2" data-bs-toggle="modal" data-bs-target="#editAnnouncementModal" 
                            data-id="<?php echo $announcement['id']; ?>" 
                            data-title="<?php echo htmlspecialchars($announcement['title']); ?>" 
                            data-content="<?php echo htmlspecialchars($announcement['content']); ?>" 
                            data-genre="<?php echo htmlspecialchars($announcement['genre']); ?>" 
                            data-active-until="<?php echo htmlspecialchars($announcement['active_until']); ?>" 
                            data-occuring-at="<?php echo htmlspecialchars($announcement['occuring_at']); ?>">
                            <i class="bi bi-pencil"></i>
                        </button>

                        <!-- Conditional Buttons -->
                        <?php if ($announcement['status'] === 'active'): ?>
                            <!-- Archive Button -->
                            <form method="POST" action="archive_announcement.php" style="display: inline;">
                                <input type="hidden" name="id" value="<?php echo $announcement['id']; ?>">
                                <button type="submit" class="btn btn-secondary btn-sm">
                                    <i class="bi bi-archive"></i> 
                                </button>
                            </form>
                        <?php else: ?>
                            <!-- Delete Button -->
                            <form method="POST" action="delete_announcement.php" style="display: inline;">
                                <input type="hidden" name="id" value="<?php echo $announcement['id']; ?>">
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="bi bi-trash"></i> 
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#createAnnouncementModal">
            <i class="bi bi-file-earmark-plus"></i> New
        </button>

        <!-- Pagination -->
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <li class="page-item <?php if($page == 1) echo 'disabled'; ?>">
                    <a class="page-link" href="?page=<?php echo $page - 1; ?>&status=<?php echo $status; ?>" tabindex="-1">
                        <i class="bi bi-chevron-left"></i> Previous
                    </a>
                </li>
                <?php for($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?php if($page == $i) echo 'active'; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>&status=<?php echo $status; ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?php if($page == $totalPages) echo 'disabled'; ?>">
                    <a class="page-link" href="?page=<?php echo $page + 1; ?>&status=<?php echo $status; ?>">
                        Next <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
            </ul>
        </nav>
    </div>

    <!-- Create Announcement Modal -->
    <div class="modal fade" id="createAnnouncementModal" tabindex="-1" aria-labelledby="createAnnouncementModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content blue-theme-modal">
                <form method="POST" action="create_announcement.php" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createAnnouncementModalLabel">
                            <i class="bi bi-megaphone-fill"></i> Create New Announcement
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label for="title"><i class="bi bi-type"></i> Title</label>
                            <input type="text" name="title" id="title" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="content"><i class="bi bi-file-text"></i> Content</label>
                            <textarea name="content" id="content" class="form-control" rows="5" required></textarea>
                        </div>
                        <div class="form-group mb-3">
                            <label for="genre"><i class="bi bi-tags"></i> Genre</label>
                            <select name="genre" id="genre" class="form-select" required>
                                <option value="Music">Music</option>
                                <option value="Sports">Sports</option>
                                <option value="Education">Education</option>
                                <option value="Health">Health</option>
                                <option value="Technology">Technology</option>
                                <option value="Environment">Environment</option>
                                <option value="Community">Community</option>
                                <option value="Culture">Culture</option>
                                <option value="Art">Art</option>
                                <option value="Emergency">Emergency</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="active_until"><i class="bi bi-calendar-check"></i> Active Until</label>
                            <input type="date" name="active_until" id="active_until" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="occuring_at"><i class="bi bi-calendar-event"></i> Will be held</label>
                            <input type="date" name="occuring_at" id="occuring_at" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="thumbnail"><i class="bi bi-image"></i> Thumbnail</label>
                            <input type="file" name="thumbnail" id="thumbnail" class="form-control" accept="image/*">
                        </div>
                        <div class="form-group mb-3">
                            <label for="author"><i class="bi bi-person"></i> Author</label>
                            <input type="text" name="author" id="author" class="form-control" value="<?php echo $_SESSION['admin_username']; ?>" readonly>
                        </div>
                        <div class="form-group mb-3">
                            <label for="allow_registrations"><i class="bi bi-check-circle"></i> Allow Registrations</label>
                            <select name="allow_registrations" id="allow_registrations" class="form-select" required>
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send"></i> Post Announcement
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Announcement Modal -->
    <div class="modal fade" id="editAnnouncementModal" tabindex="-1" aria-labelledby="editAnnouncementModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content blue-theme-modal">
                <form method="POST" action="edit_announcement.php" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editAnnouncementModalLabel">
                            <i class="bi bi-pencil-fill"></i> Edit Announcement
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="edit-id">
                        <div class="form-group mb-3">
                            <label for="edit-title"><i class="bi bi-type"></i> Title</label>
                            <input type="text" name="title" id="edit-title" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="edit-content"><i class="bi bi-file-text"></i> Content</label>
                            <textarea name="content" id="edit-content" class="form-control" rows="5" required></textarea>
                        </div>
                        <div class="form-group mb-3">
                            <label for="edit-genre"><i class="bi bi-tags"></i> Genre</label>
                            <select name="genre" id="edit-genre" class="form-select" required>
                                <option value="Music">Music</option>
                                <option value="Sports">Sports</option>
                                <option value="Education">Education</option>
                                <option value="Health">Health</option>
                                <option value="Technology">Technology</option>
                                <option value="Environment">Environment</option>
                                <option value="Community">Community</option>
                                <option value="Culture">Culture</option>
                                <option value="Art">Art</option>
                                <option value="Emergency">Emergency</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="edit-active-until"><i class="bi bi-calendar-check"></i> Active Until</label>
                            <input type="date" name="active_until" id="edit-active-until" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="edit-occuring-at"><i class="bi bi-calendar-event"></i> Occurring At</label>
                            <input type="date" name="occuring_at" id="edit-occuring-at" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="edit-thumbnail"><i class="bi bi-image"></i> Thumbnail</label>
                            <input type="file" name="thumbnail" id="edit-thumbnail" class="form-control" accept="image/*">
                        </div>
                        <div class="form-group mb-3">
                            <label for="edit-allow-registrations"><i class="bi bi-check-circle"></i> Allow Registrations</label>
                            <select name="allow_registrations" id="edit-allow-registrations" class="form-select" required>
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Save Changes
                        </button>
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

    <script>
        const editAnnouncementModal = document.getElementById('editAnnouncementModal');
        editAnnouncementModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget; // Button that triggered the modal
            const id = button.getAttribute('data-id');
            const title = button.getAttribute('data-title');
            const content = button.getAttribute('data-content');
            const genre = button.getAttribute('data-genre');
            const activeUntil = button.getAttribute('data-active-until');
            const occuringAt = button.getAttribute('data-occuring-at');

            // Populate the modal fields
            document.getElementById('edit-id').value = id;
            document.getElementById('edit-title').value = title;
            document.getElementById('edit-content').value = content;
            document.getElementById('edit-genre').value = genre;
            document.getElementById('edit-active-until').value = activeUntil;
            document.getElementById('edit-occuring-at').value = occuringAt;
        });
    </script>
</body>

</html>