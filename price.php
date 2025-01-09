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

// Function to fetch price per day
function getPricePerDay($conn, $origin, $destination) {
    $query = "SELECT price_per_day FROM pricepage WHERE origin = ? AND destination = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $origin, $destination);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['price_per_day'];
    }

    return null;
}

// Try fetching price for the provided route
$price_per_day = getPricePerDay($conn, $origin, $destination);

if ($price_per_day === null) {
    // Swap origin and destination
    $temp = $origin;
    $origin = $destination;
    $destination = $temp;

    // Try fetching price for the swapped route
    $price_per_day = getPricePerDay($conn, $origin, $destination);

    if ($price_per_day === null) {
        echo json_encode(['status' => false, 'message' => 'No price details found for the selected route or its reverse.']);
        exit();
    }
}

// Calculate the total amount for the route (original or swapped)
$total_amount = $price_per_day * $days;

// Return the response
echo json_encode([
    'status' => true,
    'message' => 'Price calculated successfully.',
    'origin' => $origin,
    'destination' => $destination,
    'days' => $days,
    'price_per_day' => $price_per_day,
    'total_amount' => $total_amount
]);

// Close the connection
$conn->close();
?>
