<?php
// Include the database connection file
include_once 'db.php';

// Set the header to return JSON response
header('Content-Type: application/json');

// Check if the request method is GET
if ($_SERVER['REQUEST_METHOD'] != 'GET') {
    echo json_encode([
        'status' => false,
        'message' => 'Invalid request method. Please use GET.'
    ]);
    exit();
}

try {
    // Query to fetch all car records
    $query = "SELECT * FROM cars";
    $result = $conn->query($query);

    // Check if records exist
    if ($result->num_rows > 0) {
        $cars = [];

        // Fetch all records
        while ($row = $result->fetch_assoc()) {
            $cars[] = [
                'id' => $row['id'],
                'userid' => $row['userid'],
                'car_name' => $row['car_name'],
                'image_path' => $row['image_path'],
                'condition' => $row['condition']
            ];
        }

        // Return records as JSON
        echo json_encode([
            'status' => true,
            'message' => 'Cars fetched successfully.',
            'data' => $cars
        ]);
    } else {
        // No records found
        echo json_encode([
            'status' => false,
            'message' => 'No cars found.'
        ]);
    }
} catch (Exception $e) {
    // Handle any errors
    echo json_encode([
        'status' => false,
        'message' => 'An error occurred: ' . $e->getMessage()
    ]);
}

// Close the database connection
$conn->close();
?>
