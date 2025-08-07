<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['status'])) {
    $id = $_POST['id'];
    $status = $_POST['status'];

    // Update the reservation status
    $stmt = $pdo->prepare("UPDATE reservations SET status = ? WHERE id = ?");
    $stmt->execute([$status, $id]);

    // Include the reason for denial if provided
    if ($status === 'denied' && isset($_POST['reason'])) {
        $reason = $_POST['reason'];
        $stmt = $pdo->prepare("UPDATE reservations SET reason = ? WHERE id = ?");
        $stmt->execute([$reason, $id]);
    }

    // Archive the reservation after approval or denial
    if (in_array($status, ['approved', 'denied'])) {
        $stmt = $pdo->prepare("INSERT INTO archived_reservations (reservation_id, user_id, type, details, start_date, end_date, price_estimate, status, reason, created_at) 
                                SELECT id, user_id, type, details, start_date, end_date, price_estimate, status, reason, created_at 
                                FROM reservations WHERE id = ?");
        $stmt->execute([$id]);

        // Delete the reservation from the active table
        $stmt = $pdo->prepare("DELETE FROM reservations WHERE id = ?");
        $stmt->execute([$id]);
    }

    // Redirect back to the admin dashboard with a success message
    header('Location: admin_dashboard.php?section=reservations&msg=Reservation updated successfully');
    exit;
}
?>