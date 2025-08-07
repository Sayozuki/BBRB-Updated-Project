<?php
require 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $announcement_title = $_POST['announcement_title'];
    $full_name = $_POST['full_name'];
    $reason = $_POST['reason'];
    $note = $_POST['note'];

    // Insert into events table
    $stmt = $pdo->prepare("INSERT INTO events (user_id, announcement_title, registered_at, full_name, reason, note) VALUES (?, ?, NOW(), ?, ?, ?)");
    $stmt->execute([$user_id, $announcement_title, $full_name, $reason, $note]);

    // Redirect back to announcements page with success message
    header('Location: announcement.php?registered=true');
    exit;
}
?>