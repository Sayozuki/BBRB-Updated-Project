<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $content = $_POST['content'];
    $genre = $_POST['genre'];
    $active_until = $_POST['active_until'];
    $occuring_at = $_POST['occuring_at'];
    $allow_registrations = $_POST['allow_registrations'];

    // Default thumbnail path
    $thumbnail = 'images/image2_BRB.jpg';

    // Handle thumbnail upload if provided
    if (!empty($_FILES['thumbnail']['name'])) {
        $thumbnail = $_FILES['thumbnail']['name'];
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($thumbnail);
        move_uploaded_file($_FILES['thumbnail']['tmp_name'], $target_file);

        $stmt = $pdo->prepare("UPDATE announcements SET title=?, content=?, genre=?, active_until=?, occuring_at=?, thumbnail=?, allow_registrations=? WHERE id=?");
        $stmt->execute([$title, $content, $genre, $active_until, $occuring_at, $thumbnail, $allow_registrations, $id]);
    } else {
        $stmt = $pdo->prepare("UPDATE announcements SET title=?, content=?, genre=?, active_until=?, occuring_at=?, allow_registrations=? WHERE id=?");
        $stmt->execute([$title, $content, $genre, $active_until, $occuring_at, $allow_registrations, $id]);
    }

    header('Location: admin_dashboard.php');
    exit;
}
?>
<div class="form-group">
    <label for="event_end_date">Event End Date</label>
    <input type="date" name="event_end_date" id="event_end_date" class="form-control" required>
</div>