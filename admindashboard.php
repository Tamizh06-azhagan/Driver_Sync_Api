<?php
// Include database connection
include_once 'db.php';

// Set response type to JSON
header('Content-Type: application/json');

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    echo json_encode(['status' => false, 'message' => 'Invalid request method. Use POST.']);
    exit();
}

// Check if the userid and image are provided
if (!isset($_POST['userid']) || !isset($_FILES['image'])) {
    echo json_encode(['status' => false, 'message' => 'Driver userid and image are required.']);
    exit();
}

// Get userid from POST request
$userid = intval($_POST['userid']);

// Fetch driver name from the signup table
$query = "SELECT name FROM signup WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userid);
$stmt->execute();
$result = $stmt->get_result();

// Check if a driver with the given userid exists
if ($result->num_rows === 0) {
    echo json_encode(['status' => false, 'message' => 'Driver not found in the signup table.']);
    exit();
}

// Fetch the driver's name
$row = $result->fetch_assoc();
$driver_name = $row['name'];

// Handle file upload
$target_dir = "uploads/"; // Directory to save uploaded images
if (!is_dir($target_dir)) {
    mkdir($target_dir, 0777, true); // Create the directory if it doesn't exist
}

$image_name = basename($_FILES['image']['name']);
$image_path = $target_dir . uniqid() . "_" . $image_name; // Add unique ID to prevent overwriting

// Move the uploaded file to the target directory
if (!move_uploaded_file($_FILES['image']['tmp_name'], $image_path)) {
    echo json_encode(['status' => false, 'message' => 'Failed to upload the image.']);
    exit();
}

// Insert driver name and image path into the driverinfo table
$query = "INSERT INTO driverinfo (name, image_path) VALUES (?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $driver_name, $image_path);

if ($stmt->execute()) {
    echo json_encode(['status' => true, 'message' => 'Driver info added successfully.']);
} else {
    echo json_encode(['status' => false, 'message' => 'Failed to add driver info.']);
}

// Close statement and database connection
$stmt->close();
$conn->close();
?>
