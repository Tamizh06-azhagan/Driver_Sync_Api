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

// Retrieve data from POST request
$driver_id = isset($_POST['driver_id']) ? intval($_POST['driver_id']) : 0;
$availability_date = isset($_POST['availability_date']) ? htmlspecialchars(strip_tags($_POST['availability_date'])) : '';

// Validate inputs
if (empty($driver_id) || empty($availability_date)) {
    echo json_encode(['status' => false, 'message' => 'Driver ID and availability date are required.']);
    exit();
}

// Fetch existing availability for the driver and date
$query = "SELECT id FROM drivers_availability WHERE driver_id = ? AND availability_date = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("is", $driver_id, $availability_date);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // If the date already exists, return a message
    echo json_encode([
        'status' => true,
        'message' => 'You already selected this date. Update your availability if needed.',
        'availability_date' => $availability_date
    ]);
} else {
    // Insert the new date into the database
    $insert_query = "INSERT INTO drivers_availability (driver_id, availability_date) VALUES (?, ?)";
    $insert_stmt = $conn->prepare($insert_query);
    $insert_stmt->bind_param("is", $driver_id, $availability_date);

    if ($insert_stmt->execute()) {
        echo json_encode([
            'status' => true,
            'message' => 'Date selected successfully. Update your availability for this date.',
            'availability_date' => $availability_date
        ]);
    } else {
        echo json_encode(['status' => false, 'message' => 'Failed to save the selected date.']);
    }

    $insert_stmt->close();
}

// Close statement and connection
$stmt->close();
$conn->close();
?>
