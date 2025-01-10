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
$availability_date = isset($_POST['availability_date']) ? htmlspecialchars(strip_tags($_POST['availability_date'])) : '';

// Validate the date input
if (empty($availability_date)) {
    echo json_encode(['status' => false, 'message' => 'Availability date is required.']);
    exit();
}

// Query to fetch drivers available for the selected date
$query = "
    SELECT 
        d.id AS driver_id, 
        d.name AS driver_name, 
        d.image_path AS driver_image, 
        da.availability_date 
    FROM 
        drivers_availability AS da 
    INNER JOIN 
        drivers AS d 
    ON 
        da.driver_id = d.id 
    WHERE 
        da.availability_date = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $availability_date);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $drivers = [];
    while ($row = $result->fetch_assoc()) {
        $drivers[] = [
            'driver_id' => $row['driver_id'],
            'driver_name' => $row['driver_name'],
            'driver_image' => $row['driver_image'],
            'availability_date' => $row['availability_date']
        ];
    }

    echo json_encode([
        'status' => true,
        'message' => 'Drivers retrieved successfully.',
        'drivers' => $drivers
    ]);
} else {
    echo json_encode(['status' => false, 'message' => 'No drivers available on the selected date.']);
}

// Close statement and connection
$stmt->close();
$conn->close();
?>
