
<?php
require 'db.php';

// Fetch admins from the admin_accounts table
$stmt = $pdo->query("SELECT username, role FROM admin_accounts ORDER BY username ASC");
$admins = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff List</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Custom styles -->
    <link href="resident_list.css" rel="stylesheet">
</head>

<body>
    <?php include 'includes/sidebar.php'; ?>

    <div class="content">
        <h2 class="mb-4">Staff List</h2>

        <!-- Search and Sort Controls -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <!-- Search Input -->
            <div class="input-group w-50">
                <span class="input-group-text bg-primary text-white">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" id="searchInput" class="form-control" placeholder="Search by username...">
            </div>

            <!-- Sort Dropdown -->
            <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle btn-sm" type="button" id="sortDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-sort-alpha-down"></i> Sort
                </button>
                <ul class="dropdown-menu" aria-labelledby="sortDropdown">
                    <li><a class="dropdown-item" id="sortAZ" href="#">A-Z</a></li>
                    <li><a class="dropdown-item" id="sortZA" href="#">Z-A</a></li>
                </ul>
            </div>
        </div>

        <!-- Staff Table -->
        <table class="table table-striped table-sm" id="staffTable">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Role</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($admins as $admin): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($admin['username']); ?></td>
                        <td><?php echo htmlspecialchars($admin['role']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
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

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function () {
            const table = $('#staffTable tbody');

            // Search Functionality
            $('#searchInput').on('input', function () {
                const searchValue = $(this).val().toLowerCase();
                table.find('tr').each(function () {
                    const rowText = $(this).text().toLowerCase();
                    $(this).toggle(rowText.includes(searchValue));
                });
            });

            // Sort A-Z
            $('#sortAZ').on('click', function (e) {
                e.preventDefault();
                const rows = table.find('tr').toArray().sort((a, b) => {
                    const nameA = $(a).find('td:first').text().toLowerCase();
                    const nameB = $(b).find('td:first').text().toLowerCase();
                    return nameA.localeCompare(nameB);
                });
                table.append(rows);
            });

            // Sort Z-A
            $('#sortZA').on('click', function (e) {
                e.preventDefault();
                const rows = table.find('tr').toArray().sort((a, b) => {
                    const nameA = $(a).find('td:first').text().toLowerCase();
                    const nameB = $(b).find('td:first').text().toLowerCase();
                    return nameB.localeCompare(nameA);
                });
                table.append(rows);
            });
        });
    </script>
</body>

</html>