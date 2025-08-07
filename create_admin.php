<?php
require 'db.php';

$username = 'admin';
$password = password_hash('admin123', PASSWORD_BCRYPT); 
$role = 'admin';

$username = 'admin2';
$password = password_hash('admin123', PASSWORD_BCRYPT); 
$role = 'admin2';

$username = 'kagawad';
$password = password_hash('kagawad123', PASSWORD_BCRYPT); 
$role = 'kagawad';

$username = 'secretary';
$password = password_hash('secretary123', PASSWORD_BCRYPT); 
$role = 'secretary';

$username = 'treasurer';
$password = password_hash('treasurer123', PASSWORD_BCRYPT); 
$role = 'treasurer';


$stmt = $pdo->prepare("INSERT INTO admin_accounts (username, password, role) VALUES (?, ?, ?)");
$stmt->execute([$username, $password, $role]);

echo "Admin account created successfully!";