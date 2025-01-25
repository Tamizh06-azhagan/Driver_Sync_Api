<?php
header('Content-Type: application/json');
include('db.php'); // Include the database connection

// Check if car_id is provided in the GET request
if (!isset($_GET['car_id'])) {
    echo json_encode([
        'status' => false,
        'message' => 'Car ID is required.'
    ]);
    exit;
}

$car_id = $_GET['car_id'];

// SQL query to fetch car details along with driver info (from signup and driverinfo tables)
$sql = "
    SELECT 
        cars.id AS car_id,
        cars.car_name,
        cars.image_path,
        cars.condition,
        signup.name AS driver_name,
        driverinfo.age,
        driverinfo.experienceyears,
        driverinfo.contactnumber
    FROM cars
    JOIN signup ON cars.userid = signup.id
    LEFT JOIN driverinfo ON signup.id = driverinfo.userid
    WHERE cars.id = ?
";

// Prepare the statement
$stmt = $conn->prepare($sql);

// Bind the car_id to the prepared statement
$stmt->bind_param("i", $car_id);

// Execute the query
$stmt->execute();

// Get the result
$result = $stmt->get_result();

// Check if the car exists
if ($result->num_rows > 0) {
    // Fetch the car and driver details as an associative array
    $car = $result->fetch_assoc();
    
    // Return the car and driver details as a JSON response with status as true
    echo json_encode([
        'status' => true,
        'data' => $car
    ]);
} else {
    // Return an error message with status as false
    echo json_encode([
        'status' => false,
        'message' => 'Car not found.'
    ]);
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
