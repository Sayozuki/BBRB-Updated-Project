<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];

    // Update the status to 'archived'
    $stmt = $pdo->prepare("UPDATE announcements SET status = 'archived' WHERE id = ?");
    $stmt->execute([$id]);

    header('Location: admin_dashboard.php?status=active');
    exit;
}
?>