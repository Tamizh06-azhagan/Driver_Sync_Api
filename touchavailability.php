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

// Retrieve and validate the availability_date from the POST request
$availability_date = isset($_POST['availability_date']) ? htmlspecialchars(strip_tags($_POST['availability_date'])) : '';

if (empty($availability_date)) {
    echo json_encode(['status' => false, 'message' => 'Availability date is required.']);
    exit();
}

// Query to fetch drivers available for the selected date
$query = "
    SELECT 
        ad.id AS availability_id,
        s.id AS driver_id,
        s.name AS driver_name,
        s.email AS driver_email,
        di.contact_number AS driver_contact,
        di.vehicle_details AS driver_vehicle,
        ad.availability AS availability_status,
        ad.availability_date
    FROM 
        admindashboard AS ad
    INNER JOIN 
        signup AS s ON ad.userid = s.id
    INNER JOIN 
        driverinfo AS di ON s.id = di.driver_id
    WHERE 
        ad.availability_date = ?
        AND ad.availability = 'yes'
";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $availability_date);
$stmt->execute();
$result = $stmt->get_result();

// Check if any drivers are available
if ($result->num_rows > 0) {
    $drivers = [];
    while ($row = $result->fetch_assoc()) {
        $drivers[] = [
            'availability_id' => $row['availability_id'],
            'driver_id' => $row['driver_id'],
            'driver_name' => $row['driver_name'],
            'driver_email' => $row['driver_email'],
            'driver_contact' => $row['driver_contact'],
            'driver_vehicle' => $row['driver_vehicle'],
            'availability_status' => $row['availability_status'],
            'availability_date' => $row['availability_date']
        ];
    }

    echo json_encode([
        'status' => true,
        'message' => 'Drivers retrieved successfully.',
        'date' => $availability_date,
        'total_drivers' => count($drivers),
        'drivers' => $drivers
    ]);
} else {
    echo json_encode([
        'status' => false,
        'message' => 'No drivers available on the selected date.',
        'date' => $availability_date
    ]);
}

// Close statement and connection
$stmt->close();
$conn->close();
?>
