<?php
session_start();
require 'db.php';

if (!isset($_SESSION['admin_username'])) {
    header('Location: admin_login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $genre = $_POST['genre'];
    $active_until = $_POST['active_until'];
    $occuring_at = $_POST['occuring_at'];
    $allow_registrations = $_POST['allow_registrations'];
    $author = $_SESSION['admin_username'];
    $thumbnail = 'images/image2_BRB.jpg'; // Default image path

    // Handle file upload
    if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        $thumbnail = $uploadDir . basename($_FILES['thumbnail']['name']);
        move_uploaded_file($_FILES['thumbnail']['tmp_name'], $thumbnail);
    }

    // Insert into database
    $stmt = $pdo->prepare("INSERT INTO announcements (title, content, genre, active_until, occuring_at, thumbnail, author, allow_registrations, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'active', NOW())");
    $stmt->execute([$title, $content, $genre, $active_until, $occuring_at, $thumbnail, $author, $allow_registrations]);

    // Redirect back to the dashboard
    header('Location: admin_dashboard.php');
    exit;
}
?>