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

// Retrieve and validate the driver ID from the POST request
$driver_id = isset($_POST['driver_id']) ? intval($_POST['driver_id']) : 0;

if ($driver_id <= 0) {
    echo json_encode(['status' => false, 'message' => 'Invalid driver ID.']);
    exit();
}

// Query to fetch the driver's name and username from the signup table
$query = "
    SELECT 
        name, username
    FROM 
        signup
    WHERE 
        id = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $driver_id);

// Execute the query and check for errors
if (!$stmt->execute()) {
    echo json_encode(['status' => false, 'message' => 'Database query failed.']);
    exit();
}

$result = $stmt->get_result();

// Check if a driver with the given ID exists
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode([
        'status' => true,
        'message' => 'Driver details retrieved successfully.',
        'driver' => [
            'name' => $row['name'],
            'username' => $row['username']
        ]
    ]);
} else {
    echo json_encode([
        'status' => false,
        'message' => 'No driver found with the given ID.',
        'driver' => []
    ]);
}

// Close statement and connection
$stmt->close();
$conn->close();
?>
