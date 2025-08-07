<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
require 'db.php';
require 'auto_archive.php'; // Include auto-archive functionality

date_default_timezone_set('Asia/Manila');

// Fetch active announcements
$stmt = $pdo->query("SELECT id, title, content, genre, created_at, active_until, occuring_at, thumbnail, allow_registrations FROM announcements WHERE status = 'active' ORDER BY created_at DESC");
$announcements = $stmt->fetchAll();

// Fetch registered events for this user
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT announcement_title FROM events WHERE user_id = ?");
$stmt->execute([$user_id]);
$registered_titles = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Fetch logged-in user's details
$stmt = $pdo->prepare("SELECT first_name, middle_name, last_name FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if user data exists
if (!$user) {
    $user = [
        'first_name' => 'Unknown',
        'middle_name' => '',
        'last_name' => 'User'
    ];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barangay Blue Ridge B - Announcements</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Custom styles -->
    <link href="css/style.css" rel="stylesheet">
    <link href="css/responsive.css" rel="stylesheet">
    <link href="announcement.css" rel="stylesheet">
</head>

<body class="sub_page">
    <div class="hero_area">
        <!-- Header Section -->
        <?php include 'header.php'; ?>
    </div>

    <div class="content-wrapper">
        <!-- Announcements Section -->
        <section class="announcement_section layout_padding">
            <div class="container">
                <div class="heading_container text-center">
                    <h2 class="announcement-heading">Announcements</h2>
                    <h2 class="announcement-description mb-5">Stay updated with the latest events and activities in Barangay Blue Ridge B</h2>
                </div>

                <!-- Success Message -->
                <?php if (isset($_GET['registered'])): ?>
                    <div class="alert alert-success">You have successfully registered for the event!</div>
                <?php endif; ?>

                <!-- Carousel -->
                <div id="announcementCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <?php foreach ($announcements as $index => $announcement): ?>
                            <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                                <div class="row align-items-start" style="height: 400px;">
                                    <!-- Left Side: Image -->
                                    <div class="col-md-6">
                                        <img src="<?php echo htmlspecialchars($announcement['thumbnail']); ?>" class="img-fluid rounded" alt="Announcement Thumbnail" style="height: 100%; width: 100%; object-fit: cover;">
                                    </div>

                                    <!-- Right Side: Information -->
                                    <div class="col-md-6">
                                        <div class="announcement-info">
                                            <h3 class="announcement-title"><?php echo htmlspecialchars($announcement['title']); ?></h3>
                                            <p class="navy-label"><i class="bi bi-tags"></i> Genre: <span class="label-value"><?php echo htmlspecialchars($announcement['genre']); ?></span></p>
                                            <p class="navy-label"><i class="bi bi-calendar-event"></i> Will be held: <span class="label-value"><?php echo htmlspecialchars($announcement['occuring_at']); ?></span></p>
                                            <p class="navy-label"><i class="bi bi-calendar-check"></i> Active Until: <span class="label-value"><?php echo htmlspecialchars($announcement['active_until']); ?></span></p>
                                            <p><?php echo htmlspecialchars($announcement['content']); ?></p>
                                            <?php if ($announcement['allow_registrations'] == 1): ?>
                                                <button class="btn btn-primary btn-participate" type="button" data-bs-toggle="modal" data-bs-target="#registerModal_<?php echo $announcement['id']; ?>">
                                                    Participate
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Custom Carousel Controls -->
                <div class="carousel-controls text-center mt-3">
                    <button class="btn btn-navy btn-sm me-2" id="prevButton">
                        <i class="bi bi-arrow-left"></i>
                    </button>
                    <button class="btn btn-navy btn-sm" id="nextButton">
                        <i class="bi bi-arrow-right"></i>
                    </button>
                </div>
            </div>
        </section>
    </div>

    <!-- Footer Section -->
    <?php include 'footer.php'; ?>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.getElementById('prevButton').addEventListener('click', function () {
        const carousel = document.querySelector('#announcementCarousel');
        const carouselInstance = bootstrap.Carousel.getInstance(carousel);
        carouselInstance.prev();
    });

    document.getElementById('nextButton').addEventListener('click', function () {
        const carousel = document.querySelector('#announcementCarousel');
        const carouselInstance = bootstrap.Carousel.getInstance(carousel);
        carouselInstance.next();
    });
</script>

<?php foreach ($announcements as $announcement): ?>
    <!-- Participate Modal -->
    <div class="modal fade" id="registerModal_<?php echo $announcement['id']; ?>" tabindex="-1" aria-labelledby="registerModalLabel_<?php echo $announcement['id']; ?>" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="register_event.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="registerModalLabel_<?php echo $announcement['id']; ?>">Participate in <?php echo htmlspecialchars($announcement['title']); ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Hidden Fields -->
                        <input type="hidden" name="announcement_title" value="<?php echo htmlspecialchars($announcement['title']); ?>">
                        <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">

                        <!-- Full Name -->
                        <div class="form-group mb-3">
                            <label for="full_name_<?php echo $announcement['id']; ?>">Full Name</label>
                            <input type="text" class="form-control" id="full_name_<?php echo $announcement['id']; ?>" name="full_name" value="<?php echo htmlspecialchars($user['last_name'] . ', ' . $user['first_name'] . ' ' . $user['middle_name']); ?>" readonly>
                        </div>

                        <!-- Reason -->
                        <div class="form-group mb-3">
                            <label for="reason_<?php echo $announcement['id']; ?>">Reason</label>
                            <select class="form-control" id="reason_<?php echo $announcement['id']; ?>" name="reason" required>
                                <option value="" disabled selected>Select your reason</option>
                                <option value="Volunteer">Volunteer</option>
                                <option value="Learn new skills">Learn new skills</option>
                                <option value="Community service">Community service</option>
                                <option value="Meet new people">Meet new people</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <!-- Note -->
                        <div class="form-group mb-3">
                            <label for="note_<?php echo $announcement['id']; ?>">Additional Note</label>
                            <textarea class="form-control" id="note_<?php echo $announcement['id']; ?>" name="note" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Register</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endforeach; ?>
</body>

</html>