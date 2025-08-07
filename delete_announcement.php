<!-- filepath: d:\XAMPP\htdocs\Barangay_system\delete_announcement.php -->
<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];

    // Delete the announcement
    $stmt = $pdo->prepare("DELETE FROM announcements WHERE id = ?");
    $stmt->execute([$id]);

    header('Location: admin_dashboard.php?status=archived');
    exit;
}
?>