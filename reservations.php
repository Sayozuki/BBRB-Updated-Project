<?php
session_start();
if (!isset($_SESSION['admin_username'])) {
    header('Location: admin_login.php');
    exit;
}

require 'db.php';

// Pagination setup
$itemsPerPage = 10; // Number of reservations per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1); // Ensure the page is at least 1
$offset = ($page - 1) * $itemsPerPage;

// Fetch total number of reservations
$totalReservationsStmt = $pdo->query("SELECT COUNT(*) FROM reservations");
$totalReservations = $totalReservationsStmt->fetchColumn();
$totalPages = ceil($totalReservations / $itemsPerPage);

// Fetch reservations for the current page
$stmt = $pdo->prepare("SELECT r.*, u.first_name, u.last_name 
                       FROM reservations r 
                       JOIN users u ON r.user_id = u.id 
                       ORDER BY r.created_at DESC 
                       LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $itemsPerPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$reservations = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Reservations</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="admin_dashboard.css">
    <link rel="stylesheet" href="admin_reservation.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>

    <?php include 'includes/sidebar.php'; ?>

   <div class="content">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Reservations</h2>
            <!-- Dropdown for Active/Archived -->
            <form method="GET" action="reservations.php" class="d-flex" id="statusForm">
                <?php
                    $status = isset($_GET['status']) ? $_GET['status'] : 'active';
                ?>
                <select name="status" class="form-select me-2" id="statusSelect">
                    <option value="active" <?php echo $status === 'active' ? 'selected' : ''; ?>>Active</option>
                    <option value="archived" <?php echo $status === 'archived' ? 'selected' : ''; ?>>Archived</option>
                </select>
            </form>
        </div>
    </div>
    <div class="container mt-5">
        <div class="reservation-container mt-5">
            <table class="table table-bordered table-striped" id="reservationTable">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Type</th>
                        <th>Details</th>
                        <th>Start</th>
                        <th>End</th>
                        <th>Price Estimate</th>
                        <th>Status</th>
                        <th>Reason</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reservations as $reservation): ?>
                        <?php if (strtolower($reservation['status']) === 'pending'): // Show only pending ?>
                        <tr>
                            <td><?php echo htmlspecialchars($reservation['first_name'] . ' ' . $reservation['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($reservation['type']); ?></td>
                            <td><?php echo htmlspecialchars($reservation['details']); ?></td>
                            <td><?php echo htmlspecialchars($reservation['start_date']); ?></td>
                            <td><?php echo htmlspecialchars($reservation['end_date']); ?></td>
                            <td>PHP <?php echo htmlspecialchars($reservation['price_estimate']); ?></td>
                            <td>
                                <span class="badge <?php echo getStatusBadgeClass($reservation['status']); ?>">
                                    <?php echo htmlspecialchars($reservation['status']); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($reservation['reason']); ?></td>
                            <td>
                                <!-- Approve Button -->
                                <button class="btn btn-success btn-sm open-approve-popup" data-id="<?php echo $reservation['id']; ?>">
                                    <i class="bi bi-check-circle"></i> Approve
                                </button>
                                <!-- Deny Button -->
                                <button class="btn btn-danger btn-sm open-deny-popup" data-id="<?php echo $reservation['id']; ?>">
                                    <i class="bi bi-x-circle"></i> Deny
                                </button>
                            </td>
                        </tr>

                        <!-- Approve Popup -->
                        <div class="custom-popup-overlay" id="approvePopupOverlay-<?php echo $reservation['id']; ?>">
                            <div class="custom-popup">
                                <button class="close-popup" data-id="<?php echo $reservation['id']; ?>" data-type="approve">&times;</button>
                                <h5>Approve Reservation</h5>
                                <p>Are you sure you want to approve this reservation?</p>
                                <form method="POST" action="update_reservation_status.php?section=reservations">
                                    <input type="hidden" name="id" value="<?php echo $reservation['id']; ?>">
                                    <button type="submit" name="status" value="approved" class="btn btn-success">Yes, Approve</button>
                                    <button type="button" class="btn btn-secondary close-popup" data-id="<?php echo $reservation['id']; ?>" data-type="approve"></button>
                                </form>
                            </div>
                        </div>

                        <!-- Deny Popup -->
                        <div class="custom-popup-overlay" id="denyPopupOverlay-<?php echo $reservation['id']; ?>">
                            <div class="custom-popup">
                                <button class="close-popup" data-id="<?php echo $reservation['id']; ?>" data-type="deny">&times;</button>
                                <h5>Deny Reservation</h5>
                                <form method="POST" action="update_reservation_status.php?section=reservations">
                                    <input type="hidden" name="id" value="<?php echo $reservation['id']; ?>">
                                    <label for="reason-<?php echo $reservation['id']; ?>">Reason for Denial:</label>
                                    <input type="text" id="reason-<?php echo $reservation['id']; ?>" name="reason" class="form-control" required>
                                    <div class="mt-3">
                                        <button type="submit" name="status" value="denied" class="btn btn-danger">Deny</button>
                                        <button type="button" class="btn btn-secondary close-popup" data-id="<?php echo $reservation['id']; ?>" data-type="deny">Cancel</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination Controls -->
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <!-- Previous Button -->
            <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                <a class="page-link" href="?page=<?php echo $page - 1; ?>" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>

            <!-- Page Numbers -->
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>

            <!-- Next Button -->
            <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                <a class="page-link" href="?page=<?php echo $page + 1; ?>" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        </ul>
    </nav>

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
    // Open/close popup logic
    $(document).ready(function() {
        $('.open-approve-popup').click(function() {
            var id = $(this).data('id');
            $('#approvePopupOverlay-' + id).css('display', 'flex');
        });
        $('.open-deny-popup').click(function() {
            var id = $(this).data('id');
            $('#denyPopupOverlay-' + id).css('display', 'flex');
        });
        $('.close-popup').click(function() {
            var id = $(this).data('id');
            var type = $(this).data('type');
            if (type === 'approve') {
                $('#approvePopupOverlay-' + id).hide();
            } else if (type === 'deny') {
                $('#denyPopupOverlay-' + id).hide();
            }
        });
        // Optional: close popup when clicking outside the popup box
        $('.custom-popup-overlay').on('click', function(e) {
            if (e.target === this) $(this).hide();
        });

        // Redirect to archived_reservations.php if "Archived" is selected
        document.getElementById('statusSelect').addEventListener('change', function() {
            if (this.value === 'archived') {
                window.location.href = 'archived_reservations.php';
            } else {
                document.getElementById('statusForm').submit();
            }
        });
    });
    </script>

    <?php
    function getStatusBadgeClass($status) {
        switch (strtolower($status)) {
            case 'approved':
                return 'bg-success'; // green
            case 'denied':
                return 'bg-danger'; // red
            case 'pending':
            default:
                return 'bg-warning text-dark'; // yellow with readable text
        }
    }
    ?>
</body>
</html>