<?php
// Include database connection file
include_once 'db.php';

// Set response type to JSON
header('Content-Type: application/json');

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    echo json_encode(['status' => false, 'message' => 'Invalid request method. Use POST.']);
    exit();
}

// Retrieve data from POST request
$origin = isset($_POST['origin']) ? htmlspecialchars(strip_tags($_POST['origin'])) : '';
$destination = isset($_POST['destination']) ? htmlspecialchars(strip_tags($_POST['destination'])) : '';
$days = isset($_POST['days']) ? intval($_POST['days']) : 0;

// Validate inputs
if (empty($origin) || empty($destination) || $days <= 0) {
    echo json_encode(['status' => false, 'message' => 'All fields are required and days must be greater than 0.']);
    exit();
}

// Example: Fetch price details from the database (assume a `price_details` table exists)
$query = "SELECT price_per_day FROM pricepage WHERE origin = ? AND destination = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $origin, $destination);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $price_per_day = $row['price_per_day'];
    $total_amount = $price_per_day * $days;

    echo json_encode([
        'status' => true,
        'message' => 'Price calculated successfully.',
        'origin' => $origin,
        'destination' => $destination,
        'days' => $days,
        'price_per_day' => $price_per_day,
        'total_amount' => $total_amount
    ]);
} else {
    echo json_encode(['status' => false, 'message' => 'No price details found for the selected route.']);
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
