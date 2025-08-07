<?php
require 'db.php';

// Fetch residents from the users table
$stmt = $pdo->query("SELECT last_name, first_name, middle_name, email, contact_number, address, birthday, blood_type FROM users ORDER BY last_name ASC");
$residents = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resident List</title>

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
        <h2 class="mb-4">Resident List</h2>

        <!-- Search and Sort Controls -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <!-- Search Input -->
            <div class="input-group w-50">
                <span class="input-group-text bg-primary text-white">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" id="searchInput" class="form-control" placeholder="Search by name...">
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

        <!-- Resident Table -->
        <table class="table table-striped table-sm" id="residentTable">
            <thead>
                <tr>
                    <th>Last Name</th>
                    <th>First Name</th>
                    <th>Middle Name</th>
                    <th>Email</th>
                    <th>Contact No.</th>
                    <th>Address</th>
                    <th>Birthday</th>
                    <th>Blood Type</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($residents as $resident): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($resident['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($resident['first_name']); ?></td>
                        <td><?php echo htmlspecialchars($resident['middle_name']); ?></td>
                        <td><?php echo htmlspecialchars($resident['email']); ?></td>
                        <td><?php echo htmlspecialchars($resident['contact_number']); ?></td>
                        <td><?php echo htmlspecialchars($resident['address']); ?></td>
                        <td><?php echo htmlspecialchars($resident['birthday']); ?></td>
                        <td><?php echo htmlspecialchars($resident['blood_type']); ?></td>
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
            const table = $('#residentTable tbody');

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