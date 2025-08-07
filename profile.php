<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require 'db.php';

// Fetch user information
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch reservations for the logged-in user
$stmt = $pdo->prepare("SELECT * FROM reservations WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$reservations = $stmt->fetchAll();

// Fetch archived reservations for the logged-in user
$stmt = $pdo->prepare("SELECT ar.*, 'archived' AS source FROM archived_reservations ar WHERE ar.user_id = ? ORDER BY ar.created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$archived_reservations = $stmt->fetchAll();

// Merge active and archived reservations
$reservations = array_merge($reservations, $archived_reservations);

// Pagination variables
$items_per_page = 5; // Number of reservations per page
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Current page number
$offset = ($current_page - 1) * $items_per_page; // Offset for SQL query

// Fetch total number of reservations
$total_reservations = count($reservations);
$total_pages = ceil($total_reservations / $items_per_page);

// Fetch reservations for the current page
$paginated_reservations = array_slice($reservations, $offset, $items_per_page);

// Fetch messages for the logged-in user
$stmt = $pdo->prepare("SELECT m.id, m.subject, m.message, m.is_read, m.created_at, 
                              IFNULL(a.username, 'Admin') AS sender_name
                       FROM messages m
                       LEFT JOIN admin_accounts a ON m.sender_id = a.id
                       WHERE m.receiver_id = ?
                       ORDER BY m.created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$messages = $stmt->fetchAll();

// Pagination for messages
$current_message_page = isset($_GET['message_page']) ? (int)$_GET['message_page'] : 1;
$offset_messages = ($current_message_page - 1) * $items_per_page;
$total_messages = count($messages);
$total_message_pages = ceil($total_messages / $items_per_page);
$paginated_messages = array_slice($messages, $offset_messages, $items_per_page);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/style.css">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.1.3/assets/owl.carousel.min.css" />

    <!-- bootstrap core css -->
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css" />

    <!-- fonts style -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,600,700&display=swap" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="css/style.css" rel="stylesheet" />
    <!-- responsive style -->
    <link href="css/responsive.css" rel="stylesheet" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <link rel="stylesheet" href="css/profile.css">

</head>

<body>
    <?php include 'header.php'; ?>

    <div class="container mt-5 profile-container">
        <h2 class="text-center">Profile</h2>
        <ul class="nav nav-tabs" id="profileTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="true">Profile</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="reservation-tab" data-toggle="tab" href="#reservation" role="tab" aria-controls="reservation" aria-selected="false">Reservation History</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="messages-tab" data-toggle="tab" href="#messages" role="tab" aria-controls="messages" aria-selected="false">Messages</a>
            </li>
        </ul>
        <div class="tab-content mt-4" id="profileTabsContent">
            <!-- Profile Tab -->
            <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Personal Information</h5>
                        <ul class="list-group">
                            <li class="list-group-item">
                                <strong>First Name:</strong> <?php echo htmlspecialchars($user['first_name']); ?>
                                <a href="#" class="float-right text-primary edit-button" 
                                data-variable-name="First Name" 
                                data-current-value="<?php echo htmlspecialchars($user['first_name']); ?>">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                            </li>
                            <li class="list-group-item">
                                <strong>Middle Name:</strong> <?php echo htmlspecialchars($user['middle_name']); ?>
                                <a href="#" class="float-right text-primary edit-button" 
                                data-variable-name="Middle Name" 
                                data-current-value="<?php echo htmlspecialchars($user['middle_name']); ?>">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                            </li>
                            <li class="list-group-item">
                                <strong>Last Name:</strong> <?php echo htmlspecialchars($user['last_name']); ?>
                                <a href="#" class="float-right text-primary edit-button" 
                                data-variable-name="Last Name" 
                                data-current-value="<?php echo htmlspecialchars($user['last_name']); ?>">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                            </li>
                            <li class="list-group-item">
                                <strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?>
                                <a href="#" class="float-right text-primary edit-button" 
                                data-variable-name="Email" 
                                data-current-value="<?php echo htmlspecialchars($user['email']); ?>">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                            </li>
                            <li class="list-group-item">
                                <strong>Address:</strong> <?php echo htmlspecialchars($user['address']); ?>
                                <a href="#" class="float-right text-primary edit-button" 
                                data-variable-name="Address" 
                                data-current-value="<?php echo htmlspecialchars($user['address']); ?>">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                            </li>
                            <li class="list-group-item">
                                <strong>City:</strong> <?php echo htmlspecialchars($user['city']); ?>
                                <a href="#" class="float-right text-primary edit-button" 
                                data-variable-name="City" 
                                data-current-value="<?php echo htmlspecialchars($user['city']); ?>">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                            </li>
                            <li class="list-group-item">
                                <strong>ZIP Code:</strong> <?php echo htmlspecialchars($user['zip_code']); ?>
                                <a href="#" class="float-right text-primary edit-button" 
                                data-variable-name="ZIP Code" 
                                data-current-value="<?php echo htmlspecialchars($user['zip_code']); ?>">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                            </li>
                            <li class="list-group-item">
                                <strong>Birthday:</strong> <?php echo htmlspecialchars($user['birthday']); ?>
                                <a href="#" class="float-right text-primary edit-button" 
                                data-variable-name="Birthday" 
                                data-current-value="<?php echo htmlspecialchars($user['birthday']); ?>">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                            </li>
                            <li class="list-group-item">
                                <strong>Blood Type:</strong> <?php echo htmlspecialchars($user['blood_type']); ?>
                                <a href="#" class="float-right text-primary edit-button" 
                                data-variable-name="Blood Type" 
                                data-current-value="<?php echo htmlspecialchars($user['blood_type']); ?>">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                            </li>
                            <li class="list-group-item">
                                <strong>Contact Number:</strong> <?php echo htmlspecialchars($user['contact_number']); ?>
                                <a href="#" class="float-right text-primary edit-button" 
                                   data-variable-name="Contact Number" 
                                   data-current-value="<?php echo htmlspecialchars($user['contact_number']); ?>">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                            </li>
                            <li class="list-group-item">
                                <strong>SSS Number:</strong> <?php echo htmlspecialchars($user['sss_number'] ?? 'Not Provided'); ?>
                                <a href="#" class="float-right text-primary edit-button" 
                                   data-variable-name="SSS Number" 
                                   data-current-value="<?php echo htmlspecialchars($user['sss_number']); ?>">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                            </li>
                            <li class="list-group-item">
                                <strong>Pag-ibig Number:</strong> <?php echo htmlspecialchars($user['pagibig_number'] ?? 'Not Provided'); ?>
                                <a href="#" class="float-right text-primary edit-button" 
                                   data-variable-name="Pag-ibig Number" 
                                   data-current-value="<?php echo htmlspecialchars($user['pagibig_number']); ?>">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                            </li>
                            <li class="list-group-item">
                                <strong>TIN Number:</strong> <?php echo htmlspecialchars($user['tin_number'] ?? 'Not Provided'); ?>
                                <a href="#" class="float-right text-primary edit-button" 
                                   data-variable-name="TIN Number" 
                                   data-current-value="<?php echo htmlspecialchars($user['tin_number']); ?>">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Reservation History Tab -->
            <div class="tab-pane fade" id="reservation" role="tabpanel" aria-labelledby="reservation-tab">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Reservation History</h5>

                        <!-- Filter Dropdown -->
                        <div class="form-group">
                            <label for="filterStatus">Filter by Status:</label>
                            <select id="filterStatus" class="form-control">
                                <option value="all">All</option>
                                <option value="pending">Pending</option>
                                <option value="approved">Approved</option>
                                <option value="denied">Denied</option>
                            </select>
                        </div>

                        <?php if (count($reservations) > 0): ?>
                            <ul class="list-group" id="reservationList">
                                <?php foreach ($paginated_reservations as $reservation): ?>
                                    <li class="list-group-item reservation-item" data-status="<?php echo htmlspecialchars($reservation['status']); ?>">
                                        <strong>Type:</strong> <?php echo htmlspecialchars($reservation['type']); ?><br>
                                        <strong>Details:</strong> <?php echo htmlspecialchars($reservation['details']); ?><br>
                                        <strong>Start:</strong> <?php echo htmlspecialchars($reservation['start_date']); ?><br>
                                        <strong>End:</strong> <?php echo htmlspecialchars($reservation['end_date']); ?><br>
                                        <strong>Price Estimate:</strong> PHP <?php echo htmlspecialchars($reservation['price_estimate']); ?><br>
                                        <strong>Status:</strong> 
                                        <span class="badge 
                                            <?php 
                                                if ($reservation['status'] === 'denied') {
                                                    echo 'bg-danger'; // Red background for denied
                                                } elseif ($reservation['status'] === 'approved') {
                                                    echo 'bg-success'; // Green background for approved
                                                } elseif ($reservation['status'] === 'pending') {
                                                    echo 'bg-warning text-dark'; // Yellow background with dark text for pending
                                                }
                                            ?>">
                                            <?php echo htmlspecialchars($reservation['status']); ?>
                                        </span><br>
                                        <?php if ($reservation['status'] === 'denied' && !empty($reservation['reason'])): ?>
                                            <strong>Admin Reason for Denial:</strong> <?php echo htmlspecialchars($reservation['reason']); ?><br>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>

                            <!-- Pagination Links -->
                            <nav aria-label="Reservation Pagination">
                                <ul class="pagination justify-content-center mt-3">
                                    <?php for ($page = 1; $page <= $total_pages; $page++): ?>
                                        <li class="page-item <?php echo $page === $current_page ? 'active' : ''; ?>">
                                            <a class="page-link" href="?page=<?php echo $page; ?>"><?php echo $page; ?></a>
                                        </li>
                                    <?php endfor; ?>
                                </ul>
                            </nav>
                        <?php else: ?>
                            <p>No reservations yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Messages Tab -->
<div class="tab-pane fade" id="messages" role="tabpanel" aria-labelledby="messages-tab">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Inbox</h5>
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
                            <div class="modal-dialog modal-dialog-centered"> <!-- Added modal-dialog-centered -->
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

            <!-- Pagination Links -->
            <nav aria-label="Messages Pagination">
                <ul class="pagination justify-content-center mt-3">
                    <?php for ($page = 1; $page <= $total_message_pages; $page++): ?>
                        <li class="page-item <?php echo $page === $current_message_page ? 'active' : ''; ?>">
                            <a class="page-link" href="?message_page=<?php echo $page; ?>"><?php echo $page; ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
    </div>
</div>

        </div>
    </div>

    <!-- Edit Info Modal -->
    <div class="modal fade" id="editInfoModal" tabindex="-1" role="dialog" aria-labelledby="editInfoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="editInfoForm" method="POST" action="update_info.php">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editInfoModalLabel">Editing <span id="modalVariableName"></span></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p><strong>Current:</strong> <span id="modalCurrentValue"></span></p>
                        <div class="form-group">
                            <label for="newValue">New:</label>
                            <input type="text" class="form-control d-none" id="newValue" name="new_value">
                            <input type="date" class="form-control d-none" id="dateInput" name="new_value">
                            <select class="form-control d-none" id="bloodTypeSelect" name="new_value">
                                <option value="">Select Blood Type</option>
                                <option value="A+">A+</option>
                                <option value="A-">A-</option>
                                <option value="B+">B+</option>
                                <option value="B-">B-</option>
                                <option value="AB+">AB+</option>
                                <option value="AB-">AB-</option>
                                <option value="O+">O+</option>
                                <option value="O-">O-</option>
                            </select>
                            <input type="hidden" id="variableName" name="variable_name">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Confirm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Send Message Modal -->
    <div class="modal fade" id="sendMessageModal" tabindex="-1" aria-labelledby="sendMessageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"> <!-- Added modal-dialog-centered -->
            <div class="modal-content">
                <form action="send_message.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="sendMessageModalLabel">Send New Message</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="receiver_id" class="form-label">Recipient</label>
                            <select name="receiver_id" id="receiver_id" class="form-control" required>
                                <option value="1">Admin</option>
                                <option value="8">Admin 2</option>
                                <option value="9">Kagawad</option>
                                <option value="10">Secretary</option>
                                <option value="11">Treasurer</option> <!-- Assuming admin ID is 1 -->
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
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Send</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <script src="js/jquery-3.4.1.min.js"></script>
    <script src="js/bootstrap.js"></script>

    <script>
document.addEventListener('DOMContentLoaded', function () {
    const editButtons = document.querySelectorAll('.edit-button');
    const modal = document.getElementById('editInfoModal');
    const modalVariableName = document.getElementById('modalVariableName');
    const modalCurrentValue = document.getElementById('modalCurrentValue');
    const variableNameInput = document.getElementById('variableName');
    const newValueInput = document.getElementById('newValue');
    const dateInput = document.getElementById('dateInput');
    const bloodTypeSelect = document.getElementById('bloodTypeSelect');
    const editInfoForm = document.getElementById('editInfoForm');

    editButtons.forEach(button => {
        button.addEventListener('click', function () {
            const variableName = this.dataset.variableName;
            const currentValue = this.dataset.currentValue;

            modalVariableName.textContent = variableName;
            modalCurrentValue.textContent = currentValue;
            variableNameInput.value = variableName;

            // Reset all input fields
            newValueInput.classList.add('d-none');
            newValueInput.disabled = true;
            newValueInput.removeAttribute('pattern');
            newValueInput.removeAttribute('maxlength');
            newValueInput.removeAttribute('title');
            dateInput.classList.add('d-none');
            dateInput.disabled = true;
            bloodTypeSelect.classList.add('d-none');
            bloodTypeSelect.disabled = true;

            // Show the appropriate input field based on the variable being edited
            if (variableName === 'Birthday') {
                dateInput.value = currentValue;
                dateInput.classList.remove('d-none');
                dateInput.disabled = false;
            } else if (variableName === 'Blood Type') {
                bloodTypeSelect.value = currentValue;
                bloodTypeSelect.classList.remove('d-none');
                bloodTypeSelect.disabled = false;
            } else {
                newValueInput.value = currentValue;
                newValueInput.classList.remove('d-none');
                newValueInput.disabled = false;

                // Add restrictions based on the variable being edited
                if (variableName === 'SSS Number') {
                    newValueInput.setAttribute('pattern', '\\d{14}');
                    newValueInput.setAttribute('maxlength', '14');
                    newValueInput.setAttribute('title', 'SSS Number must be 14 digits.');
                } else if (variableName === 'Pag-ibig Number') {
                    newValueInput.setAttribute('pattern', '\\d{12}');
                    newValueInput.setAttribute('maxlength', '12');
                    newValueInput.setAttribute('title', 'Pag-ibig Number must be 12 digits.');
                } else if (variableName === 'Contact Number') {
                    newValueInput.setAttribute('pattern', '\\d{11}');
                    newValueInput.setAttribute('maxlength', '11');
                    newValueInput.setAttribute('title', 'Contact Number must be 11 digits.');
                } else if (variableName === 'TIN Number') {
                    newValueInput.setAttribute('pattern', '\\d{12}');
                    newValueInput.setAttribute('maxlength', '12');
                    newValueInput.setAttribute('title', 'TIN Number must be 12 digits.');
                }
            }

            $('#editInfoModal').modal('show');
        });
    });

    // Ensure only the visible input field is disabled before submitting the form
    editInfoForm.addEventListener('submit', function () {
        if (!newValueInput.classList.contains('d-none')) {
            newValueInput.disabled = false;
        }
        if (!dateInput.classList.contains('d-none')) {
            dateInput.disabled = false;
        }
        if (!bloodTypeSelect.classList.contains('d-none')) {
            bloodTypeSelect.disabled = false;
        }
    });
});

document.addEventListener('DOMContentLoaded', function () {
        const filterStatus = document.getElementById('filterStatus');
        const reservationItems = document.querySelectorAll('.reservation-item');

        filterStatus.addEventListener('change', function () {
            const selectedStatus = this.value;

            reservationItems.forEach(item => {
                const itemStatus = item.getAttribute('data-status');

                if (selectedStatus === 'all' || itemStatus === selectedStatus) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });

document.addEventListener('DOMContentLoaded', function () {
    const profileTabs = document.querySelectorAll('.nav-link');
    const profileTabsContent = document.querySelectorAll('.tab-pane');

    // Restore the active tab from localStorage
    const activeTabId = localStorage.getItem('activeTab') || 'profile-tab';
    const activeTab = document.getElementById(activeTabId);
    const activeTabContent = document.querySelector(`#${activeTabId.replace('-tab', '')}`);

    if (activeTab) {
        profileTabs.forEach(tab => tab.classList.remove('active'));
        profileTabsContent.forEach(content => content.classList.remove('show', 'active'));

        activeTab.classList.add('active');
        activeTabContent.classList.add('show', 'active');
    }

    // Save the active tab to localStorage when clicked
    profileTabs.forEach(tab => {
        tab.addEventListener('click', function () {
            localStorage.setItem('activeTab', this.id);
        });
    });
});
</script>


</body>

</html>