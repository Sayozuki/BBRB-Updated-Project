<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $variableName = $_POST['variable_name'];
    $newValue = $_POST['new_value'];
    $userId = $_SESSION['user_id'];

    // Map variable names to database columns
    $columns = [
        'First Name' => 'first_name',
        'Middle Name' => 'middle_name',
        'Last Name' => 'last_name',
        'Email' => 'email',
        'Address' => 'address',
        'City' => 'city',
        'ZIP Code' => 'zip_code',
        'Birthday' => 'birthday',
        'Blood Type' => 'blood_type',
        'Contact Number' => 'contact_number',
        'SSS Number' => 'sss_number',
        'Pag-ibig Number' => 'pagibig_number',
        'TIN Number' => 'tin_number'
    ];

    if (isset($columns[$variableName])) {
        $column = $columns[$variableName];

        // Update the database
        $stmt = $pdo->prepare("UPDATE users SET $column = ? WHERE id = ?");
        $stmt->execute([$newValue, $userId]);
    }

    header('Location: profile.php');
    exit;
}
?>