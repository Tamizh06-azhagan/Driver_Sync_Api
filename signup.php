<?php
// Include the database connection file
include_once 'db.php';

// Set the header to return JSON response
header('Content-Type: application/json');

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    echo json_encode([
        'status' => false, 
        'message' => 'Invalid request method. Please use POST.'
    ]);
    exit();
}

// Get the JSON data from the POST request
$data = json_decode(file_get_contents("php://input"));

// Validate if all necessary fields are provided
if (!isset($data->name) || !isset($data->username) || !isset($data->email) || !isset($data->password) || !isset($data->role)) {
    echo json_encode([
        'status' => false, 
        'message' => 'Missing required fields'
    ]);
    exit();
}

// Sanitize user input to avoid malicious data
$name = htmlspecialchars(strip_tags($data->name));
$username = htmlspecialchars(strip_tags($data->username));
$email = htmlspecialchars(strip_tags($data->email));
$password = htmlspecialchars(strip_tags($data->password));
$role = htmlspecialchars(strip_tags($data->role));

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'status' => false, 
        'message' => 'Invalid email format'
    ]);
    exit();
}

// Optionally validate the role (e.g., allow only specific roles like 'admin', 'user')
$allowed_roles = ['User','Driver']; // Define allowed roles
if (!in_array($role, $allowed_roles)) {
    echo json_encode([
        'status' => false, 
        'message' => 'Invalid role. Allowed roles are admin and user.'
    ]);
    exit();
}

// Check if username or email already exists in the database
$query = "SELECT id FROM signup WHERE username = ? OR email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $username, $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode([
        'status' => false, 
        'message' => 'Username or email already exists'
    ]);
    $stmt->close();
    $conn->close();
    exit();
}

// Insert the new user into the database
$query = "INSERT INTO signup (`name`, `username`, `email`, `password`, `role`) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("sssss", $name, $username, $email, $password, $role);

if ($stmt->execute()) {
    echo json_encode([
        'status' => true, 
        'message' => 'User registered successfully'
    ]);
} else {
    echo json_encode([
        'status' => false, 
        'message' => 'Error registering user'
    ]);
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
