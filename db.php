<?php
// Database configuration
$host = 'localhost';
$dbname = 'driversync';
$username = 'root';
$password = '';

// Create connection
$conn = mysqli_connect($host, $username, $password, $dbname);

// Check connection
if (!$conn) {
    // Send JSON error response
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'error',
        'message' => 'Database connection failed: ' . mysqli_connect_error()
    ]);
    exit;
}
?>