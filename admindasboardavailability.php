<?php
// Include database connection file
include_once 'db.php';

// Set response type to JSON
header('Content-Type: application/json');

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Read input data
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    // Get the availability date from the request
    $availability_date = isset($data['availability_date']) ? $data['availability_date'] : '';

    // Validate the availability date
    if (empty($availability_date)) {
        echo json_encode(['status' => false, 'message' => 'Availability date is required.']);
        exit();
    }

    // Query to fetch available drivers for the given date
    $query = "SELECT d.userid, s.name, d.contactnumber, d.age, d.experienceyears
              FROM admindashboard a
              JOIN driverinfo d ON a.userid = d.userid
              join signup s on d.userid = s.id
              WHERE a.availability = 'yes' AND a.availability_date = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $availability_date);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if results exist
    $drivers = [];
    while ($row = $result->fetch_assoc()) {
        $drivers[] = [
            'userid' => $row['userid'],
            'name' => $row['name'],
            'contact_number' => $row['contactnumber'],
            'age' => $row['age'],
            'experience_years' => $row['experienceyears'],
        ];
    }

    if (!empty($drivers)) {
        echo json_encode([
            'status' => true,
            'available_drivers' => $drivers,
            'message' => "List of drivers available on $availability_date."
        ]);
    } else {
        echo json_encode([
            'status' => false,
            'message' => "No drivers available on $availability_date."
        ]);
    }

    // Close statement
    $stmt->close();
} else {
    echo json_encode(['status' => false, 'message' => 'Invalid request method.']);
}

// Close the database connection
$conn->close();
?>
