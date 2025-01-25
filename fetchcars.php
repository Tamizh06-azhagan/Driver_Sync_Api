<?php
header('Content-Type: application/json');
include('db.php'); // Include the database connection

// SQL query to fetch all cars
$sql = "SELECT * FROM cars";

// Execute the query
$result = $conn->query($sql);

// Check if the query was successful
if ($result->num_rows > 0) {
    // Fetch all results as an associative array
    $cars = $result->fetch_all(MYSQLI_ASSOC);
    
    // Return the results as a JSON response
    echo json_encode([
        'status' => 'success',
        'data' => $cars
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'No cars found.'
    ]);
}

// Close the connection
$conn->close();
?>
