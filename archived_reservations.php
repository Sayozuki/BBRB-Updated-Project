<?php
session_start();
require 'db.php';

// Fetch all archived reservations
$stmt = $pdo->query("SELECT ar.*, u.first_name, u.last_name FROM archived_reservations ar JOIN users u ON ar.user_id = u.id ORDER BY ar.created_at DESC");
$archived_reservations = $stmt->fetchAll();

// Separate approved and denied reservations
$approved_reservations = array_filter($archived_reservations, function ($reservation) {
    return strtolower($reservation['status']) === 'approved';
});

$denied_reservations = array_filter($archived_reservations, function ($reservation) {
    return strtolower($reservation['status']) === 'denied';
});
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Archived Reservations</title>
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="admin_dashboard.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="admin_reservation.css">
</head>
<body>

    <?php include 'includes/sidebar.php'; ?>

    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Archived Reservations</h2>
            <form method="GET" action="archived_reservations.php" class="d-flex" id="statusForm">
                <?php
                    $status = isset($_GET['status']) ? $_GET['status'] : 'archived';
                ?>
                <select name="status" class="form-select custom-dropdown" id="statusSelect">
                    <option value="active" <?php echo $status === 'active' ? 'selected' : ''; ?>>Active</option>
                    <option value="archived" <?php echo $status === 'archived' ? 'selected' : ''; ?>>Archived</option>
                </select>
            </form>
        </div>
        <script>
            document.getElementById('statusSelect').addEventListener('change', function() {
                if (this.value === 'active') {
                    window.location.href = 'reservations.php';
                } else {
                    document.getElementById('statusForm').submit();
                }
            });
        </script>

        <!-- Toggle Buttons -->
        <div class="mb-3">
            <button class="btn btn-primary" id="showApproved">Show Approved</button>
            <button class="btn btn-secondary" id="showDenied">Show Denied</button>
        </div>

        <!-- Search Bar -->
        <div class="mb-3">
            <input type="text" id="searchInput" class="form-control" placeholder="Search reservations...">
        </div>

        <!-- Approved Reservations Table -->
        <div id="approvedSection">
            <h3>Approved Reservations</h3>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Type</th>
                            <th>Details</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Price Estimate</th>
                            <th>Status</th>
                            <th>Reason</th>
                        </tr>
                    </thead>
                    <tbody id="approvedTable">
                        <?php foreach ($approved_reservations as $reservation): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($reservation['first_name'] . ' ' . $reservation['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($reservation['type']); ?></td>
                                <td><?php echo htmlspecialchars($reservation['details']); ?></td>
                                <td><?php echo htmlspecialchars($reservation['start_date']); ?></td>
                                <td><?php echo htmlspecialchars($reservation['end_date']); ?></td>
                                <td>PHP <?php echo htmlspecialchars($reservation['price_estimate']); ?></td>
                                <td>
                                    <span class="badge bg-success"><?php echo htmlspecialchars($reservation['status']); ?></span>
                                </td>
                                <td><?php echo htmlspecialchars($reservation['reason']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <nav>
                <ul class="pagination justify-content-center" id="approvedPagination"></ul>
            </nav>
        </div>

        <!-- Denied Reservations Table -->
        <div id="deniedSection" style="display: none;">
            <h3>Denied Reservations</h3>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Type</th>
                            <th>Details</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Price Estimate</th>
                            <th>Status</th>
                            <th>Reason</th>
                        </tr>
                    </thead>
                    <tbody id="deniedTable">
                        <?php foreach ($denied_reservations as $reservation): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($reservation['first_name'] . ' ' . $reservation['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($reservation['type']); ?></td>
                                <td><?php echo htmlspecialchars($reservation['details']); ?></td>
                                <td><?php echo htmlspecialchars($reservation['start_date']); ?></td>
                                <td><?php echo htmlspecialchars($reservation['end_date']); ?></td>
                                <td>PHP <?php echo htmlspecialchars($reservation['price_estimate']); ?></td>
                                <td>
                                    <span class="badge bg-danger"><?php echo htmlspecialchars($reservation['status']); ?></span>
                                </td>
                                <td><?php echo htmlspecialchars($reservation['reason']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <nav>
                <ul class="pagination justify-content-center" id="deniedPagination"></ul>
            </nav>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS for Toggle, Search, and Pagination -->
    <script>
        const approvedSection = document.getElementById('approvedSection');
        const deniedSection = document.getElementById('deniedSection');
        const showApproved = document.getElementById('showApproved');
        const showDenied = document.getElementById('showDenied');
        const searchInput = document.getElementById('searchInput');
        const rowsPerPage = 5;

        // Toggle between Approved and Denied sections
        showApproved.addEventListener('click', () => {
            approvedSection.style.display = '';
            deniedSection.style.display = 'none';
            searchInput.value = ''; // Clear search input when switching sections
            applySearch('approvedTable');
            paginateTable('approvedTable', 'approvedPagination');
        });

        showDenied.addEventListener('click', () => {
            approvedSection.style.display = 'none';
            deniedSection.style.display = '';
            searchInput.value = ''; // Clear search input when switching sections
            applySearch('deniedTable');
            paginateTable('deniedTable', 'deniedPagination');
        });

        // Search functionality
        function applySearch(tableId) {
            const table = document.getElementById(tableId);
            const rows = Array.from(table.getElementsByTagName('tr'));
            const query = searchInput.value.toLowerCase();

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(query) ? '' : 'none';
            });
        }

        searchInput.addEventListener('input', () => {
            if (approvedSection.style.display !== 'none') {
                applySearch('approvedTable');
            } else {
                applySearch('deniedTable');
            }
        });

        // Pagination logic
        function paginateTable(tableId, paginationId) {
            const table = document.getElementById(tableId);
            const pagination = document.getElementById(paginationId);
            const rows = Array.from(table.getElementsByTagName('tr'));
            let currentPage = 1;

            function displayRows() {
                const start = (currentPage - 1) * rowsPerPage;
                const end = start + rowsPerPage;
                rows.forEach((row, index) => {
                    row.style.display = index >= start && index < end ? '' : 'none';
                });
            }

            function setupPagination() {
                pagination.innerHTML = '';
                const pageCount = Math.ceil(rows.length / rowsPerPage);
                for (let i = 1; i <= pageCount; i++) {
                    const li = document.createElement('li');
                    li.className = 'page-item' + (i === currentPage ? ' active' : '');
                    li.innerHTML = `<a class="page-link" href="#">${i}</a>`;
                    li.addEventListener('click', (e) => {
                        e.preventDefault();
                        currentPage = i;
                        displayRows();
                        setupPagination();
                    });
                    pagination.appendChild(li);
                }
            }

            displayRows();
            setupPagination();
        }

        // Initial setup
        paginateTable('approvedTable', 'approvedPagination');
        applySearch('approvedTable');
    </script>
</body>
</html>