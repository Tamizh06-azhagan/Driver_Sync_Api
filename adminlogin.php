<?php
// Enable error reporting for debugging (remove in production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'db.php';

// Read POST data (login credentials)
$requestData = json_decode(file_get_contents('php://input'), true);

if (isset($requestData['username']) && isset($requestData['password'])) {
    $username = $requestData['username'];
    $password = $requestData['password'];

    // Sanitize input to prevent SQL injection
    $username = $conn->real_escape_string($username);

    // Prepare the SQL query to check if the user exists and the password matches
    $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password' LIMIT 1";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // User found and password matches
        $response = [
            'status' => true,
            'message' => 'Login successful.'
        ];
    } else {
        // Either username doesn't exist or password is incorrect
        $response = [
            'status' => false,
            'message' => 'Invalid username or password.'
        ];
    }
} else {
    // Missing username or password in the request
    $response = [
        'status' => false,
        'message' => 'Please provide both username and password.'
    ];
}

// Close the database connection
$conn->close();

// Set response header for JSON response
header('Content-Type: application/json');

// Output the response as JSON
echo json_encode($response);
?>