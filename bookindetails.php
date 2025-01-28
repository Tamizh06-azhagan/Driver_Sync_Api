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

// Retrieve and validate the user ID from the POST request
$userid = isset($_POST['userid']) ? intval($_POST['userid']) : 0;

if ($userid <= 0) {
    echo json_encode(['status' => false, 'message' => 'Invalid user ID.']);
    exit();
}

// Query to fetch booking details for the user
$query = "
    SELECT 
        bd.id AS booking_id,
        bd.drivername,
        COALESCE(bd.status, 'pending') AS booking_status
    FROM 
        bookingdetails AS bd
    WHERE 
        bd.userid = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userid);
$stmt->execute();
$result = $stmt->get_result();

// Check if any bookings are available
if ($result->num_rows > 0) {
    $bookings = [];
    while ($row = $result->fetch_assoc()) {
        $bookings[] = [
            'booking_id' => $row['booking_id'],
            'drivername' => $row['drivername'],
            'status' => $row['booking_status']
        ];
    }

    echo json_encode([
        'status' => true,
        'message' => 'Bookings retrieved successfully.',
        'userid' => $userid,
        'bookings' => $bookings
    ]);
} else {
    echo json_encode([
        'status' => false,
        'message' => 'No bookings found for the user.',
        'userid' => $userid
    ]);
}

// Close statement and connection
$stmt->close();
$conn->close();
?>
