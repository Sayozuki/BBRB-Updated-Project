<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require 'db.php';

$error = '';
$success = '';

// Allow both admin and resident to send messages
if (!isset($_SESSION['admin_id']) && !isset($_SESSION['user_id'])) {
    $error = 'Access denied. Please log in as admin or resident.';
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Determine sender
    $sender_id = $_SESSION['admin_id'] ?? $_SESSION['user_id'];
    $sender_type = isset($_SESSION['admin_id']) ? 'admin' : 'user';

    // Validate form fields
    $receiver_id = trim($_POST['receiver_id'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $source = $_POST['source'] ?? 'profile'; // Default to 'profile' if no source is provided

    if (empty($receiver_id) || empty($subject) || empty($message)) {
        $error = 'All fields are required.';
    } elseif (!ctype_digit($receiver_id)) {
        $error = 'Recipient User ID must be a number.';
    } elseif ($receiver_id == $sender_id) {
        $error = 'You cannot send a message to yourself.';
    } else {
        // Insert message into database
        $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, subject, message, sent_at) VALUES (?, ?, ?, ?, NOW())");
        if ($stmt->execute([$sender_id, $receiver_id, $subject, $message])) {
            $success = "Message sent successfully!";
        } else {
            $error = "Failed to send message.";
        }
    }

    // If this is an AJAX request, return JSON
    if (
        isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
    ) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => $success,
            'error' => $error
        ]);
        exit;
    }

    // For non-AJAX POST requests, redirect based on the source
    if ($success) {
        if ($source === 'messages') {
            header('Location: messages.php');
        } elseif ($source === 'messaging') {
            header('Location: messaging.php');
        } else {
            header('Location: profile.php?tab=messages');
        }
    } else {
        header('Location: profile.php?tab=messages&error=' . urlencode($error));
    }
    exit;
} else {
    echo json_encode(['error' => 'Invalid request.']);
}
?>