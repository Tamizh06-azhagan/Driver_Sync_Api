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

// Query to fetch booking details and driver's name for the user with status check
$query = "
    SELECT 
        bd.id AS booking_id,
        bd.dateofbooking as date,
        COALESCE(bd.status, 'Pending') AS booking_status,
        s.name
    FROM 
        bookingdetails AS bd
    JOIN 
        signup AS s ON bd.driver_id = s.id
    WHERE 
        bd.userid = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userid);

// Execute the query and check for errors
if (!$stmt->execute()) {
    echo json_encode(['status' => false, 'message' => 'Database query failed.']);
    exit();
}

$result = $stmt->get_result();

// Check if any bookings are available
if ($result->num_rows > 0) {
    $bookings = [];
    while ($row = $result->fetch_assoc()) {
        // Ensure the status is one of the expected values: Accepted, Pending, Rejected
        $status = $row['booking_status'];

        // Update status if needed (for example, if there is a possibility of the value being stored differently in the database)
        if (!in_array($status, ['accepted', 'pending', 'rejected'])) {
            $status = 'pending'; // Default to Pending if the status is unrecognized
        }

        $bookings[] = [
            'booking_id' => $row['booking_id'],
            'date' => $row['date'],
            'drivername' => $row['name'],  // Added the driver's name from the signup table
            'status' => $status
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
        'userid' => $userid,
        'bookings' => []
    ]);
}

// Close statement and connection
$stmt->close();
$conn->close();
?>
