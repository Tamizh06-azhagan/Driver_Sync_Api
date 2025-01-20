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

// Create the uploads directory if it doesn't exist
$upload_dir = 'uploads/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true); // Create the directory with write permissions
}

// Validate required fields
if (!isset($_POST['name']) || !isset($_POST['username']) || !isset($_POST['email']) ||
    !isset($_POST['password']) || !isset($_POST['role']) || !isset($_POST['contact_number']) || 
    !isset($_FILES['image'])) {
    echo json_encode([
        'status' => false,
        'message' => 'Missing required fields'
    ]);
    exit();
}

// Sanitize user input to avoid malicious data
$name = htmlspecialchars(strip_tags($_POST['name']));
$username = htmlspecialchars(strip_tags($_POST['username']));
$email = htmlspecialchars(strip_tags($_POST['email']));
$password = htmlspecialchars(strip_tags($_POST['password']));
$role = htmlspecialchars(strip_tags($_POST['role']));
$contact_number = htmlspecialchars(strip_tags($_POST['contact_number']));

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'status' => false,
        'message' => 'Invalid email format'
    ]);
    exit();
}

// Validate the contact number (10 to 15 digits)
if (!preg_match('/^\d{10,15}$/', $contact_number)) {
    echo json_encode([
        'status' => false,
        'message' => 'Invalid contact number. It should be 10 to 15 digits.'
    ]);
    exit();
}

// Optionally validate the role (e.g., allow only specific roles like 'User', 'Driver')
$allowed_roles = ['User', 'Driver'];
if (!in_array($role, $allowed_roles)) {
    echo json_encode([
        'status' => false,
        'message' => 'Invalid role. Allowed roles are User and Driver.'
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

// Handle the image upload
$image = $_FILES['image'];
$image_name = basename($image['name']);
$image_path = $upload_dir . $image_name;

// Check for errors during file upload
if ($image['error'] != UPLOAD_ERR_OK) {
    echo json_encode([
        'status' => false,
        'message' => 'Error uploading image'
    ]);
    exit();
}

// Move the uploaded image to the 'uploads' directory
if (!move_uploaded_file($image['tmp_name'], $image_path)) {
    echo json_encode([
        'status' => false,
        'message' => 'Failed to move uploaded image.'
    ]);
    exit();
}

// Insert the new user into the database
$query = "INSERT INTO signup (`name`, `username`, `email`, `password`, `role`, `contact_number`, `image_path`) VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("sssssss", $name, $username, $email, $password, $role, $contact_number, $image_path);

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
