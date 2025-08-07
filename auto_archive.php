<?php
require 'db.php';

// Update announcements where active_until has passed and status is still 'active'
$stmt = $pdo->prepare("UPDATE announcements SET status = 'archived' WHERE active_until < CURDATE() AND status = 'active'");
$stmt->execute();

?>