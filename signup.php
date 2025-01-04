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
if (!isset($data->name) || !isset($data->username) || !isset($data->email) || !isset($data->password)) {
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

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'status' => false, 
        'message' => 'Invalid email format'
    ]);
    exit();
}

// // Check password strength (simple rule: at least 8 characters, one number, one uppercase letter, one lowercase letter)
// if (!preg_match("/^(?=.[a-z])(?=.[A-Z])(?=.*\d).{8,}$/", $password)) {
//     echo json_encode([
//         'status' => false, 
//         'message' => 'Password must be at least 8 characters long, contain at least one number, one uppercase letter, and one lowercase letter'
//     ]);
//     exit();
// }

// Check if username or email already exists in the database
$query = "SELECT id FROM users WHERE username = ? OR email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $username, $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode([
        'status' => false, 
        'message' => 'Username or email already exists'
    ]);
    exit();
}

// Hash the password before storing it
// $hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insert the new user into the database
$query = "INSERT INTO users (name, username, email, password) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("ssss", $name, $username, $email, $password);

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